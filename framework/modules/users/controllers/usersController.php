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
/** @define "BASE" "../../../.." */

class usersController extends expController {
    public $basemodel_name = 'user';
    public $add_permissions = array(
        'toggle_extension'=>'Activate Extensions',
        'kill_session'=>'End Sessions',
        'boot_user'=>'Boot Users',
    );
    public $remove_permissions = array(
        'create',
        'edit'
    );

    //public $useractions = array('showall'=>'Show all');

    function displayname() { return gt("User Manager"); }
    function description() { return gt("This is the user management module. It allows for creating user, editing user, etc."); }
    function hasSources() { return false; }
    function hasContent() { return false; }
    
    public function manage() {
        global $user;

        expHistory::set('manageable', $this->params);
//        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
//        $order = empty($this->config['order']) ? 'username' : $this->config['order'];
        if ($user->is_system_user == 1) {
            $filter = 1; //'1';
        } elseif($user->isSuperAdmin()) {
            $filter = 2; //"is_system_user != 1";
        } else {
            $filter = 3; //"is_admin != 1";
        }
//        $page = new expPaginator(array(
//                    'model'=>'user',
//                    'where'=>$where,
//                    'limit'=>$limit,
//                    'order'=>$order,
//                    'controller'=>$this->baseclassname,
//                    'action'=>$this->params['action'],
//                    'columns'=>array(
//                        gt('Username')=>'username',
//                        gt('First Name')=>'firstname',
//                        gt('Last Name')=>'lastname',
//                        gt('Is Admin')=>'is_acting_admin',
//                        )
//                    ));
//
//        assign_to_template(array('page'=>$page));
        assign_to_template(array(
            'filter'=>$filter
        ));
    }
    
    public function create() {
        redirect_to(array('controller'=>'users', 'action'=>'edituser'));
//        $this->edituser();
    }

    public function edituser() {
        global $user, $db;
        
        // set history
        expHistory::set('editable', $this->params);
        expSession::set("userkey",sha1(microtime()));
	    expSession::clearCurrentUserSessionCache();

        $id = !empty($this->params['id']) ? intval($this->params['id']) : null;
        
        // check to see if we should be editing.  You either need to be an admin, or editing own account.
        if ($user->isAdmin() || ($user->id == $id)) {
            $u = new user($id);
        } else {
            flash('error', gt('You do not have the proper permissions to edit this user'));
            expHistory::back();
        }
        $active_extensions = $db->selectObjects('profileextension','active=1','rank');

		//If there is no image uploaded, use the default avatar
        if(empty($u->image)) $u->image = DEFAULT_AVATAR;

        assign_to_template(array(
            'edit_user'=>$u,
            'extensions'=>$active_extensions,
            "userkey"=>expSession::get("userkey")
        ));
    }
    
