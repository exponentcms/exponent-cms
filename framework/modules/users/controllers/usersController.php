<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
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

class usersController extends expController {
    public $basemodel_name = 'user';
    public $add_permissions = array(
        'toggle_extension'=>'Activate Extensions', 
        'edit_user'=>'Edit Users',
        'kill_session'=>'End Sessions',
        'boot_user'=>'Boot Users',
    );
    public $remove_permissions = array('create', 'edit_user');
    
    //public $useractions = array('showall'=>'Show all');

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "User Manager"; }
    function description() { return "This is the user management module. It allows for creating user, editing user, etc."; }
    function author() { return "Adam Kessler - OIC Group, Inc"; }
    function hasSources() { return false; }
    function hasViews() { return true; }
    function hasContent() { return false; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return false; }
    
    public function manage() {
        expHistory::set('managable', $this->params);
        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        $order = empty($this->config['order']) ? 'username' : $this->config['order'];
        $page = new expPaginator(array(
                    'model'=>'user',
                    'where'=>1, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array(
                        'Username'=>'username',
                        'First Name'=>'firstname',
                        'Last Name'=>'lastname',
                        'Is Admin'=>'is_acting_admin',
                        )
                    ));
                    
        assign_to_template(array('page'=>$page)); 
    }
    
    public function create() {
        redirect_to(array('controller'=>'users', 'action'=>'edituser'));
    }    
    public function edituser() {
        global $user, $db;
        
        // set history
        expHistory::set('editable', $this->params);
        
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        
        // check to see if we should be editing.  You either need to be an admin, or
        // editing your own account.
        if ($user->isAdmin() || ($user->id == $id)) {
            $u = new user($id);
        } else {
            flash('error', 'You do not have the proper permissions to edit this user');
            expHistory::back();
        }
        
        $active_extensions = $db->selectColumn('profileextension','classname','active=1', 'rank');
        assign_to_template(array('edit_user'=>$u, 'extensions'=>$active_extensions));
    }
    
    public function update() {
        global $user, $db;
        
        // get the id of user we are editing, if there is one
        $id = empty($this->params['id']) ? null : $this->params['id'];
        
        // make sure this user should be updating user accounts
        if (!exponent_users_isLoggedIn() && SITE_ALLOW_REGISTRATION == 0){
            flash('error', 'This site does not allow user registrations');
            expHistory::back();
        } elseif (!$user->isAdmin() && ($user->isLoggedIn() && $user->id != $id)) {
            flash('error', 'You do not have permission to edit this user account');            
            expHistory::back();
        }
        
        // if this is a new user account we need to check the password.  
        // the password fields wont come thru on an edit. Otherwise we will
        // just update the existing account.
        if (!empty($id)) {
            $u = new user($id);
            $u->update($this->params);  
            if ($user->isAdmin()) {
                flash('message', 'Account information for '.$u->username.' has been updated.');
            } else {
                flash('message', 'Thank you '.$u->firstname.'.  Your account information has been updated.');
            }          
        } else {
            $u = new user($this->params);
            $ret = $u->setPassword($this->params['pass1'], $this->params['pass2']);
            if ($ret != true) expValidator::failAndReturnToForm($ret, $this->params);
            $u->save();
            if ($user->isAdmin()) {
                flash('message', 'Created new user account for '.$u->username);
            } else {
                user::login($u->username, $this->params['pass1']);
                flash('message', 'Thank you '.$u->firstname.'.  Your new account has been created.');
            }
        }     
        
        // update the user profiles
        if (!empty($u->id)) {
            $this->params['user_id'] = $u->id;
            // get the active profile extensions and save them out
            $active_extensions = $db->selectObjects('profileextension','active=1');
            foreach ($active_extensions as $pe) {
                include_once($pe->classfile);
                $ext = new $pe->classname();
                $db->delete($ext->tablename, 'user_id='.$u->id);
                $ext->update($this->params);
            }
        }
        
        // if this is a new account then we will check to see if we need to send 
        // a welcome message or admin notification of new accounts.
        if (empty($id)) {
            // Calculate Group Memeberships for newly created users.  Any groups that
	        // are marked as 'inclusive' automatically pick up new users.  This is the part
	        // of the code that goes out, finds those groups, and makes the new user a member
	        // of them.
	        $memb = null;
	        $memb->member_id = $u->id;
	        // Also need to process the groupcodes, for promotional signup
	        $code_where = '';
	        if (isset($this->params['groupcode']) && $this->params['groupcode'] != '') {
		        $code_where = " OR code='".$this->params['groupcode']."'";
	        }
	        foreach($db->selectObjects('group','inclusive=1'.$code_where) as $g) {
		        $memb->group_id = $g->id;
		        $db->insertObject($memb,'groupmembership');
	        }

            // if we added the user to any group than we need to reload their permissions
            exponent_permissions_load($u);
            
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
		        
		        flash('message', 'A welcome email has been sent to '.$u->email);
	        }

            // send and email notification to the admin of the site.
	        if (USER_REGISTRATION_SEND_NOTIF && !$user->isAdmin()){
		        $msg = "When: " . date("F j, Y, g:i a") ."\n\n";
		        $msg .= "Their name is: " . $u->firstname . " " . $u->lastname . "\n\n";

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
        
        expHistory::back();
    }
    
    public function delete() {
        global $user, $db;
        if (!$user->isAdmin()) {
            flash('error', 'You do not have permission to delete user accounts');
            expHistory::back();
        }
        
        if (empty($this->params['id'])) {
            flash('error', 'No user selected.');
            expHistory::back();
        }
        
        // remove group memeberships
        $db->delete('groupmembership', 'member_id='.$this->params['id']);
        
        // remove user permissions
        $db->delete('userpermission', 'uid='.$this->params['id']);
        
        //remove user profiles
        $active_extensions = $db->selectObjects('profileextension','active=1');
        foreach ($active_extensions as $pe) {
            include_once($pe->classfile);
            $ext = new $pe->classname();
            $db->delete($ext->table, 'user_id='.$this->params['id']);
        }
        
        // remove user address
        $address = new address();        
        $db->delete($address->table, 'user_id='.$this->params['id']);
        
        parent::delete();
    }
    
    public function manage_sessions() {
        global $db, $user;
        
        expHistory::set('managable', $this->params);
        
        //cleans up any old sessions
	    $db->delete('sessionticket','last_active < ' . (time() - SESSION_TIMEOUT));
	
	    if (!defined('SYS_DATETIME')) require_once(BASE.'subsystems/datetime.php');
	
	    $sessions = $db->selectObjects('sessionticket');
	    for ($i = 0; $i < count($sessions); $i++) {
		    $sessions[$i]->user = new user($sessions[$i]->uid);
		    $sessions[$i]->duration = exponent_datetime_duration($sessions[$i]->last_active,$sessions[$i]->start_time);
	    }
	
	    assign_to_template(array('sessions'=>$sessions));
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
        expHistory::set('managable', $this->params);
        
        // Lets find all the user profiles availabe and then see if they are
        // in the database yet.  If not we will add them.
		$extdirs = array(BASE.'framework/modules/users/extensions', 
		BASE.'themes/'.DISPLAY_THEME_REAL.'framework/modules/users/extensions');
		foreach ($extdirs as $dir) {
			if (is_readable($dir)) {
	        	$dh = opendir($dir);
	        	while (($file = readdir($dh)) !== false) {
                	if (is_file("$dir/$file") && is_readable("$dir/$file") && substr($file, 0, 1) != '_' && substr($file, 0, 1) != '.') {
                    	include_once("$dir/$file");
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
		                'Name'=>'title',
		                'Description'=>'body',
		                'Active'=>'active'
		                ),
		            ));
		
		assign_to_template(array('page'=>$page));
    }

    public function manage_groups() {
        expHistory::set('managable', $this->params);
        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        $order = empty($this->config['order']) ? 'name' : $this->config['order'];
        $page = new expPaginator(array(
                    'model'=>'group',
                    'where'=>1, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array(
                        'Name'=>'name',
                        'Description'=>'description',
                        'Type'=>'inclusive',
                        )
                    ));
                    
        assign_to_template(array('page'=>$page)); 
    }
    
    public function reset_password() {
        expHistory::set('editable', $this->params);
    }
    
    public function send_new_password() {
        global $db;
        
        // find the user
        $u = user::getByUsername($this->params['username']);

        if (empty($u)) {
            expValidator::failAndReturnToForm('We were unable to find an account with that username', $this->params);
        } elseif (empty($u->email)) {
            expValidator::failAndReturnToForm('Your account does not appear to have an email address.  Please contact the site administrators to reset your password', $this->params);
        } elseif ($u->isAdmin()) {
            expValidator::failAndReturnToForm('You cannot reset passwords for an administrator account.', $this->params);
        }
	
        $tok = null;
        $tok->uid = $u->id;
        $tok->expires = time() + 2*3600;
        $tok->token = md5(time()).uniqid('');
	
        $email = $template = get_template_for_action($this, 'password_reset_email', $this->loc);
        $email->assign('token',$tok);
        $msg = $email->render();
        $mail = new expMail();
        $mail->quickSend(array(
                'html_message'=>$msg,
			    'to'=>trim($u->email),
			    'from'=>SMTP_FROMADDRESS,
			    'subject'=>'Your password has',
        ));
        
        $db->delete('passreset_token', 'uid='.$u->id);
        $db->insertObject($tok,'passreset_token');
        flash('message', 'An email has been sent to your email address with instructions on how to 
        finish resetting your password.<br><br>The new password is good for 2 hours.  If you have not completed
        the password reset process in 2 hours time, the new password will expire.');
        
        expHistory::back();
    }
    
    public function confirm_password_reset() {
        global $db;

        $db->delete('passreset_token','expires < ' . time());
        $tok = $db->selectObject('passreset_token','uid='.trim($_GET['uid'])." AND token='".preg_replace('/[^A-Za-z0-9]/','',$_GET['token']) ."'");
        if ($tok == null) {
	        flash('error', 'Your password reset has expired.  Please try again.');
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
        $msg = $email->render();
        
        // send the new password to the user
        $mail = new expMail();
        $mail->quickSend(array(
                'html_message'=>$msg,
		        'to'=>trim($u->email),
		        'from'=>SMTP_FROMADDRESS,
		        'subject'=>'Your new password for '.HOSTNAME,
        ));        
        
        // Save new password
        $u->update(array('password'=>md5($newpass)));        

        // cleanup the reset token
        $db->delete('passreset_token','uid='.$tok->uid);

        flash ('message', 'Your new password has been emailed to your email account.');

        // send the user the login page.
        redirect_to(array('module'=>'loginmodule', 'action'=>'loginredirect'));
    }
    
    public function change_password() {
        global $user;
        expHistory::set('editable', $this->params);
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        
        if ($user->isAdmin() || ($user->id == $id)) {
            $isuser = ($user->id == $id) ? 1 : 0 ;
            $u = new user($id);
        } else {
            flash('error', expLang::gettext('You do not have the proper permissions to do that'));
            expHistory::back();
        }
        assign_to_template(array('u'=>$u,'isuser'=>$isuser));
    }
    
    public function save_change_password() {
        global $user, $db;
        if (!$user->isAdmin() && ($this->params['uid'] != $user->id)) {
            flash('error', 'You do not have permissions to change this users password.');
            expHistory::back();
        }
        
        if (!$user->isAdmin() && (empty($this->params['password']) || $user->password != md5($this->params['password']))) {
            flash('error', 'The current password you entered is not correct.');
            expHistory::returnTo('editable');
        }
        //eDebug($user);
        $u = new user($this->params['uid']);

        $ret = $u->setPassword($this->params['new_password1'], $this->params['new_password2']);
        //eDebug($u, true);
        if (is_string($ret)) {
            flash('error', $ret);
            expHistory::returnTo('editable');
        }else{
            $u->update();  
        }
        
        if ($this->params['uid'] != $user->id) {
            flash('message', 'Your password for '.$u->username.' been reset.');
        } else {
            flash('message', 'Your password has been reset.');
        }
        expHistory::back();
    }
    
    public function edit_userpassword() {
        expHistory::set('editable', $this->params);
        if (empty($this->params['id'])) {
            flash ('error', 'You must specify the user whose password you want to change');
        }
        
        $u = new user($this->params['id']);
        assign_to_template(array('u'=>$u));
    }
    
    public function update_userpassword() {
        if (empty($this->params['id'])) {
            expValidator::failAndReturnToForm('You must specify the user whose password you want to change', $this->params);
        }
        
        if (empty($this->params['new_password1'])) {
            expValidator::setErrorField('new_password1');
            expValidator::failAndReturnToForm('You must specify a new password for this user.', $this->params);
        }
         
        if (empty($this->params['new_password2'])) {
            expValidator::setErrorField('new_password2');
            expValidator::failAndReturnToForm('You must confirm the password.', $this->params);
            
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
        
        flash('message', 'Password reset for user '.$u->username);
        expHistory::back();
    }
    
    public function edit_group() {
        expHistory::set('editable', $this->params);
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $group = new group($id);
        assign_to_template(array('record'=>$group));
    }
    
    public function manage_group_memberships() {
        global $db, $user;
        expHistory::set('manageable', $this->params);
        if (!defined('SYS_USERS')) require_once(BASE.'subsystems/users.php');
		
        $memb = $db->selectObject('groupmembership','member_id='.$user->id.' AND group_id='.$this->params['id'].' AND is_admin=1');

        $perm_level = 0;
        if ($memb) $perm_level = 1;
        if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) $perm_level = 2;

        $group = $db->selectObject('group','id='.$this->params['id']);
		$users = exponent_users_getAllUsers(0);
		
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
		
        assign_to_template(array(
            'group'=>$group,
            'users'=>$users,
            'canAdd'=>(count($members) < count($users) ? 1 : 0),
            'hasMember'=>(count($members) > 0 ? 1 : 0),
            'perm_level'=>$perm_level,
        ));
    }
    
    public function update_group() {
        $group = new group();
        $group->update($this->params);
        expHistory::back();
    }
    
    public function toggle_extension() {
        global $db;
	    if (isset($this->params['id'])) $db->toggle('profileextension', 'active', 'id='.$this->params['id']);
	    expHistory::back();
    }
}

?>
