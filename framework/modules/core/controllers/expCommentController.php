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
 * This is the class expCommentController
 *
 * @package Core
 * @subpackage Controllers
 */

class expCommentController extends expController {
    public $base_class = 'expComment';
    protected $remove_permissions = array(
        'create'
    );
    protected $add_permissions = array(
        'approve'=>"Approve Comments",
        'bulk'=>"Bulk Actions"
    );

    static function displayname() { return gt("Comments"); }
    static function description() { return gt("Use this module to add comments to a page."); }

	function edit() {
	    if (empty($this->params['content_id'])) {
	        flash('message',gt('An error occurred: No content id set.'));
            expHistory::back();
	    }
        /* The global constants can be overridden by passing appropriate params */
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
//        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
//        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
//        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
//        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];


	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $comment = new expComment($id);
        //FIXME here is where we might sanitize the comment before displaying/editing it
		assign_to_template(array(
		    'content_id'=>$this->params['content_id'],
            'content_type'=>$this->params['content_type'],
		    'comment'=>$comment
		));
	}

	function manage() {
	    expHistory::set('manageable', $this->params);

        $order = 'approved';
        $dir = 'ASC';
        /* The global constants can be overridden by passing appropriate params */
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
//        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
//        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
//        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
//        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];

	    $sql  = 'SELECT c.*, cnt.* FROM '.DB_TABLE_PREFIX.'_expComments c ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_content_expComments cnt ON c.id=cnt.expcomments_id ';
        if (!empty($this->params['content_id']) && !empty($this->params['content_type'])) {
            $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".$this->params['content_type']."' ";
            $order = 'created_at';
            $dir = 'DESC';
        }
        //$sql .= 'AND c.approved=0';

        $page = new expPaginator(array(
//            'model'=>'expComment',
            'sql'=>$sql,
            'limit'=>10,
            'order'=>$order,
            'dir'=>$dir,
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array(
                gt('Approved')=>'approved',
                gt('Poster')=>'name',
                gt('Comment')=>'body',
                gt('Type')=>'content_type'
            ),
        ));

        $refs[][] = array();
        foreach ($page->records as $record) {
            //FIXME here is where we might sanitize the comments before displaying them
            $item = new $record->content_type($record->content_id);
            $refs[$record->content_type][$record->content_id] = $item->title;
        }
        assign_to_template(array(
            'page'=>$page,
            'refs'=>$refs,
        ));
	}

    /**
     * Displays comments attached to specified item
     */
	function showComments() {
		global $user, $db;

        /* The global constants can be overridden by passing appropriate params */
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : intval($this->params['require_login']);
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : intval($this->params['require_approval']);
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : intval($this->params['require_notification']);
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : expString::escape($this->params['notification_email']);

//        $sql  = 'SELECT c.*, ua.image, u.username FROM '.$db->prefix.'expComments c ';
//        $sql .= 'JOIN '.$db->prefix.'content_expComments cnt ON c.id=cnt.expcomments_id ';
//        $sql .= 'JOIN '.$db->prefix.'user_avatar ua ON c.poster=ua.user_id ';
//        $sql .= 'JOIN '.$db->prefix.'user u ON c.poster=u.id ';
//        $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".$this->params['content_type']."' ";

        $sql  = 'SELECT c.* FROM '.$db->prefix.'expComments c ';
        $sql .= 'JOIN '.$db->prefix.'content_expComments cnt ON c.id=cnt.expcomments_id ';
        $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".expString::escape($this->params['content_type'])."' ";
        if (!$user->isAdmin()) {
            $sql .= 'AND c.approved=1';
        }

        $comments = new expPaginator(array(
            //'model'=>'expComment',
            'sql'=>$sql,
//            'limit'=>999,
            'order'=>'created_at',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array(
                gt('Readable Column Name')=>'Column Name'
            ),
        ));

        // add username and avatar
        foreach ($comments->records as $key=>$record) {
            $commentor = new user($record->poster);
            //FIXME here is where we might sanitize the comments before displaying them
//            $comments->records[$key]->username = $commentor->username;  //FIXME this should follow the site attribution setting
            $comments->records[$key]->username = user::getUserAttribution($commentor->id);  // follow the site attribution setting
            $comments->records[$key]->avatar = $db->selectObject('user_avatar',"user_id='".$record->poster."'");
        }

        if (empty($this->params['config']['disable_nested_comments'])) $comments->records = self::arrangecomments($comments->records);
        // eDebug($sql, true);

        // count the unapproved comments
        if ($require_approval == 1 && $user->isAdmin()) {
            $sql  = 'SELECT count(com.id) as c FROM '.$db->prefix.'expComments com ';
            $sql .= 'JOIN '.$db->prefix.'content_expComments cnt ON com.id=cnt.expcomments_id ';
            $sql .= 'WHERE cnt.content_id='.$this->params['content_id']." AND cnt.content_type='".expString::escape($this->params['content_type'])."' ";
            $sql .= 'AND com.approved=0';
            $unapproved = $db->countObjectsBySql($sql);
        } else {
            $unapproved = 0;
        }

        $this->config = $this->params['config'];
        $type = !empty($this->params['type']) ? $this->params['type'] : gt('Comment');
        $ratings = !empty($this->params['ratings']) ? true : false;

        assign_to_template(array(
            'comments'=>$comments,
            'config'=>$this->params['config'],
            'unapproved'=>$unapproved,
			'content_id'=>$this->params['content_id'],
			'content_type'=>$this->params['content_type'],
			'user'=>$user,
			'hideform'=>$this->params['hideform'],
			'hidecomments'=>$this->params['hidecomments'],
			'title'=>$this->params['title'],
			'formtitle'=>$this->params['formtitle'],
            'type'=>$type,
            'ratings'=>$ratings,
            'require_login'=>$require_login,
            'require_approval'=>$require_approval,
            'require_notification'=>$require_notification,
            'notification_email'=>$notification_email,
		));
	}