    public function update() {
        global $user, $db;

        // get the id of user we are editing, if there is one
        $id = !empty($this->params['id']) ? intval($this->params['id']) : null;
        if ((($user->id == $id) || $user->isAdmin()) && $this->params['userkey'] != expSession::get("userkey")) expHistory::back();
        
        // make sure this user should be updating user accounts
        if (!$user->isLoggedIn() && SITE_ALLOW_REGISTRATION == 0){
            flash('error', gt('This site does not allow user registrations'));
            expHistory::back();
        } elseif (!$user->isAdmin() && ($user->isLoggedIn() && $user->id != $id)) {
            flash('error', gt('You do not have permission to edit this user account'));
            expHistory::back();
        }
        
        // if this is a new user account we need to check the password.  
        // the password fields wont come thru on an edit. Otherwise we will
        // just update the existing account.
        if (!empty($id)) {
            $u = new user($id);
            $u->update($this->params);  
            if ($user->isAdmin() && $user->id != $id) {
                flash('message', gt('Account information for').' '.$u->username.' '.gt('has been updated.'));
            } else {
                flash('message', gt('Thank you').' '.$u->firstname.'.  '.gt('Your account information has been updated.'));
            }
            if ($user->id == $id) {
                $_SESSION[SYS_SESSION_KEY]['user'] = $u;
                $user = $u;
            }
        } else {
            $u = new user($this->params);
            $ret = $u->setPassword($this->params['pass1'], $this->params['pass2']);
            if ($ret != true) expValidator::failAndReturnToForm($ret, $this->params);
            $u->save();
            if ($user->isAdmin()) {
                flash('message', gt('Created new user account for').' '.$u->username);
            } else {
                user::login($u->username, $this->params['pass1']);
                flash('message', gt('Thank you').' '.$u->firstname.'.  '.gt('Your new account has been created.'));
            }
        }     
        
        // update the user profiles
        if (!empty($u->id)) {
            $this->params['user_id'] = $u->id;
            // get the active profile extensions and save them out
            $active_extensions = $db->selectObjects('profileextension','active=1');
            foreach ($active_extensions as $pe) {
                if (is_file(BASE.$pe->classfile)) {
                   include_once(BASE.$pe->classfile);
                   $ext = new $pe->classname();
                   $db->delete($ext->tablename, 'user_id='.$u->id);
                   $ext->update($this->params);
                }
            }
        }
        
        // if this is a new account then we will check to see if we need to send 
        // a welcome message or admin notification of new accounts.
        if (empty($id)) {
            // Calculate Group Memberships for newly created users.  Any groups that
	        // are marked as 'inclusive' automatically pick up new users.  This is the part
	        // of the code that goes out, finds those groups, and makes the new user a member
	        // of them.
	        $memb = new stdClass();
	        $memb->member_id = $u->id;
	        // Also need to process the groupcodes, for promotional signup
	        $code_where = '';
	        if (isset($this->params['groupcode']) && $this->params['groupcode'] != '') {
		        $code_where = " OR code='".$this->params['groupcode']."'";
	        }
            // Add to default plus any groupcode groups
	        foreach($db->selectObjects('group','inclusive=1'.$code_where) as $g) {
		        $memb->group_id = $g->id;
		        $db->insertObject($memb,'groupmembership');
	        }

            // if we added the user to any group than we need to reload their permissions
//            expPermissions::load($u);  //FIXME why are we doing this? this loads the edited user perms over the current user???
            
	        //signup email stuff
          	if (USER_REGISTRATION_SEND_WELCOME){
          	    $msg = $u->firstname . ", \n\n";
		        $msg .= sprintf(USER_REGISTRATION_WELCOME_MSG, $u->firstname, $u->lastname, $u->username);
		        
		        $mail = new expMail();
		        $mail->quickSend(array(
					    'text_message'=>$msg,
					    'to'=>trim($u->email),
					    'from'=>SMTP_FROMADDRESS,
					    //'from_name'=>ecomconfig::getConfig('from_name'),
					    'subject'=>USER_REGISTRATION_WELCOME_SUBJECT,
		        ));
		        
		        flash('message', gt('A welcome email has been sent to').' '.$u->email);
	        }

            // send and email notification to the admin of the site.
	        if (USER_REGISTRATION_SEND_NOTIF && !$user->isAdmin()){
		        $msg = gt("When").": " . date("F j, Y, g:i a") ."\n\n";
		        $msg .= gt("Their name is").": " . $u->firstname . " " . $u->lastname . "\n\n";

		        $mail = new expMail();
		        $mail->quickSend(array(
					    'text_message'=>$msg,
					    'to'=>trim(USER_REGISTRATION_ADMIN_EMAIL),
					    'from'=>SMTP_FROMADDRESS,
					    //'from_name'=>ecomconfig::getConfig('from_name'),
					    'subject'=>USER_REGISTRATION_NOTIF_SUBJECT,
		        ));
          	}
        }

        // we need to reload our updated profile if we just edited our own account
        if ($id == $user->id) {
            $user->getUserProfile();
//            expPermissions::load($user);  // not sure this is necessary since we can't add groups here
        }

        expHistory::back();
    }
    
    public function delete() {
        global $user, $db;
        if (!$user->isAdmin()) {
            flash('error', gt('You do not have permission to delete user accounts'));
            expHistory::back();
        }
        
        if (empty($this->params['id'])) {
            flash('error', gt('No user selected.'));
            expHistory::back();
        }
        
        // remove group memeberships
        $db->delete('groupmembership', 'member_id='.$this->params['id']);
        
        // remove user permissions
        $db->delete('userpermission', 'uid='.$this->params['id']);
        
        //remove user profiles
        $active_extensions = $db->selectObjects('profileextension','active=1');
        foreach ($active_extensions as $pe) {
            if (is_file(BASE.$pe->classfile)) {
                include_once(BASE.$pe->classfile);
                $ext = new $pe->classname();
                $db->delete($ext->table, 'user_id='.$this->params['id']);
            }
        }
        
        // remove user address
        $address = new address();        
        $db->delete($address->table, 'user_id='.$this->params['id']);
        
        parent::delete();
    }
    
