<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

/**
 * @subpackage Controllers
 * @package Modules
 */

class simplePollController extends expController {
	public $basemodel_name = 'simplepoll_question';
	public $useractions = array(
        'showall'=>'Show Poll Question',
        'showRandom'=>'Show Random Question',
	);
	public $remove_configs = array(
        'aggregation',
        'categories',
		'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',
		'rss',
		'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)
//    public $codequality = 'beta';

    static function displayname() { return gt("Simple Poll"); }
    static function description() { return gt("A simple poll that asks a visitor one question with multiple answers.  Can manage multiple questions, though it only displays one."); }
//	function isSearchable() { return true; }

    public function __construct($src=null, $params=array()) {
        parent::__construct($src, $params);
        $this->simplepoll_timeblock = new simplepoll_timeblock();
    }

	public function showall() {
        expHistory::set('viewable', $this->params);
        $where = $this->aggregateWhereClause();
        $where .= " AND active = 1";
        $question = $this->simplepoll_question->find('first', $where);
        if (empty($question)) $question = $this->simplepoll_question->find('first', $this->aggregateWhereClause());
        assign_to_template(array(
            'question'=>$question,
        ));
	}

    public function showRandom() {
   	    expHistory::set('viewable', $this->params);
        $question = $this->simplepoll_question->find('first', $this->aggregateWhereClause(), 'RAND()');
   		assign_to_template(array(
           'question'=>$question,
       ));
   	}

    public function manage_questions() {
        global $router;

        expHistory::set('manageable', $router->params);
        $where = $this->aggregateWhereClause();
        $questions = $this->simplepoll_question->find('all', $where);
        assign_to_template(array(
            'questions'=>$questions,
        ));
    }

    public function manage_question() {
        global $router;

        $question = null;
        if (isset($this->params['id'])) {
            $question = $this->simplepoll_question->find('first','id='.$this->params['id']);
        }

        if ($question) {
            expHistory::set('manageable', $router->params);
            assign_to_template(array(
                'question'=>$question,
            ));
        }
    }

    public function delete() {
        // if no active question, set first question as active
        $question = $this->simplepoll_question->find('first', 'id='.$this->params['id']);
        parent::delete();
        $question = $this->simplepoll_question->find('first', "location_data='".$question->location_data."' AND active = 1");
        if (empty($question)) {
            $question = $this->simplepoll_question->find('first', "location_data='".$question->location_data."'");
            $question->update(array('active'=>1));
        }
    }

    public function edit_answer() {
        $id = !empty($this->params['id']) ? $this->params['id'] : null;
        $answer = new simplepoll_answer($id);
        if (empty($answer->simplepoll_question->id) && !empty($this->params['question_id'])) {
            $answer->simplepoll_question = $this->simplepoll_question->find('first', 'id='.$this->params['question_id']);
        }
        assign_to_template(array(
            'answer'=>$answer,
       ));
    }

    public function update_answer() {
        $answer = new simplepoll_answer($this->params);
        $answer->update();
	    expHistory::returnTo('manageable');
    }

    public function delete_answer() {
        if (isset($this->params['id'])) {
            $answer = new simplepoll_answer($this->params['id']);
            $answer->delete();
        }
        expHistory::back();
    }

    public function activate() {
        $this->simplepoll_question->toggle();
        $active = $this->simplepoll_question->find('first',"id=".$this->params['id']);
        $active->update(array('active'=>1));
	    expHistory::returnTo('manageable');
    }

    public function vote() {
        global $user;

        if (isset($this->params['choice'])) {
            $answer = new simplepoll_answer(intval($this->params['choice']));
            if (empty($this->config)) {
                $this->config['anonymous_timeout'] = 5*3600;
                $this->config['thank_you_message'] = 'Thank you for voting.';
                $this->config['already_voted_message'] = 'You have already voted in this poll.';
                $this->config['voting_closed_message'] = 'Voting has been closed for this poll.';
            }

            // Check to see if voting is even allowed:
            if ($answer->simplepoll_question->open_voting) {
                // Time blocking
//                $timeblock = null;
                if (is_object($user) && $user->id > 0) {
                    $timeblock = $this->simplepoll_timeblock->find('first','user_id='.$user->id.' AND simplepoll_question_id='.$answer->simplepoll_question_id);
//                    $timeblock = $db->selectObject('simplepoll_timeblock','user_id='.$user->id.' AND simplepoll_question_id='.$answer->simplepoll_question_id);
                } else {
                    $timeblock = $this->simplepoll_timeblock->find('first',"ip_hash='".md5($_SERVER['REMOTE_ADDR'])."' AND simplepoll_question_id=".$answer->simplepoll_question_id);
//                    $timeblock = $db->selectObject('simplepoll_timeblock',"ip_hash='".md5($_SERVER['REMOTE_ADDR'])."' AND simplepoll_question_id=".$answer->simplepoll_question_id);
                }

                if ($timeblock == null || ($timeblock->lock_expires < time() && $timeblock->lock_expires != 0)) {
                    if ($timeblock == null)
                        $timeblock = new simplepoll_timeblock();
                    $answer->vote_count++;
                    $answer->update();

                    // Update the timeblock
                    $timeblock->simplepoll_question_id = $answer->simplepoll_question_id;
                    if (is_object($user) && $user->id > 0) {
                        $timeblock->lock_expires = 0;
                        $timeblock->user_id = $user->id;
                        $timeblock->ip_hash = '';
                    } else {
                        $timeblock->lock_expires = time()+($this->config['anonymous_timeout']*3600);
                        $timeblock->user_id = 0;
                        $timeblock->ip_hash = md5($_SERVER['REMOTE_ADDR']);
                    }

//                    if (isset($timeblock->id)) {
//                        $db->updateObject($timeblock,'simplepoll_timeblock');
//                    } else {
//                        $db->insertObject($timeblock,'simplepoll_timeblock');
//                    }
                    $timeblock->update();

                    flash('message', $this->config['thank_you_message']);
                    if ($answer->simplepoll_question->open_results) {
                        redirect_to(array('controller'=>'simplePoll', 'action'=>'results','id'=>$answer->simplepoll_question_id));
                    } else {
                        expHistory::back();
                    }
                } else {
                    flash('error', $this->config['already_voted_message']);
                    expHistory::back();
                }
            } else {
                flash('error', $this->config['voting_closed_message']);
                expHistory::back();
            }
        } else {
            flash('error', gt('You must select an answer to vote'));
   	        expHistory::back();
        }
    }

    public function results() {
        if (isset($this->params['id'])) {
            $question = $this->simplepoll_question->find('first', 'id='.$this->params['id']);
        }
        if (!empty($question) && $question->open_results) {
            $total = 0;
            foreach ($question->simplepoll_answer as $answer) {
                $total += $answer->vote_count;
            }
            assign_to_template(array(
               'question'=>$question,
               'vote_total'=>$total,
           ));
        }
    }

}

?>