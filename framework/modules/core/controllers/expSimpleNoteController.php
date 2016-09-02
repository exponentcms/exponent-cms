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
 * This is the class expSimpleNoteController
 *
 * @package Core
 * @subpackage Controllers
 */

class expSimpleNoteController extends expController {
    public $base_class = 'expSimpleNote';
    protected $add_permissions = array(
        'approve'=>"Approve Comments"
    );
//    protected $remove_permissions = array(
//        'edit',
//        'create'
//    );

    static function displayname() { return gt("Simple Notes"); }
    static function description() { return gt("Use this module to add Simple Notes attached to something (product, order, etc)"); }
    static function author() { return "Jonathan Worent @ OIC Group, Inc"; }
    
    function edit() {
        global $user;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        if (empty($this->params['formtitle'])) 
        {
            if (empty($this->params['id']))
            {
                $formtitle = gt("Add New Note");
            }
            else
            {
                $formtitle = gt("Edit Note");
            }
        }
        else
        {
            $formtitle = $this->params['formtitle'];
        }
        
        $id = empty($this->params['id']) ? null : $this->params['id'];
        $simpleNote = new expSimpleNote($id);
        //FIXME here is where we might sanitize the note before displaying/editing it

        assign_to_template(array(
            'simplenote'=>$simpleNote,
            'user'=>$user,
            'require_login'=>$require_login,
            'require_approval'=>$require_approval,
            'require_notification'=>$require_notification,
            'notification_email'=>$notification_email,
            'formtitle'=>$formtitle,
            'content_type'=>$this->params['content_type'],
            'content_id'=>$this->params['content_id'],
            'tab'=>empty($this->params['tab'])?0:$this->params['tab']
        ));
    }    
    
    function manage() {
        expHistory::set('manageable', $this->params);
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        $sql  = 'SELECT n.* FROM '.DB_TABLE_PREFIX.'_expSimpleNote n ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_content_expSimpleNote cnt ON n.id=cnt.expsimplenote_id ';
        $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".$this->params['content_type']."' ";
        $sql .= 'AND n.approved=0';
        
        $page = new expPaginator(array(
//            'model'=>'expSimpleNote',  // brings in all of model
            'sql'=>$sql, 
            'limit'=>10,
            'order'=>'created_at',
            'dir'=>'DESC',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array(
                gt('Approved')=>'approved',
                gt('Poster')=>'name',
                gt('Comment')=>'body'
            ),
        ));
        
        //FIXME here is where we might sanitize the notes before displaying them

        assign_to_template(array(
            'page'=>$page,
            'require_login'=>$require_login,
            'require_approval'=>$require_approval,
            'require_notification'=>$require_notification,
            'notification_email'=>$notification_email,
            'tab'=>$this->params['tab']
        ));
    }
    
    function getNotes() {
        global $user, $db;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        
        $sql  = 'SELECT n.* FROM '.$db->prefix.'expSimpleNote n ';
        $sql .= 'JOIN '.$db->prefix.'content_expSimpleNote cnt ON n.id=cnt.expsimplenote_id ';
        $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".$this->params['content_type']."' ";
        $sql .= 'AND n.approved=1';
        
        $simplenotes = new expPaginator(array(
            //'model'=>'expSimpleNote',
            'sql'=>$sql, 
            'limit'=>10,
            'order'=>'created_at',
            'dir'=>'DESC',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array(
                gt('Readable Column Name')=>'Column Name'
            ),
        ));
        
        // count the unapproved comments
        if ($require_approval == 1 && $user->isAdmin()) {
            $sql  = 'SELECT count(com.id) as c FROM '.$db->prefix.'expSimpleNote com ';
            $sql .= 'JOIN '.$db->prefix.'content_expSimpleNote cnt ON com.id=cnt.expsimplenote_id ';
            $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".$this->params['content_type']."' ";
            $sql .= 'AND com.approved=0';
            $unapproved = $db->countObjectsBySql($sql);
        } else {
            $unapproved = 0;
        }        
 
        //FIXME here is where we might sanitize the notes before displaying them

        assign_to_template(array(
            'simplenotes'=>$simplenotes,
            'unapproved'=>$unapproved, 
            'content_id'=>$this->params['content_id'], 
            'content_type'=>$this->params['content_type'],
            'user'=>$user,
            'hideform'=>$this->params['hideform'],
            'hidenotes'=>$this->params['hidenotes'],
            'title'=>$this->params['title'],
            'formtitle'=>$this->params['formtitle'],
            'require_login'=>$require_login,
            'require_approval'=>$require_approval,
            'require_notification'=>$require_notification,
            'notification_email'=>$notification_email,
            'tab'=>$this->params['tab']
        ));
    }