    public function manage_sessions() {
        global $db, $user;
        
        expHistory::set('manageable', $this->params);
        
        //cleans up any old sessions
		if(SESSION_TIMEOUT_ENABLE == true){	
			$db->delete('sessionticket','last_active < ' . (time() - SESSION_TIMEOUT));
		} else {
            $db->delete('sessionticket','1');
        }
		
		if (isset($_GET['id']) && $_GET['id'] == 0) {
			$sessions = $db->selectObjects('sessionticket', "uid<>0");
			$filtered = 1;
		} else {
			$sessions = $db->selectObjects('sessionticket');
			$filtered = 0;
		}
		
//	    $sessions = $db->selectObjects('sessionticket');
	    for ($i = 0; $i < count($sessions); $i++) {
		    $sessions[$i]->user = new user($sessions[$i]->uid);
			if ($sessions[$i]->uid == 0) {
				$sessions[$i]->user->id = 0;
			}
		    $sessions[$i]->duration = expDateTime::duration($sessions[$i]->last_active,$sessions[$i]->start_time);
	    }

	    assign_to_template(array(
            'sessions'=>$sessions,
            'filter'=>$filtered
        ));
    }
    
    public function kill_session() {
        global $user, $db;
        $ticket = $db->selectObject('sessionticket',"ticket='".preg_replace('/[^A-Za-z0-9.]/','',$this->params['ticket'])."'");
	    if ($ticket) {
		    $u = new user($ticket->uid);
		    if ($user->isSuperAdmin() || ($user->isActingAdmin() && !$u->isAdmin())) {
			    // We can only kick the user if they are A) not an acting admin, or 
			    // B) The current user is a super user and the kicked user is not.
			    $db->delete('sessionticket',"ticket='".$ticket->ticket."'");
		    }
	    }
	    expHistory::back();
    }
    
    public function boot_user() {        
        global $user, $db;
        if (!empty($this->params['id'])) {
		    $u = new user($this->params['id']);
		    if ($user->isSuperAdmin() || ($user->isActingAdmin() && !$u->isAdmin())) {
			    // We can only kick the user if they are A) not an acting admin, or 
			    // B) The current user is a super user and the kicked user is not.
			    $db->delete('sessionticket','uid='.$u->id);
		    }
	    }
	    expHistory::back();
    }
    
	/**
	 * This function scans two directories and searches for php files to add to the extensions database.
	 * If you have added new extensions since the last time you have visited the page, it will add them to the database
	 * in effect enabling your new extension to be tacked onto users profiles. You then have to enable it in the menu, but at least
	 * now it is in the system and when the user goes to edit his profile, it will check for extensions and this one will be in!
	 * 
	 * @global string This function uses the global $db save information through the Exponenet database connection.
	 */
    public function manage_extensions() {
        global $db;
        
        // set history
        expHistory::set('manageable', $this->params);
        
        // Lets find all the user profiles availabe and then see if they are
        // in the database yet.  If not we will add them.
		$extdirs = array(
			'framework/modules/users/extensions',
			'themes/'.DISPLAY_THEME.'framework/modules/users/extensions'
		);
		foreach ($extdirs as $dir) {
			if (is_readable(BASE.$dir)) {
	        	$dh = opendir(BASE.$dir);
	        	while (($file = readdir($dh)) !== false) {
                	if (is_file(BASE."$dir/$file") && is_readable(BASE."$dir/$file") && substr($file, 0, 1) != '_' && substr($file, 0, 1) != '.') {
                    	include_once(BASE."$dir/$file");
                    	$classname = substr($file,0,-4);                  	
                    	$class = new $classname();
                    	$extension = $db->selectObject('profileextension', "title='".$class->name()."'");
                    	if (empty($extension->id)) {
                    	    $pe = new profileextension();
                    	    $pe->title = $class->name();
                    	    $pe->body = $class->description();
                    	    $pe->classfile = "$dir/$file";
                    	    $pe->classname = $classname;
                    	    $pe->save();
                    	}
                	}
	        	}
			}
		}
		
		$page = new expPaginator(array(
		            'model'=>'profileextension',
		            'where'=>1,
		            'limit'=>25,
		            'order'=>'title',
		            'controller'=>$this->baseclassname,
		            'action'=>$this->params['action'],
		            'columns'=>array(
                        gt('Name')=>'title',
                        gt('Description')=>'body',
                        gt('Active')=>'active'
		                ),
		            ));
		
		assign_to_template(array(
            'page'=>$page
        ));
    }

