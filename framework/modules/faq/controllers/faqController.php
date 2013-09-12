<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
        'comments',
        'ealerts',
        'facebook',
        'files',
        'rss',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Frequently Asked Questions"); }
    static function description() { return gt("Display frequently asked questions.  Users may also post new questions to be answered."); }
    static function isSearchable() { return true; }

    public function showall() {
        expHistory::set('viewable', $this->params);
        $faqs = new faq();
        $questions = $faqs->find('all', $this->aggregateWhereClause().' AND include_in_faq=1', 'rank');

        if (empty($this->config['usecategories']) ? false : $this->config['usecategories']) {
            expCatController::addCats($questions,'rank',!empty($this->config['uncat'])?$this->config['uncat']:gt('Not Categorized'));
            $cats[0] = new stdClass();
            $cats[0]->name = '';
            $cats[0]->count = 0;
            $cats[0]->color = null;
            expCatController::sortedByCats($questions,$cats);
            assign_to_template(array(
                'cats'=>$cats
            ));
        }

        assign_to_template(array(
            'items'=>$questions
        ));
    }

//    public function showall_by_tags() {
//        global $db;
//
//        // get the tag being passed
//        $tag = new expTag($this->params['tag']);
//
//        // find all the id's of the filedownload for this filedownload module
////            $item_ids = $db->selectColumn('faqs', 'id', $this->aggregateWhereClause());
//        $item_ids = $db->selectColumn('faq', 'id', $this->aggregateWhereClause());
//
//        // find all the blogs that this tag is attached to
//        $items = $tag->findWhereAttachedTo('faq');
//
//        // loop the filedownload for this tag and find out which ones belong to this module
//        $items_by_tags = array();
//        foreach($items as $item) {
//            if (in_array($item->id, $item_ids)) $items_by_tags[] = $item;
//        }
//
//        // create a pagination object for the filedownload and render the action
//        $order = 'created_at';
//        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
//
//        $page = new expPaginator(array(
//                    'records'=>$items_by_tags,
//                    'limit'=>$limit,
//                    'order'=>$order,
//                    'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
//                    'controller'=>$this->baseclassname,
//                    'action'=>$this->params['action'],
//                    'columns'=>array('Title'=>'title'),
//                    ));
//
//        assign_to_template(array('page'=>$page, 'items'=>$page->records,'moduletitle'=>'FAQ\'s by tag "'.$this->params['tag'].'"'));
//    }

	/**
	 *    This manage function will show the FAQs that appear for a particular module, so if you have multiple FAQs around the site, this 
	 *    will let you edit each individual module's FAQs and the ranks associated with them.
	 */
    public function manage() {
        expHistory::set('manageable', $this->params);
        $page = new expPaginator(array(
            'model'=>'faq',
//            'where' => "location_data='".serialize($this->loc)."'",
            'where' => $this->aggregateWhereClause(),
		    'limit'=>25,
            'order'=>'rank',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('In FAQ')=>'include_in_faq',
                gt('Answered')=>'answer',
                gt('Question')=>'question',
                gt('Submitted')=>'created_at',
                gt('Submitted By')=>'submitter_name'
            ),
        ));
        
        assign_to_template(array(
            'page'=>$page
        ));
    }
    
    public function ask_question() {
        global $user;
        
        expHistory::set('editable', $this->params);
//        if ($user->isAdmin()) {
//            redirect_to(array('controller'=>'faq', 'action'=>'edit', 'src'=>$this->loc->src));
//        }
    }
    
    public function submit_question() {
        global $user;

        // figure out the name and email address
        if (!empty($user->id) && empty($this->params['id'])) {
            $this->params['submitter_name'] = $user->firstname." ".$user->lastname;
            $this->params['submitter_email'] = $user->email;
        }

        $faq = new faq();
        $faq->update($this->params);
        flash('message', gt('Your question has been submitted. Some one should get back to you shortly. Thank you.'));

        // send and email notification
//        if ($this->config['notify_of_new_question'] && !$user->isAdmin()) {
        if ($this->config['notify_of_new_question']) {
            $msg = gt("A Question was asked by").": <strong>" . $faq->submitter_name . "</strong><br>";
            $msg .= "<h3>".$faq->question."</h3>";

            $mail = new expMail();
            $mail->quickSend(array(
                'html_message'=>$msg,
                'to'=>trim(empty($this->config['notification_email_address'])?SMTP_FROMADDRESS:$this->config['notification_email_address']),
                'from'=>SMTP_FROMADDRESS,
                'subject'=>$this->config['notification_email_subject'],
            ));
        }

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
                    $tag = str_replace('"', "", $tag); // strip double quotes
                    $tag = str_replace("'", "", $tag); // strip single quotes
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
        if (!empty($this->params['include_in_faq'])) {
            $this->params['title'] = $this->params['question'];
            $this->params['body'] = $this->params['answer'];
            $this->addContentToSearch($this->params);
        }

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
        $reply  = "<strong>" . gt('An answer has been posted to your question') . "</strong><h3>".$faq->question."</h3>";
        if ($faq->include_in_faq) {
            $reply .= '<h4>' . gt('This question has also been selected to be included in the FAQ section on our site.') . '</h4>';
        }
        $reply .= '<h4>' . gt('The answer to your question is'). ':</h4>'.$faq->answer;
        $reply .= '<strong>' . gt('Thank you for submitting your question!') . '</strong>';
        
        $from = empty($this->config['answer_from_address']) ? SMTP_FROMADDRESS : $this->config['answer_from_address'];
        assign_to_template(array(
            'faq'=>$faq,
            'reply'=>$reply,
            'from'=>$from
        ));
    }
    
    public function update_answer() {
        if (empty($this->params['id'])) {
            flash('error', gt('No ID was specified for the question to be answered'));
            expHistory::back();
        }
        
        $faq = new faq($this->params['id']);

        if (!empty($faq->submitter_email)) {
            $mail = new expMail();
            $mail->quickSend(array(
                'html_message'=>$this->params['body'],
                'to'=>trim($faq->submitter_email),
                'from'=>array(empty($this->config['answer_from_address']) ? SMTP_FROMADDRESS : $this->config['answer_from_address']=>
                    empty($this->config['answer_from_name']) ? null : $this->config['answer_from_name']),
                'subject'=>$this->params['subject'],
            ));
            flash('message', gt('Your email was sent to').' '.$faq->submitter_name.' '.gt('at').' '.$faq->submitter_email);
        } else {
            flash('error', gt('Your email was NOT sent to').' '.$faq->submitter_name.' '.gt('at').' '.$faq->submitter_email);
        }
        expHistory::back();
    }

    function addContentToSearch() {
        global $db, $router;

        $count = 0;
        $model = new $this->basemodel_name(null, false, false);
        $where = (!empty($this->params['id'])) ? 'id='.$this->params['id'] : null;
        $content = $db->selectArrays($model->tablename,$where);
        foreach ($content as $cnt) {
            if (!empty($cnt['include_in_faq'])) {
                $cnt['title'] = $cnt['question'];
                $cnt['body'] = $cnt['answer'];

                $origid = $cnt['id'];
                unset($cnt['id']);
                //build the search record and save it.
//                $sql = "original_id=".$origid." AND ref_module='".$this->classname."'";
                $sql = "original_id=".$origid." AND ref_module='".$this->baseclassname."'";
                $oldindex = $db->selectObject('search',$sql);
                if (!empty($oldindex)) {
                    $search_record = new search($oldindex->id, false, false);
                    $search_record->update($cnt);
                } else {
                    $search_record = new search($cnt, false, false);
                }

                //build the search record and save it.
                $search_record->original_id = $origid;
                $search_record->posted = empty($cnt['created_at']) ? null : $cnt['created_at'];
                // get the location data for this content
                if (isset($cnt['location_data'])) $loc = expUnserialize($cnt['location_data']);
                $src = isset($loc->src) ? $loc->src : null;
                $link = str_replace(URL_FULL,'', makeLink(array('controller'=>$this->baseclassname, 'action'=>'showall', 'src'=>$src)));
    //	        if (empty($search_record->title)) $search_record->title = 'Untitled';
                $search_record->view_link = $link;
//                $search_record->ref_module = $this->classname;
                $search_record->ref_module = $this->baseclassname;
                $search_record->category = $this->searchName();
                $search_record->ref_type = $this->searchCategory();
                $search_record->save();
                $count += 1;
            }
         }

         return $count;
    }

}

?>