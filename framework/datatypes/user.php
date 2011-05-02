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

class user extends expRecord {
    public $validates = array(
		    'presence_of'=>array(
			    'firstname'=>array('message'=>'First name is a required field.'),
			    'lastname'=>array('message'=>'Last name is a required field.'),			    			
		    ),
		    'is_valid_email'=>array(
			    'email'=>array('message'=>'The email address does not appear to be valid')
	        ),
	        'uniqueness_of'=>array(
			    'username'=>array('message'=>'That username is already take. Please use another username.')
	        ),
	        'length_of'=>array(
			    'username'=>array('length'=>3)
	        ),
		);
		
	function __construct($params=null, $get_assoc = false, $get_attached = false) {
		if (is_array($params) && isset($params['pass1'])) $params['password'] = $this->encryptPassword($params['pass1']);
		parent::__construct($params, $get_assoc, $get_attached);
		$this->getUserProfile();
		$this->groups = $this->getGroupMemberships();
		$this->getsToolbar = $this->getsToolbar();
	}	

	 public function save($overrideUsername = false) {
        global $db;
        
        // if someone is trying to make this user an admin, lets make sure they have permission to do so.
        if(isset($params['is_admin']) || isset($params['is_acting_admin'])) $this->checkAdminFlags();
        
        // if the site is configured to use the email addy as the username we need to force the 
        // the email address into the username field.
        if (USER_REGISTRATION_USE_EMAIL == 1 && !empty($this->email) && $overrideUsername == false) $this->username = $this->email;
        parent::save();        
    }
	
	public static function login($username, $password) {
        global $db;

	    // Retrieve the user object from the database.  This may be null, if the username is non-existent.
	    $user = new user($db->selectValue('user', 'id', "username='" . $username . "'"));
	
	    // if the user object doesn't have an id then we didn't find a valid user accout with this username
	    if (empty($user->id)) return false; 
	
	    // try to authenticate the user - use the authentication type specified in the site config
	    if ( USE_LDAP == 1 && (empty($user) || $user->is_ldap ==1)) {
		    $ldap = new expLDAP();
		    $ldap->connect();
		    $authenticated = $ldap->authenticate($ldap->getLdapUserDN($username), $password);
		    if ($authenticated) {
			    if (empty($user)) $user = $ldap->addLdapUserToDatabase($username, $password);
		    }
		    $ldap->close();
	    } else {
		    $authenticated = $user->authenticate($password);
	    }

	    if($authenticated) {		
		    // Call on the Sessions subsystem to log the user into the site.
		    exponent_sessions_login($user);
		    //Update the last login timestamp for this user.
		    $user->updateLastLogin();
	    }
    }
    
    public function authenticate($password) {
	    if (MAINTENANCE_MODE && $this->is_acting_admin == 0 ) return false;  // if MAINTENANCE_MODE only allow admins
	    if (empty($this->id)) return false;  // if the user object is null then fail the login
	    // check password, if account is locked, or is admin(account locking doesn't to administrators)
	    return (($this->is_admin == 1 || $this->is_locked == 0) && $this->password == md5($password)) ? true : false;
    }
    
	public function updateLastLogin() {
		global $db, $user;
		$obj->id = $this->id;
		$obj->last_login = time();
		$db->updateObject($obj, 'user');
		//$this->update(array('last_login'=>time()));
	}

	public function isAdmin() {
		return (!empty($this->is_acting_admin) || !empty($this->is_admin)) ? true : false;
	}

	public function isSuperAdmin() {
		return $this->is_admin;
	}

    public function isActingAdmin() {
       return ($this->is_admin == false && $this->is_acting_admin == true) ? true : false;
	}
	
	public function isLoggedIn() {
		return (empty($this->id)) ? false : true;
	}

	private function getUserProfile() {
        global $db;
        if (!empty($this->id)) {
            $active_extensions = $db->selectObjects('profileextension', 'active=1');
            foreach ($active_extensions as $ext) {
                include_once($ext->classfile);
                $extension = new $ext->classname();
                $items = $db->selectObject($extension->tablename, 'user_id='.$this->id);
                if (!empty($items)) {
                    foreach($items as $key=>$item) {
                        if ($key != 'user_id') {
                            $this->$key = preg_match('/^([a-zA-Z]+):([0-9]+):{/', $item) ? unserialize($item) : $item;
                        }
                    }
                }
            }
        }
    }

	private function checkAdminFlags() {
		global $user;
		if (!empty($this->is_admin) && $user->is_admin == 0) $this->is_admin = 0;
        if (!empty($this->is_acting_admin) && $user->is_admin == 0) $this->is_acting_admin = 0;
	}

    public function setPassword($pass1, $pass2) {
        // make sure the password is good to go
        if (empty($pass1) || empty($pass2)) {
           return 'You must fill out both password fields.';
        } elseif ($pass1 != $pass2) {
           return 'Your passwords do not match';
        }
        
        if (strcasecmp($this->username,$pass1) == 0) {
		    return 'Your password cannot be the same as your username';
	    }
	    # For example purposes, the next line forces passwords to be over 8 characters long.
	    if (strlen($pass1) < 8) {
		    return 'Passwords must be at least 8 characters longs';
	    }
	    
	    // if we get here the password must be good
	    $this->password = $this->encryptPassword($pass1);
	    return true;
    }
    
	public function encryptPassword($password) {
		return md5($password);
	}
	
	public function getGroupMemberships() {
	    global $db, $user;
	    
	    // Don't have enough information to consult the membership tables. Return an empty array.
	    if (!$this->isLoggedIn()) return array();	    
	    
	    // For administrators, we synthesize group memberships - they effectively belong to all groups.  
	    if ($this->isAdmin()) return exponent_users_getAllGroups(true, true);
        
        $groups = array(); // Holding array for the groups.
	    foreach ($db->selectObjects('groupmembership','member_id='.$this->id) as $m) {
		    $groups[] = $db->selectObject('group','id='.$m->group_id);
	    }
	    
	    return $groups;
    }
    
	private function getsToolbar() {
	    global $db;
	    
	    if ($this->isAdmin()) return true;

	    // check check userpermissions to see if the user has the ability to edit anything
        if ($db->selectValue('userpermission','uid','uid=\''.$this->id.'\'')) return true;
	    
	    // check groups to see if this group has the ability to admin anything
	    foreach ($this->groups as $group) {
	        if ($db->selectValue('grouppermission','gid','gid=\''.$group->id.'\' AND permission!=\'view\'')) return true;
	    }
	    
	    return false;
    }
    
    public static function getByUsername($username) {
        global $db;
        $user = new user($db->selectValue('user', 'id', 'username="'.$username.'"'));
        return empty($user->id) ? false : $user;
    }
}

?>