    public function manage_groups() {
        expHistory::set('manageable', $this->params);
        $page = new expPaginator(array(
                    'model'=>'group',
                    'where'=>1, 
                    'limit'=>(isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10,
                    'order'=>empty($this->config['order']) ? 'name' : $this->config['order'],
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array(
                        gt('Name')=>'name',
                        gt('Description')=>'description',
                        gt('Type')=>'inclusive',
                        )
                    ));
                    
        assign_to_template(array(
            'page'=>$page
        ));
    }
    
    public function reset_password() {
        expHistory::set('editable', $this->params);
    }
    
    public function send_new_password() {
        global $db;
        
        // find the user
        $u = user::getUserByName($this->params['username']);
        if (empty($u)) {
            $u = user::getUserByEmail($this->params['username']);
            if (!empty($u) && $u->count > 1) {
                expValidator::failAndReturnToForm(gt('That email address applies to more than one user account, please enter your username instead.'));
            }
        }
        $u = new user($u->id);

        if (!expValidator::check_antispam($this->params)) {
            expValidator::failAndReturnToForm(gt('Anti-spam verification failed'), $this->params);
		} elseif (empty($u->id)) {
            expValidator::failAndReturnToForm(gt('We were unable to find an account with that username/email'), $this->params);
        } elseif (empty($u->email)) {
            expValidator::failAndReturnToForm(gt('Your account does not appear to have an email address.  Please contact the site administrators to reset your password'), $this->params);
        } elseif ($u->isAdmin()) {
            expValidator::failAndReturnToForm(gt('You cannot reset passwords for an administrator account.'), $this->params);
        }
	
        $tok = new stdClass();
        $tok->uid = $u->id;
        $tok->expires = time() + 2*3600;
        $tok->token = md5(time()).uniqid('');
	
        $email = $template = get_template_for_action($this, 'password_reset_email', $this->loc);
        $email->assign('token',$tok);
        $email->assign('username',$u->username);
        $msg = $email->render();
        $mail = new expMail();
        $mail->quickSend(array(
                'html_message'=>$msg,
			    'to'=>trim($u->email),
			    'from'=>SMTP_FROMADDRESS,
			    'subject'=>gt('Password Reset Requested'),
        ));
        
        $db->delete('passreset_token', 'uid='.$u->id);
        $db->insertObject($tok,'passreset_token');
        flash('message', gt('An email has been sent to you with instructions on how to finish resetting your password.').'<br><br>'.
            gt('This new password request is only valid for 2 hours.  If you have not completed the password reset process within 2 hours, the new password request will expire.'));
        
        expHistory::back();
    }
    
    public function confirm_password_reset() {
        global $db;

        $db->delete('passreset_token','expires < ' . time());
        $tok = $db->selectObject('passreset_token','uid='.intval($_GET['uid'])." AND token='".preg_replace('/[^A-Za-z0-9]/','',$_GET['token']) ."'");
        if ($tok == null) {
	        flash('error', gt('Your password reset request has expired.  Please try again.'));
	        expHistory::back();
        } 

        // create the password
        $newpass = '';
        for ($i = 0; $i < rand(12,20); $i++) {
	        $num=rand(48,122);
	        if(($num > 97 && $num < 122) || ($num > 65 && $num < 90) || ($num >48 && $num < 57)) $newpass.=chr($num);
	        else $i--;
        }

        // look up the user
        $u = new user($tok->uid);

        // get the email message body and render it
        $email = $template = get_template_for_action($this, 'confirm_password_email', $this->loc);
        $email->assign('newpass',$newpass);
        $email->assign('username',$u->username);
        $msg = $email->render();
        
        // send the new password to the user
        $mail = new expMail();
        $mail->quickSend(array(
                'html_message'=>$msg,
		        'to'=>trim($u->email),
		        'from'=>SMTP_FROMADDRESS,
		        'subject'=>gt('The account password for').' '.HOSTNAME.' '.gt('was reset'),
        ));        
        
        // Save new password
        $u->update(array('password'=>md5($newpass)));

        // cleanup the reset token
        $db->delete('passreset_token','uid='.$tok->uid);

        flash('message', gt('Your password has been reset and the new password has been emailed to you.'));

        // send the user the login page.
        redirect_to(array('controller'=>'login', 'action'=>'loginredirect'));
    }
    
    public function change_password() {
        global $user;

        expHistory::set('editable', $this->params);
        $id = isset($this->params['ud']) ? $this->params['ud'] : $user->id;

        if ($user->isAdmin() || ($user->id == $id)) {
            $isuser = ($user->id == $id) ? 1 : 0 ;
            $u = new user($id);
        } else {
            flash('error', gt('You do not have the proper permissions to do that'));
            expHistory::back();
        }
        assign_to_template(array(
            'u'=>$u,
            'isuser'=>$isuser
        ));
    }
    
    public function save_change_password() {
        global $user;

        if (!$user->isAdmin() && ($this->params['uid'] != $user->id)) {
            flash('error', gt('You do not have permissions to change this users password.'));
            expHistory::back();
        }
        
        if (!$user->isAdmin() && (empty($this->params['password']) || $user->password != md5($this->params['password']))) {
            flash('error', gt('The current password you entered is not correct.'));
            expHistory::returnTo('editable');
        }
        //eDebug($user);
        $u = new user($this->params['uid']);

        $ret = $u->setPassword($this->params['new_password1'], $this->params['new_password2']);
        //eDebug($u, true);
        if (is_string($ret)) {
            flash('error', $ret);
            expHistory::returnTo('editable');
        } else {
            $u->update();  
        }
        
        if ($this->params['uid'] != $user->id) {
            flash('message', gt('The password for').' '.$u->username.' '.gt('has been changed.'));
        } else {
            $user->password = $u->password;
            flash('message', gt('Your password has been changed.'));
        }
        expHistory::back();
    }
    
    public function edit_userpassword() {
        expHistory::set('editable', $this->params);
        if (empty($this->params['id'])) {
            flash('error', gt('You must specify the user whose password you want to change'));
        }
        
        $u = new user($this->params['id']);
        assign_to_template(array(
            'u'=>$u
        ));
    }
    
    public function update_userpassword() {
        if (empty($this->params['id'])) {
            expValidator::failAndReturnToForm(gt('You must specify the user whose password you want to change'), $this->params);
        }
        
        if (empty($this->params['new_password1'])) {
            expValidator::setErrorField('new_password1');
            expValidator::failAndReturnToForm(gt('You must specify a new password for this user.'), $this->params);
        }
         
        if (empty($this->params['new_password2'])) {
            expValidator::setErrorField('new_password2');
            expValidator::failAndReturnToForm(gt('You must confirm the password.'), $this->params);
            
        }
        
        $u = new user($this->params['id']);
        $ret = $u->setPassword($this->params['new_password1'], $this->params['new_password2']);
        if (is_string($ret)) {
            expValidator::setErrorField('new_password1');
            $this->params['new_password1'] = '';
            $this->params['new_password2'] = '';
            expValidator::failAndReturnToForm($ret, $this->params);
        } else {
            $u->save(true);
        }
        
        flash('message', gt('Password reset for user').' '.$u->username);
        expHistory::back();
    }
    
    public function edit_group() {
        global $db;

        expHistory::set('editable', $this->params);
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $group = new group($id);
        $group->redirect = $db->selectValue('section','id',"sef_name='".$group->redirect."'");
        assign_to_template(array(
            'record'=>$group
        ));
    }
    
    public function manage_group_memberships() {
        global $db, $user;
//        expHistory::set('manageable', $this->params);

        $memb = $db->selectObject('groupmembership','member_id='.$user->id.' AND group_id='.$this->params['id'].' AND is_admin=1');

        $perm_level = 0;
        if ($memb) $perm_level = 1;
        if (expPermissions::check('user_management',expCore::makeLocation('administrationmodule'))) $perm_level = 2;

        $group = $db->selectObject('group','id='.$this->params['id']);
		$users = user::getAllUsers(0);
		
		$members = array();
		$admins = array();
		foreach ($db->selectObjects('groupmembership','group_id='.$group->id) as $m) {
			$members[] = $m->member_id;
			if ($m->is_admin == 1) {
				$admins[] = $m->member_id;
			}
		}
		
		for ($i = 0; $i < count($users); $i++) {
			if (in_array($users[$i]->id,$members)) {
				$users[$i]->is_member = 1;
			} else {
				$users[$i]->is_member = 0;
			}
			
			if (in_array($users[$i]->id,$admins)) {
				$users[$i]->is_admin = 1;
			} else {
				$users[$i]->is_admin = 0;
			}
		}

        //$limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        $page = new expPaginator(array(
//                    'model'=>'user',
					'records'=>$users,
                    'where'=>1, 
//                    'limit'=>9999,  // unless we're showing all users on a page at once, there's no way to
                                    // add all users to a group, since it's rebuilding the group on save...
                    'order'=>empty($this->config['order']) ? 'username' : $this->config['order'],
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array(
                        gt('Username')=>'username',
                        gt('First Name')=>'firstname',
                        gt('Last Name')=>'lastname',
                        gt('Is Member')=>'is_member',
                        gt('Is Admin')=>'is_admin',
                        )
                    ));
                    