    /**
     * function to arrange comments in hierarchy of parent_id's as children properties
     *
     * @param array $comments
     *
     * @return array
     */
    function arrangecomments($comments) {

        $tree = array();

        /* We get all the parent into the tree array */
        foreach ($comments as &$node) {
            /* Note: I've used 0 for top level parent, you can change this to == 'NULL' */
            if($node->parent_id=='0'){
                $tree[] = $node;
                unset($node);
            }
        }

        /* This is the recursive function that does the magic */
        /* $k is the position in the array */
        if (!function_exists('findchildren')) {
            function findchildren(&$parent, &$comments, $k = 0)
            {
                if (isset($comments[$k])) {
                    if ($comments[$k]->parent_id == $parent->id) {
                        $com = $comments[$k];
                        findchildren($com, $comments); // We try to find children's children
                        $parent->children[] = $com;
                    }
                    findchildren($parent, $comments, $k + 1); // And move to the next sibling
                }
            }
        }

        /* looping through the parent array, we try to find the children */
        foreach ($tree as &$parent) {
            findchildren($parent, $comments);
        }

        return $tree;
    }

    /**
     * Returns count of comments attached to specified item
     *
     * @static
     * @param $params
     * @return int
     */
    public static function countComments($params) {
//        global $user, $db;
        global $user;

        $sql  = 'SELECT c.* FROM '.DB_TABLE_PREFIX.'_expComments c ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_content_expComments cnt ON c.id=cnt.expcomments_id ';
        $sql .= 'WHERE cnt.content_id='.$params['content_id']." AND cnt.content_type='".$params['content_type']."' ";
        if (!$user->isAdmin()) {
            $sql .= 'AND c.approved=1';
        }

        $comments = new expPaginator(array(
            'sql'=>$sql,
        ));
        return count($comments->records);
//        return $count = $db->countObjectsBySql($sql);

    }

    /**
     * Returns comments attached to specified item
     *
     * @static
     * @param $params
     * @return array
     */
    public static function getComments($params) {
        global $user, $db;

          $sql  = 'SELECT c.* FROM '.$db->prefix.'expComments c ';
          $sql .= 'JOIN '.$db->prefix.'content_expComments cnt ON c.id=cnt.expcomments_id ';
          $sql .= 'WHERE cnt.content_id='.$params['content_id']." AND cnt.content_type='".$params['content_type']."' ";
          if (!$user->isAdmin()) {
              $sql .= 'AND c.approved=1';
          }

          $comments = new expPaginator(array(
              //'model'=>'expComment',
              'sql'=>$sql,
  //            'limit'=>999,
              'order'=>'created_at',
//              'page'=>(isset($params['page']) ? $params['page'] : 1),
              'controller'=>'expComment',
//              'action'=>$params['action'],
//              'columns'=>array(
//                  gt('Readable Column Name')=>'Column Name'
//              ),
          ));

          // add username and avatar
          foreach ($comments->records as $key=>$record) {
              $commentor = new user($record->poster);
              //FIXME here is where we might sanitize the comments before displaying them
//              $comments->records[$key]->username = $commentor->username;  //FIXME this should follow the site attribution setting
              $comments->records[$key]->username = user::getUserAttribution($commentor->id);  // follow the site attribution setting
              $comments->records[$key]->avatar = $db->selectObject('user_avatar',"user_id='".$record->poster."'");
          }
//          if (empty($params['config']['disable_nested_comments'])) $comments->records = self::arrangecomments($comments->records);
          // eDebug($sql, true);

          // count the unapproved comments
          $unapproved = 0;

//          assign_to_template(array(
//            'comments'=>$comments,
//            'config'=>$params['config'],
//            'unapproved'=>$unapproved,
//            'content_id'=>$params['content_id'],
//            'content_type'=>$params['content_type'],
//            'user'=>$user,
//            'hideform'=>$params['hideform'],
//            'hidecomments'=>$params['hidecomments'],
//            'title'=>$params['title'],
//            'formtitle'=>$params['formtitle'],
//        ));
        return $comments->records;
    }

