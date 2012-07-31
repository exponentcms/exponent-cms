<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
        'showall'=>'Show poll question',
	);
	public $remove_configs = array(
        'aggregation',
        'categories',
		'comments',
        'ealerts',
        'files',
        'pagination',
		'rss',
		'tags'
	); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')
    public $codequality = 'alpha';

	function displayname() { return gt("Simple Poll 2"); }
	function description() { return gt("A simple poll that asks a visitor one question with mutiple answers.  Can manage multiple questions, though it only displays one."); }
//	function isSearchable() { return true; }  // this content is pulled by the navigation module since we don't display individual text items
	
	public function showall() {
        expHistory::set('viewable', $this->params);
        $where = $this->aggregateWhereClause();
        $where .= " AND active = 1";
        $question = $this->simplepoll_question->find('first', $where);
        $question->simplepoll_answer = expSorter::sort(array('array'=>$question->simplepoll_answer,'sortby'=>'rank', 'order'=>'ASC', 'type'=>'a'));
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
    		$question->simplepoll_answer = expSorter::sort(array('array'=>$question->simplepoll_answer,'sortby'=>'rank', 'order'=>'ASC', 'type'=>'a'));
            assign_to_template(array(
                'question'=>$question,
            ));
        }
    }

    public function edit() {

        $question = null;
            if (isset($this->params['id'])) {
            $question = $this->simplepoll_question->find('first', 'id='.$this->params['id']);
        }
        assign_to_template(array(
            'record'=>$question,
        ));
    }

    public function delete() {
        global $db;

        $question = $this->simplepoll_question->find('first', 'id='.$this->params['id']);
        parent::delete();
        $question = $this->simplepoll_question->find('first', "location_data='".$question->location_data."' AND active = 1");
        if (empty($question)) {
            $question = $this->simplepoll_question->find('first', "location_data='".$question->location_data."'");
            $question->active = 1;
//            $db->updateObject($question,'simplepoll_question');
            $question->update();
        }
    }

    public function edit_answer() {
        global $db;

        $question = null;
        $answer = null;
        if (isset($this->params['id'])) {
        	$answer = $db->selectObject('simplepoll_answer','id='.intval($_GET['id']));
        	if ($answer) {
                $question = $this->simplepoll_question->find('first','id='.$answer->simplepoll_question_id);
        	}
        } else if (isset($this->params['question_id'])) {
            $question = $this->simplepoll_question->find('first', 'id='.$this->params['question_id']);
        }

        if ($question) {
            assign_to_template(array(
                'answer'=>$answer,
                'question'=>$question,
           ));
        }
    }

    public function update_answer() {
        global $db;

        $answer = new stdClass();
        if (!empty($this->params['id'])) $answer->id = $this->params['id'];
        $answer->simplepoll_question_id = $this->params['simplepoll_question_id'];
        $answer->answer = $this->params['answer'];
        !empty($this->params['rank']) ? $answer->rank = $this->params['rank'] : $answer->rank = $db->max('simplepoll_answer','rank',null,"simplepoll_question_id=".$this->params['simplepoll_question_id'])+1;
        !empty($this->params['vote_count']) ? $answer->vote_count = $this->params['vote_count'] : $answer->vote_count = 0;
        if (isset($answer->id)) {
            $db->updateObject($answer,'simplepoll_answer');
        } else {
            $db->insertObject($answer,'simplepoll_answer');
        }
	    expHistory::returnTo('manageable');
    }

    public function delete_answer() {
        global $db;

        if (isset($this->params['id'])) {
        	$answer = $db->selectObject('simplepoll_answer','id='.$this->params['id']);
        	if ($answer) {
        		$question = $this->simplepoll_question->find('first','id='.$answer->simplepoll_question_id);
                if ($question) {
             		$db->delete('simplepoll_answer','id='.$answer->id);
             		$db->decrement('simplepoll_answer','rank',1,'simplepoll_question_id='.$question->id.' AND rank > '.$answer->rank);
             		expHistory::back();
               }
        	}
        }
    }

    public function activate() {
        global $db;

        $db->toggle('simplepoll_question',"active",'active=1');
        $active = $this->simplepoll_question->find('first',"id=".$this->params['id']);
        $active->active = 1;
        $active->update();
	    expHistory::returnTo('manageable');
    }

    public function vote() {
        global $db, $user;

        if (isset($this->params['choice'])) {
        	$answer = $db->selectObject('simplepoll_answer','id='.$this->params['choice']);
            $question = null;
        	if ($answer) {
        		$question = $db->selectObject('simplepoll_question','id='.$answer->simplepoll_question_id);
        	}
            if ($answer && $question) {
            	if (empty($this->config)) {
            		$this->config['anonymous_timeout'] = 5*3600;
            		$this->config['thank_you_message'] = 'Thank you for voting.';
            		$this->config['already_voted_message'] = 'You have already voted in this poll.';
            		$this->config['voting_closed_message'] = 'Voting has been closed for this poll.';
            	}

            	// Check to see if voting is even allowed:
            	if ($question->open_voting) {

            		// Time blocking
            		$timeblock = null;
                    if (is_object($user) && $user->id > 0) {
            			$timeblock = $db->selectObject('simplepoll_timeblock','user_id='.$user->id.' AND simplepoll_question_id='.$answer->simplepoll_question_id);
            		} else {
            			$timeblock = $db->selectObject('simplepoll_timeblock',"ip_hash='".md5($_SERVER['REMOTE_ADDR'])."' AND simplepoll_question_id=".$answer->simplepoll_question_id);
            		}

            		if ($timeblock == null || $timeblock->lock_expires < time() && $timeblock->lock_expires != 0) {
            			$answer->vote_count++;
            			$db->updateObject($answer,'simplepoll_answer');

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

            			if (isset($timeblock->id)) {
            				$db->updateObject($timeblock,'simplepoll_timeblock');
            			} else {
            				$db->insertObject($timeblock,'simplepoll_timeblock');
            			}

                        flash('error', $this->config['thank_you_message']);
            			if ($question->open_results) {
                            redirect_to(array('controller'=>'simplePoll', 'action'=>'results','id'=>$question->id));
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
        if ($question && $question->open_results) {
            $total = 0;
            $question->simplepoll_answer = expSorter::sort(array('array'=>$question->simplepoll_answer,'sortby'=>'vote_count', 'order'=>'DESC', 'type'=>'a'));
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