        assign_to_template(array(
			'page'=>$page,
            'group'=>$group,
            'users'=>$users,
            'canAdd'=>(count($members) < count($users) ? 1 : 0),
            'hasMember'=>(count($members) > 0 ? 1 : 0),
            'perm_level'=>$perm_level,
        ));
    }
    
    public function update_group() {
        global $db;

        $group = new group();
        if (!empty($this->params['redirect'])) {
            $this->params['redirect'] = $db->selectValue('section','sef_name','id='.$this->params['redirect']);
        }
        $group->update($this->params);
        expHistory::back();
    }

    public function delete_group() {
        global $user, $db;
        if (!$user->isAdmin()) {
            flash('error', gt('You do not have permission to delete user groups'));
            expHistory::back();
        }
        
        if (empty($this->params['id'])) {
            flash('error', gt('No group selected.'));
            expHistory::back();
        }
        
        // remove group members
        $db->delete('groupmembership', 'group_id='.$this->params['id']);
        
        // remove group permissions
        $db->delete('grouppermission', 'gid='.$this->params['id']);
        
        // remove group
        $db->delete('group', 'id='.$this->params['id']);
        expHistory::back();		
    }
        
    public function toggle_extension() {
        global $db;
	    if (isset($this->params['id'])) $db->toggle('profileextension', 'active', 'id='.$this->params['id']);
	    expHistory::back();
    }

    public function update_memberships() {
        global $user, $db;
        
        //$memb = $db->selectObject('groupmembership','member_id='.$user->id.' AND group_id='.$this->params['id'].' AND is_admin=1');
    	$group = $db->selectObject('group','id='.intval($this->params['id']));

		$db->delete('groupmembership','group_id='.$group->id);
		$memb = new stdClass();
		$memb->group_id = $group->id;
		if ($this->params['memdata'] != "") {
			foreach ($this->params['memdata'] as $u=>$str) {
				$memb->member_id = $u;
				$memb->is_admin = $str['is_admin'];
				$db->insertObject($memb,'groupmembership');
			}
		}
		expPermissions::triggerRefresh();
        expHistory::back();
        
    }
	
	public function getUsersByJSON() {
		global $db,$user;
        $modelname = $this->basemodel_name;
        $results = 25; // default get all
        $startIndex = 0; // default start at 0
        $sort = null; // default don't sort
        $dir = 'asc'; // default sort dir is asc
        $sort_dir = SORT_ASC;

        // How many records to get?
        if(strlen($_GET['results']) > 0) {
            $results = $_GET['results'];
        }

        // Start at which record?
        if(strlen($_GET['startIndex']) > 0) {
            $startIndex = $_GET['startIndex'];
        }

        // Sorted?
        if(strlen($_GET['sort']) > 0) {
            $sort = $_GET['sort'];
            if ($sort = 'id') $sort = 'username';
        }

        if(!empty($_GET['filter'])) {
            switch ($_GET['filter']) {
                case '1' :
                    $filter = '';
                    break;
                case '2' :
                    $filter = "is_system_user != 1";
                    break;
                case '3' :
                    $filter = "is_admin != 1";
            }
        }

        // Sort dir?
        if((strlen($_GET['dir']) > 0) && ($_GET['dir'] == 'desc')) {
            $dir = 'desc';
            $sort_dir = SORT_DESC;
        }
        else {
            $dir = 'asc';
            $sort_dir = SORT_ASC;
        }
        
        if (!empty($_GET['query'])) {

            $_GET['query'] = expString::sanitize($_GET['query']);
            $totalrecords = $this->$modelname->find('count',(empty($filter)?'':$filter." AND ")."(username LIKE '%".$_GET['query']."%' OR firstname LIKE '%".$_GET['query']."%' OR lastname LIKE '%".$_GET['query']."%' OR email LIKE '%".$_GET['query']."%')");
            
            $users = $this->$modelname->find('all',(empty($filter)?'':$filter." AND ")."(username LIKE '%".$_GET['query']."%' OR firstname LIKE '%".$_GET['query']."%' OR lastname LIKE '%".$_GET['query']."%' OR email LIKE '%".$_GET['query']."%')" ,$sort.' '.$dir, $results, $startIndex);
			
			for($i = 0; $i < count($users); $i++) {
				if(ECOM == 1) {
					$users[$i]->usernamelabel = "<a href='viewuser/{$users[$i]->id}'  class='fileinfo'>{$users[$i]->username}</a>";
				} else {
					$users[$i]->usernamelabel = $users[$i]->username;
				}
			}
			
		   $returnValue = array(
                'recordsReturned'=>count($users),
                'totalRecords'=>$totalrecords,
                'startIndex'=>$startIndex,
                'sort'=>$sort,
                'dir'=>$dir,
                'pageSize'=>$results,
                'records'=>$users
            );
        } else {
          
            $totalrecords = $this->$modelname->find('count',$filter);
			
            $users = $this->$modelname->find('all',$filter,$sort.' '.$dir, $results, $startIndex);
			
			for($i = 0; $i < count($users); $i++) {
				if(ECOM == 1) {
					$users[$i]->usernamelabel = "<a href='viewuser/{$users[$i]->id}'  class='fileinfo'>{$users[$i]->username}</a>";
				} else {
					$users[$i]->usernamelabel = $users[$i]->username;
				}
			}

            $returnValue = array(
                'recordsReturned'=>count($users),
                'totalRecords'=>$totalrecords,
                'startIndex'=>$startIndex,
                'sort'=>$sort,
                'dir'=>$dir,
                'pageSize'=>$results,
                'records'=>$users
            );
                  
        }
        
        echo json_encode($returnValue);
	}
	
	public function viewuser() {
		
		$u = new user($this->params['id']);
		$address = new address();
	
		$billings = $address->find('all', 'user_id='.$u->id.' AND is_billing = 1');
		$shippings = $address->find('all', 'user_id='.$u->id.' AND is_shipping = 1');	
		
		// build out a SQL query that gets all the data we need and is sortable.
		$sql  = 'SELECT o.*, b.firstname as firstname, b.billing_cost as total, b.middlename as middlename, b.lastname as lastname, os.title as status, ot.title as order_type ';
		$sql .= 'FROM '.DB_TABLE_PREFIX.'_orders o, '.DB_TABLE_PREFIX.'_billingmethods b, ';
		$sql .= DB_TABLE_PREFIX.'_order_status os, ';                                          
		$sql .= DB_TABLE_PREFIX.'_order_type ot ';                                          
		$sql .= 'WHERE o.id = b.orders_id AND o.order_status_id = os.id AND o.order_type_id = ot.id AND o.purchased > 0 AND user_id =' . $u->id;     
		
		
		$limit = (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 50;
		//eDebug($sql, true);
		$orders = new expPaginator(array(
			//'model'=>'order',
			'controller'=>$this->params['controller'],
			'action'=>$this->params['action'],
			'sql'=>$sql,            
			'order'=>'purchased',
			'dir'=>'DESC',
			'limit'=>$limit,
			'columns'=>array(
                gt('Order #')=>'invoice_id',
                gt('Total')=>'total',
                gt('Date Purchased')=>'purchased',
                gt('Type')=>'order_type_id',
                gt('Status')=>'order_status_id',
                gt('Ref')=>'orig_referrer',
				)
			));
		
		 
		 assign_to_template(array(
			'u'=>$u,
            'billings'=>$billings,
			'shippings'=>$shippings,
            'orders'=>$orders,
        ));
	}

}

?>