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

class faqController extends expController {
    public $useractions = array(
        'showall'=>'Show FAQs',
        'ask_question'=>'Show Question Form'
    );
	public $remove_configs = array(
//        'aggregation',
        'comments',
        'files',
        'rss',
        'tags'
    ); // all options: ('aggregation', 'categories','comments','ealerts','files','module_title','pagination', 'rss','tags')

    function displayname() { return "Frequently Asked Questions"; }
    function description() { return "This module allows you show frequently asked questions.  Users can post questions to you to answer too."; }
    
    public function showall() {
        expHistory::set('viewable', $this->params);
        $faqs = new faq();
        $questions = $faqs->find('all', $this->aggregateWhereClause().' AND include_in_faq=1', 'rank');

        if (empty($this->config['usecategories']) ? false : $this->config['usecategories']) {
            expCatController::addCats($questions,'rank');
            $cats = array();
            $cats[0]->name = '';
            expCatController::sortedByCats($questions,$cats);
            assign_to_template(array('cats'=>$cats));
        }

        assign_to_template(array('questions'=>$questions));
    }

	/**
	 *    This manage function will show the FAQs that appear for a particular module, so if you have multiple FAQs around the site, this 
	 *    will let you edit each individual module's FAQs and the ranks associated with them.
	 */
    public function manage() {
        expHistory::set('manageable', $this->params);
        $page = new expPaginator(array(
            'model'=>'faq',
            'where' => "location_data='".serialize($this->loc)."'",
		    'limit'=>25,
            'order'=>'rank',
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array('In FAQ'=>'include_in_faq', 'Answered'=>'answer', 'Question'=>'question', 'Submitted'=>'created_at', 'Submitted By'=>'submitter_name'),
        ));
        
        assign_to_template(array('page'=>$page));
    }
    
    public function ask_question() {
        global $user;
        
        expHistory::set('editable', $this->params);
//        if ($user->isAdmin()) {
//            redirect_to(array('controller'=>'faq', 'action'=>'edit', 'src'=>$this->loc->src));
//        }
    }
    
    public function submit_question() {
        $faq = new faq();
        $faq->update($this->params);
        flash('message', gt('Your question has been submitted. Some one should get back to you shortly. Thank you.'));
        expHistory::back();
    }
    
    public function update() {
        global $db;

        //check for and handle tags
        if (array_key_exists('expTag',$this->params)&&!empty($this->params['expTag'])) {
	        if (isset($this->params['id'])) {
    	        $db->delete('content_expTags', 'content_type="'.(!empty($this->params['content_type'])?$this->params['content_type']:$this->basemodel_name).'" AND content_id='.$this->params['id']);
    	    }
	        $tags = explode(",", trim($this->params['expTag']));
	        unset($this->params['expTag']);

	        foreach($tags as $tag) {
                if (!empty($tag)) {
                    $tag = strtolower(trim($tag));
                    $expTag = new expTag($tag);
                    if (empty($expTag->id)) $expTag->update(array('title'=>$tag));
                    $this->params['expTag'][] = $expTag->id;
                }
	        }
        }
        //check for and handle cats
        if (array_key_exists('expCat',$this->params)&&!empty($this->params['expCat'])) {
            $catid = $this->params['expCat'];
            unset($this->params['expCat']);
            $this->params['expCat'][] = $catid;
        }

        $faq = new faq();
        $faq->update($this->params);
        $this->addContentToSearch($this->params);

        if (!empty($this->params['send_email'])) {
            redirect_to(array('controller'=>'faq', 'action'=>'edit_answer', 'id'=>$faq->id, 'src'=>$this->loc->src));
        } else {
            expHistory::back();
        }
        
    }
    
    public function edit_toggle() {
        if (!empty($this->params['id'])) {
            $faq = new faq($this->params['id']);
            $faq->include_in_faq = empty($faq->include_in_faq) ? 1 : 0;
            $faq->save();
        }        
        
        expHistory::back();
    }
    
    public function edit_answer() {
        if (empty($this->params['id'])) {
            flash('error', gt('No ID was specified for the question to be answered'));
            expHistory::back();
        }
        
        $faq = new faq($this->params['id']);
        $reply  = '<h3>An answer has been posted to your question '.$faq->question.'</h3>';
        if ($faq->include_in_faq) {
            $reply .= '<h4>This question has also been selected to be included in the FAQ section on our site.</h4><br>';
        }
        $reply .= '<h4>The answer to your question:</h4><p>'.$faq->answer.'</p>';
        $reply .= '<strong>Thank you for submitting your question!</strong>';
        
        $from = empty($this->config['answer_from_address']) ? SMTP_FROMADDRESS : $this->config['answer_from_address'];
        assign_to_template(array('faq'=>$faq, 'reply'=>$reply, 'from'=>$from));
    }
    
    public function update_answer() {
        if (empty($this->params['id'])) {
            flash('error', gt('No ID was specified for the question to be answered'));
            expHistory::back();
        }
        
        $faq = new faq($this->params['id']);
        
        $mail = new expMail();
        $mail->quickSend(array(
                'html_message'=>$this->params['body'],
			    'to'=>trim($faq->submitter_email),
//			    'from'=>empty($this->config['answer_from_address']) ? SMTP_FROMADDRESS : $this->config['answer_from_address'],
//			    'from_name'=>empty($this->config['answer_from_name']) ? null : $this->config['answer_from_name'],
			    'from'=>array(empty($this->config['answer_from_address']) ? SMTP_FROMADDRESS : $this->config['answer_from_address']=>
			        empty($this->config['answer_from_name']) ? null : $this->config['answer_from_name']),
			    'subject'=>$this->params['subject'],
        ));
        
        flash('message', gt('Your email was sent to').' '.$faq->submitter_name.' '.gt('at').' '.$faq->submitter_email);
        expHistory::back();
    }
}

?>