    function update() {
        global $user;

        /* The global constants can be overridden by passing appropriate params */
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
//        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
//        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];

        if (COMMENTS_REQUIRE_LOGIN && !$user->isLoggedIn()) {
            expValidator::failAndReturnToForm('You must be logged on to post a comment!', $this->params);
        }
        // check the anti-spam control
        if (!(ANTI_SPAM_USERS_SKIP && $user->isLoggedIn())) {
            expValidator::check_antispam($this->params, gt('Your comment was not posted.') . ' ' . gt("Anti-spam verification failed.  Please try again. Please try again."));
        }

        // figure out the name and email address
        if (!empty($user->id) && empty($this->params['id'])) {
            $this->params['name'] = $user->firstname." ".$user->lastname;
            $this->params['email'] = $user->email;
        }

        // save the comment
        if (empty($require_approval)) {
            $this->expComment->approved=1;
        }
        $this->expComment->update($this->params);

        // attach the comment to the datatype it belongs to (blog, news, etc..);
//        $obj = new stdClass();
//		$obj->content_type = $this->params['content_type'];
//		$obj->content_id = $this->params['content_id'];
//		$obj->expcomments_id = $this->expComment->id;
//		if(isset($this->params['subtype'])) $obj->subtype = $this->params['subtype'];
//		$db->insertObject($obj, $this->expComment->attachable_table);
        $this->expComment->attachComment($this->params['content_type'], $this->params['content_id'], $this->params['subtype']);

		$msg = 'Thank you for posting a comment.';
		if ($require_approval == 1 && !$user->isAdmin()) {
		    $msg .= ' '.gt('Your comment is now pending approval. You will receive an email to').' ';
		    $msg .= $this->expComment->email.' '.gt('letting you know when it has been approved.');
		}

		if ($require_notification && !$user->isAdmin()) {
		    $this->sendNotification($this->expComment,$this->params);
		}
        if ($require_approval==1 && $this->params['approved']==1 && $this->expComment->poster != $user->id) {
		    $this->sendApprovalNotification($this->expComment,$this->params);
        }

		flash('message', $msg);

		expHistory::back();
	}

	public function approve() {
	    expHistory::set('editable', $this->params);

        /* The global constants can be overriden by passing appropriate params */
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
//        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
//        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
//        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
//        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];

	    if (empty($this->params['id'])) {
	        flash('error', gt('No ID supplied for comment to approve'));
	        expHistory::back();
	    }

	    $comment = new expComment($this->params['id']);
	    assign_to_template(array(
            'comment'=>$comment
        ));
	}

	public function approve_submit() {
	    if (empty($this->params['id'])) {
	        flash('error', gt('No ID supplied for comment to approve'));
	        expHistory::back();
	    }

        /* The global constants can be overriden by passing appropriate params */
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
//        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
//        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
//        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
//        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];

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
//        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
//        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
//        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
//        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];

	    $comment = new expComment($this->params['id']);
	    $comment->approved = $comment->approved == 1 ? 0 : 1;
	    if ($comment->approved) {
		    $this->sendApprovalNotification($comment,$this->params);
	    }
	    $comment->save();
	    expHistory::back();
	}

    /**
     * this method bulk processes the selected comments
     */
    function bulk_process() {
        global $db;

        if (!empty($this->params['bulk_select']) && !empty($this->params['command'])) {
            foreach ($this->params['bulk_select'] as $item) {
                switch ($this->params['command']) {
                    case 1:  // approve
                        $comment = new expComment($item);
                        if (!$comment->approved) {
                            $comment->approved = 1;
                            //FIXME here is where we might sanitize the comments before approving them
                            $attached = $db->selectObject('content_expComments','expcomments_id='.$item);
                            $params['content_type'] = $attached->content_type;
                            $params['content_id'] = $attached->content_id;
                            $this->sendApprovalNotification($comment,$params);
                            $comment->save();
                        }
                        break;
                    case 2:  // disable
                        $comment = new expComment($item);
                  	    $comment->approved = 0;
                  	    $comment->save();
                        break;
                    case 3:  //delete
                        // delete the comment
                        $comment = new expComment($item);
                        $comment->delete();
                        // delete the association too
                        $db->delete($comment->attachable_table, 'expcomments_id='.$item);
                }
            }
        }
        expHistory::returnTo('manageable');
    }