    function update() {
        global $db, $user, $history;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        // figure out the name and email address
        if (!empty($user->id)) {
            $this->params['name'] = $user->firstname." ".$user->lastname;
            $this->params['email'] = $user->email;
        }
        
        // if simplenotes are configed to require approvals set this to 0 otherwise we 
        // will just go ahead and turn the approval on for this comment.
        $this->expSimpleNote->approved = ($require_approval == 1 && !$user->isAdmin()) ? 0 : 1;
        
        // save the note
        //FIXME here is where we might sanitize the note before saving it
        $this->expSimpleNote->update($this->params);
        
        // attach the note to the datatype it belongs to (product, order, etc..);
//        $obj = new stdClass();
//        $obj->content_type = $this->params['content_type'];
//        $obj->content_id = $this->params['content_id'];
//        $obj->expsimplenote_id = $this->expSimpleNote->id;
//        if(isset($this->params['subtype'])) $obj->subtype = $this->params['subtype'];
//        $db->insertObject($obj, $this->expSimpleNote->attachable_table);
        $this->expSimpleNote->attachNote($this->params['content_type'], $this->params['content_id'], $this->params['subtype']);

        $msg = gt('Your note has been added.');
        if ($require_approval == 1 && !$user->isAdmin()) {
            $msg .= ' '.gt('Your note is now pending approval. You will receive an email to').' ';
            $msg .= $this->expSimpleNote->email.' '.gt('letting you know when it has been approved.');
        }
        
        if ($require_notification && !$user->isAdmin()) {
            $this->sendNotification($this->expComment);
        }
        
        flash('message', $msg);
        
        
        $lastUrl = makelink($history->history[$history->history['lasts']['type']][count($history->history[$history->history['lasts']['type']])-1]['params']);
        if (!empty($this->params['tab']))
        {
            $lastUrl .= "#".$this->params['tab'];
        }
        redirect_to($lastUrl);
    }
    
    public function approve() {
        expHistory::set('editable', $this->params);
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        if (empty($this->params['id'])) {
            flash('error', gt('No ID supplied for note to approve'));
            $lastUrl = expHistory::getLast('editable');
        }
        
        $simplenote = new expSimpleNote($this->params['id']);
        assign_to_template(array(
            'simplenote'=>$simplenote,
            'require_login'=>$require_login,
            'require_approval'=>$require_approval,
            'require_notification'=>$require_notification,
            'notification_email'=>$notification_email,
            'tab'=>$this->params['tab']
        ));
    }
    
    public function approve_submit() {
        global $history;
        
        if (empty($this->params['id'])) {
            flash('error', gt('No ID supplied for comment to approve'));
            $lastUrl = expHistory::getLast('editable');
        }
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        $simplenote = new expSimpleNote($this->params['id']);
        //FIXME here is where we might sanitize the note before approving it
        $simplenote->body = $this->params['body'];
        $simplenote->approved = $this->params['approved'];
        $simplenote->save();
        
        $lastUrl = makelink($history->history[$history->history['lasts']['type']][count($history->history[$history->history['lasts']['type']])-1]['params']);
        if (!empty($this->params['tab']))
        {
            $lastUrl .= "#".$this->params['tab'];
        }
        redirect_to($lastUrl);
    }
    
    public function approve_toggle() {
        global $history;
        
        if (empty($this->params['id'])) return;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        
        $simplenote = new expSimpleNote($this->params['id']);
        $simplenote->approved = $simplenote->approved == 1 ? 0 : 1;
        $simplenote->save();
        
        $lastUrl = makelink($history->history[$history->history['lasts']['type']][count($history->history[$history->history['lasts']['type']])-1]['params']);
        if (!empty($this->params['tab']))
        {
            $lastUrl .= "#".$this->params['tab'];
        }
        redirect_to($lastUrl);
    }
    
    public function delete() {
        global $db, $history;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        if (empty($this->params['id'])) {
            flash('error', gt('Missing id for the comment you would like to delete'));
            $lastUrl = expHistory::getLast('editable');
        }
        
        // delete the note
        $simplenote = new expSimpleNote($this->params['id']);
        $rows = $simplenote->delete();
        
        // delete the assocication too
        $db->delete($simplenote->attachable_table, 'expsimplenote_id='.$this->params['id']);        
        
        // send the user back where they came from.
        $lastUrl = expHistory::getLast('editable');
        if (!empty($this->params['tab']))
        {
            $lastUrl .= "#".$this->params['tab'];
        }
        redirect_to($lastUrl);
    }
    
    private function sendNotification($simplenote) {
        if (empty($simplenote)) return false;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? SIMPLENOTE_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? SIMPLENOTE_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? SIMPLENOTE_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? SIMPLENOTE_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        // setup some email variables.
        $subject = 'Notification of a New Note Posted to '.URL_BASE;
        $tos = explode(',', str_replace(' ', '', $notification_email));
        $editlink = makelink(array('controller'=>'expSimpleNote', 'action'=>'edit', 'id'=>$simplenote->id));
        
        // make the email body
        $body  = gt('Posted By').':&#160;'.$simplenote->name."<br>";
        $body .= gt('Posters Email').':&#160;'.$simplenote->email."<br><br>";
        $body .= $simplenote->body."<br><br>";
        $body .= gt('You can view, edit and optionally approved this comment by going to').' ';
        $body .= '<a href="'.$editlink.'">'.$editlink.'</a>';
        
        // create the mail message
        $mail = new expMail();        
        $mail->quickSend(array(
                'html_message'=>$body,
                'to'=>$tos,
                'from'=>array(trim(SMTP_FROMADDRESS) => trim(ORGANIZATION_NAME)),
                'subject'=>$subject,
        ));
        
        return true;
    }

}

?>
