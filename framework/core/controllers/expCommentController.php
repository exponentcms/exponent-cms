<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file thats holds the expCommentController class.
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2006 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expCommentController
 *
 * @subpackage Core-Controllers
 * @package Framework
 */

class expCommentController extends expController {
    public $base_class = 'expComment';
    protected $add_permissions = array('approve'=>"Approve Comments");
   	protected $remove_permissions = array('create');

	function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Comments"; }
    function description() { return "Use this module to add comments to a page."; }
    function author() { return "Adam Kessler @ OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    
	function edit() {
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        
	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $comment = new expComment($id);
		assign_to_template(array('comment'=>$comment));
	}	
	
	function manage() {
	    expHistory::set('managable', $this->params);
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
	    
	    $sql  = 'SELECT c.* FROM '.DB_TABLE_PREFIX.'_expComments c ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_content_expComments cnt ON c.id=cnt.expcomments_id ';
        $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".$this->params['content_type']."' ";
        $sql .= 'AND c.approved=0';

        $page = new expPaginator(array(
            //'model'=>'expComment',
            'sql'=>$sql, 
            'limit'=>10,
            'order'=>'created_at',
            'dir'=>'DESC',
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array('Approved'=>'approved', 'Poster'=>'name', 'Comment'=>'body'),
        ));
        
        assign_to_template(array('page'=>$page));
	}
	
	function getComments() {
		global $user, $db;
                
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        
        $sql  = 'SELECT c.* FROM '.DB_TABLE_PREFIX.'_expComments c ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_content_expComments cnt ON c.id=cnt.expcomments_id ';
        $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".$this->params['content_type']."' ";
        $sql .= 'AND c.approved=1';

        $comments = new expPaginator(array(
            //'model'=>'expComment',
            'sql'=>$sql, 
            'limit'=>10,
            'order'=>'created_at',
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array('Readable Column Name'=>'Column Name'),
        ));
        
        //eDebug($comments);
        
        // count the unapproved comments
        if ($require_approval == 1 && $user->isAdmin()) {
            $sql  = 'SELECT count(com.id) as c FROM '.DB_TABLE_PREFIX.'_expComments com ';
            $sql .= 'JOIN '.DB_TABLE_PREFIX.'_content_expComments cnt ON com.id=cnt.expcomments_id ';
            $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".$this->params['content_type']."' ";
            $sql .= 'AND com.approved=0';
            $unapproved = $db->countObjectsBySql($sql);
        } else {
            $unapproved = 0;
        }        
        
        assign_to_template(array(
            'comments'=>$comments,
            'unapproved'=>$unapproved, 
			'content_id'=>$this->params['content_id'], 
			'content_type'=>$this->params['content_type'],
			'user'=>$user,
			'hideform'=>$this->params['hideform'],
			'hidecomments'=>$this->params['hidecomments'],
			'title'=>$this->params['title'],
			'formtitle'=>$this->params['formtitle'],
		));
	}

    function update() {
        global $db, $user;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        // check the anti-spam control
        if (!exponent_users_isLoggedIn())
        {
            expValidator::check_antispam($this->params, "Your comment could not be posted because anti-spam verification failed.  Please try again.");
        }
        
        // figure out the name and email address
        if (!empty($user->id) && empty($this->params['id'])) {
            $this->params['name'] = $user->firstname." ".$user->lastname;
            $this->params['email'] = $user->email;
        }
                
        // save the comment
        $this->expComment->update($this->params);
        
        // attach the comment to the datatype it belongs to (blog, news, etc..);
		$obj->content_type = $this->params['content_type'];
		$obj->content_id = $this->params['content_id'];
		$obj->expcomments_id = $this->expComment->id;
		if(isset($this->params['subtype'])) $obj->subtype = $this->params['subtype'];
		$db->insertObject($obj, $this->expComment->attachable_table);
		
		$msg = 'Thank you for posting a comment.';
		if ($require_approval == 1 && !$user->isAdmin()) {
		    $msg .= ' Your comment is now pending approval. You will receive an email to ';
		    $msg .= $this->expComment->email.' letting you know when it has been approved.'; 
		}
		
		if ($require_notification && !$user->isAdmin()) {
		    $this->sendNotification($this->expComment);
		}
        if ($require_approval==1 && $this->params['approved']==1) {
		    $this->sendApprovalNotification($this->expComment);
        }
		//if ($require_notification && !$user->isAdmin()) {
		//}
		
		flash('message', $msg);
		
		expHistory::back();
	}
	
	public function approve() {
	    expHistory::set('editable', $this->params);
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
	    
	    if (empty($this->params['id'])) {
	        flash('error', 'No ID supplied for comment to approve');
	        expHistory::back();
	    }
	    
	    $comment = new expComment($this->params['id']);
	    assign_to_template(array('comment'=>$comment));
	}
	
	public function approve_submit() {
	    if (empty($this->params['id'])) {
	        flash('error', 'No ID supplied for comment to approve');
	        expHistory::back();
	    }
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
	    
	    $comment = new expComment($this->params['id']);
	    $comment->body = $this->params['body'];
	    $comment->approved = $this->params['approved'];
	    $comment->save();
	    expHistory::back();
	}
	
	public function approve_toggle() {
	    if (empty($this->params['id'])) return;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
        
        
	    $comment = new expComment($this->params['id']);
	    $comment->approved = $comment->approved == 1 ? 0 : 1;
	    if ($comment->approved) {
		    $this->sendApprovalNotification($comment);
	    }
	    $comment->save();
	    expHistory::back();
	}
	public function delete() {
	    global $db;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
	    
	    if (empty($this->params['id'])) {
	        flash('error', 'Missing id for the comment you would like to delete');
	        expHistory::back();
	    }
	    
	    // delete the comment
        $comment = new expComment($this->params['id']);
        $rows = $comment->delete();
        
        // delete the assocication too
        $db->delete($comment->attachable_table, 'expcomments_id='.$this->params['id']);        
        
        // send the user back where they came from.
        expHistory::back();
	}
	
	private function sendNotification($comment) {
	    if (empty($comment)) return false;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
	    
	    // setup some email variables.
	    $subject = 'Notification of a New Comment Posted to '.URL_BASE;
        $tos = explode(',', str_replace(' ', '', $notification_email));
        $editlink = makelink(array('controller'=>'expComment', 'action'=>'edit', 'id'=>$comment->id));
        
        // make the email body
        $body  = 'Posted By:&nbsp;'.$comment->name."<br>";
        $body .= 'Posters Email:&nbsp;'.$comment->email."<br><br>";
        $body .= $comment->body."<br><br>";
        $body .= 'You can view, edit and optionally approved this comment by going to ';
        $body .= '<a href="'.$editlink.'">'.$editlink.'</a>';
        
        // create the mail message
        $mail = new expMail();        
        $mail->quickSend(array(
                'html_message'=>$body,
			    'to'=>$tos,
			    'from'=>trim(SMTP_FROMADDRESS),
			    'from_name'=>trim(ORGANIZATION_NAME),
			    'subject'=>$subject,
        ));
        
        return true;
	}

	private function sendApprovalNotification($comment) {
	    if (empty($comment)) return false;
        
        /* The global constants can be overriden by passing appropriate params */ 
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];
	    
	    // setup some email variables.
	    $subject = 'Notification of a New Comment Posted to '.URL_BASE;
        $tos = explode(',', str_replace(' ', '', $notification_email));
        $editlink = makelink(array('controller'=>'expComment', 'action'=>'edit', 'id'=>$comment->id));
        
        // make the email body
        $body  = 'Posted By:&nbsp;'.$comment->name."<br>";
        $body .= 'Posters Email:&nbsp;'.$comment->email."<br><br>";
        $body .= $comment->body."<br><br>";
        $body .= 'Your comment has been approved.';
        //$body .= '<a href="'.$editlink.'">'.$editlink.'</a>';
        
        // create the mail message
        $mail = new expMail();        
        $mail->quickSend(array(
                'html_message'=>$body,
			    'to'=>$tos,
			    'from'=>trim(SMTP_FROMADDRESS),
			    'from_name'=>trim(ORGANIZATION_NAME),
			    'subject'=>$subject,
        ));
        
        return true;
	}

}

?>