	public function delete() {
	    global $db;

        /* The global constants can be overriden by passing appropriate params */
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
//        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
//        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
//        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
//        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];

	    if (empty($this->params['id'])) {
	        flash('error', gt('Missing id for the comment you would like to delete'));
	        expHistory::back();
	    }

	    // delete the comment
        $comment = new expComment($this->params['id']);
        $comment->delete();

        // delete the association too
        $db->delete($comment->attachable_table, 'expcomments_id='.$this->params['id']);

        // send the user back where they came from.
        expHistory::back();
	}

	private function sendNotification($comment,$params) {
//	    global $db;
	    if (empty($comment)) return false;

        //eDebug($comment,1);
        /* The global constants can be overriden by passing appropriate params */
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
//        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
//        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
//        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];

	    // setup some email variables.
	    $subject = gt('Notification of a New Comment Posted to').' '.URL_BASE;
        $tos = explode(',', str_replace(' ', '', $notification_email));
        $tos = array_filter($tos);
        if (empty($tos)) return false;

        $model = new $params['content_type']($params['content_id']);
//	    $loc = expUnserialize($model->location_data);

        $posting = makelink(array('controller'=>$params['content_type'], 'action'=>'show', 'title'=>$model->sef_url));
        $editlink = makelink(array('controller'=>'expComment', 'action'=>'edit', 'content_id'=>$params['content_id'], 'content_type'=>$params['content_type'], 'id'=>$comment->id));

        // make the email body
        $body = '<h1>'.gt('New Comment Posted').'</h1>';
        $body .= '<h2>'.gt('Posted By').'</h2>';
        $body .= '<p>'.$comment->name."</p>";
        $body .= '<h2>'.gt('Poster\'s Email').'</h2>';
        $body .= '<p>'.$comment->email."</p>";
        $body .= '<h2>'.gt('Comment').'</h2>';
        $body .= '<p>'.$comment->body.'</p>';
        $body .= '<h3>'.gt('View posting').'</h3>';
        $body .= '<a href="'.$posting.'">'.$posting.'</a>';
        //1$body .= "<br><br>";
        $body .= '<h3>'.gt('Edit / Approve comment').'</h3>';
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

	private function sendApprovalNotification($comment,$params) {
	    if (empty($comment)) return false;

        /* The global constants can be overriden by passing appropriate params */
        //sure wish I could do this once in the constructor. sadly $this->params[] isn't set yet
//        $require_login = empty($this->params['require_login']) ? COMMENTS_REQUIRE_LOGIN : $this->params['require_login'];
//        $require_approval = empty($this->params['require_approval']) ? COMMENTS_REQUIRE_APPROVAL : $this->params['require_approval'];
//        $require_notification = empty($this->params['require_notification']) ? COMMENTS_REQUIRE_NOTIFICATION : $this->params['require_notification'];
        $notification_email = empty($this->params['notification_email']) ? COMMENTS_NOTIFICATION_EMAIL : $this->params['notification_email'];

	    // setup some email variables.
	    $subject = gt('Notification of Comment Approval on').' '.URL_BASE;
        $tos = explode(',', str_replace(' ', '', $notification_email));
        $tos[] = $comment->email;
		$tos = array_filter($tos);
		if (empty($tos)) return false;

        $model = new $params['content_type']($params['content_id']);
//	    $loc = expUnserialize($model->location_data);

        $posting = makelink(array('controller'=>$params['content_type'], 'action'=>'show', 'title'=>$model->sef_url));

        // make the email body
        $body = '<h1>'.gt('Comment Approved').'</h1>';
        $body .= '<h2>'.gt('Posted By').'</h2>';
        $body .= '<p>'.$comment->name."</p>";
        $body .= '<h2>'.gt('Poster\'s Email').'</h2>';
        $body .= '<p>'.$comment->email.'</p>';
        $body .= '<h2>'.gt('Comment').'</h2>';
        $body .= '<p>'.$comment->body."</p>";
        $body .= '<h3>'.gt('View posting').'</h3>';
        $body .= '<a href="'.$posting.'">'.$posting.'</a>';

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
