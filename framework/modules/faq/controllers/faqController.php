<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class faqController extends expController {
    public $useractions = array('showall'=>'Show FAQs', 'ask_question'=>'Show Question Form');
	public $codequality = 'beta';

	public $remove_configs = array(
        'aggregretion',
        'comments',
        'files',
        'rss',
        'tags'
    );

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Frequently Asked Questions"; }
    function description() { return "This module allows you show frequently asked questions.  Users can post questions to you to answer too."; }
    function author() { return "Adam Kessler - OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return true; }
    
    public function showall() {
        expHistory::set('viewable', $this->params);
        $faqs = new faq();
        $questions = $faqs->find('all', $this->aggregateWhereClause().' AND include_in_faq=1', 'rank');
        assign_to_template(array('questions'=>$questions));
    }
    
	
	
	/*
	 *    This manage function will show the FAQs that appear for a particular module, so if you have multiple FAQs around the site, this 
	 *    will let you edit each individual module's FAQs and the ranks associated with them.
	 */
    public function manage() {
        expHistory::set('managable', $this->params);
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
        if ($user->isAdmin()) {
            redirect_to(array('controller'=>'faq', 'action'=>'edit', 'src'=>$this->loc->src));
        }
    }
    
    public function submit_question() {
        $faq = new faq();
        $faq->update($this->params);
        flash('message', 'Your question has been submitted. Some one should get back to you shortly. Thank you.');
        expHistory::back();
    }
    
    public function update() {
        $faq = new faq();
        $faq->update($this->params);
        
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
            flash('error', 'No ID was specified for the question to be answered');
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
            flash('error', 'No ID was specified for the question to be answered');
            expHistory::back();
        }
        
        $faq = new faq($this->params['id']);
        
        $mail = new expMail();
        $mail->quickSend(array(
                'html_message'=>$this->params['body'],
			    'to'=>trim($faq->submitter_email),
			    'from'=>empty($this->config['answer_from_address']) ? SMTP_FROMADDRESS : $this->config['answer_from_address'],
			    'from_name'=>empty($this->config['answer_from_name']) ? null : $this->config['answer_from_name'],
			    'subject'=>$params['subject'],
        ));
        
        flash('message', 'Your email was sent to '.$faq->submitter_name.' at '.$faq->submitter_email);
        expHistory::back();
    }
}


?>
