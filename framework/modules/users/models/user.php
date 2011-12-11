<?php

/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the user class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */

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
			    'username'=>array('message'=>'That username is already taken. Please use another username.')
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
        $this->checkAdminFlags();
        
        // if the site is configured to use the email addy as the username we need to force the 
        // the email address into the username field.
        if (USER_REGISTRATION_USE_EMAIL == 1 && !empty($this->email) && $overrideUsername == false) $this->username = $this->email;
        parent::save();        
    }
	
	public static function login($username, $password) {
        global $db, $user;

	    // Retrieve the user object from the database.  This may be null, if the username is non-existent.
	    $user = new user($db->selectValue('user', 'id', "username='" . $username . "'"));
	
	    // if the user object doesn't have an id then we didn't find a valid user account with this username
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
		    expSession::login($user);
		    //Update the last login timestamp for this user.
		    $user->updateLastLogin();
	    }
//		$obj = new stdClass();
//		$obj->user_id = $user->id;
//		$obj->timestamp = time();
//		$obj->ip_address = exponent_users_getRealIpAddr();
//		$obj->authenticated = $authenticated;
//		$db->insertObject($obj, "user_loginAttempts");
//
//		return $user;
    }
    
    public function authenticate($password) {
	    if (MAINTENANCE_MODE && $this->is_acting_admin == 0 ) return false;  // if MAINTENANCE_MODE only allow admins
	    if (empty($this->id)) return false;  // if the user object is null then fail the login
	    // check password, if account is locked, or is admin(account locking doesn't to administrators)
	    return (($this->is_admin == 1 || $this->is_locked == 0) && $this->password == md5($password)) ? true : false;
    }
    
	public function updateLastLogin() {
		global $db, $user;

		$obj = null;
		$obj->id = $this->id;
		$obj->last_login = time();
		$db->updateObject($obj, 'user');
		//$this->update(array('last_login'=>time()));
	}

	/**
	 * Is the user either a super admin or an acting admin?
	 *
	 * @return bool
	 */
	public function isAdmin() {
		return (!empty($this->is_acting_admin) || !empty($this->is_admin)) ? true : false;
	}

	/**
	 * Is the user a super admin?
	 *
	 * @return bool
	 */
	public function isSuperAdmin() {
		return $this->is_admin;
	}

	/**
	 * Is the user an acting admin and NOT a super admin?
	 *
	 * @return bool
	 */
	public function isActingAdmin() {
       return ($this->is_admin == false && $this->is_acting_admin == true) ? true : false;
	}

	/**
	 * Is the user logged on
	 *
	 * @return bool
	 */
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

	/**
	 * Set user object admin flags
	 */
	private function checkAdminFlags() {
		global $user;

		if (!empty($this->is_admin) && $user->isAdmin) $this->is_admin = 0;
        if (!empty($this->is_acting_admin) && $user->isAdmin) $this->is_acting_admin = 0;
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
	    if ($this->isAdmin()) return group::getAllGroups(true, true);
        
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
    
    public static function getByUsername($username) {  //FIXME this is a duplicate of getUserByName - deprecate
        global $db;

        $user = new user($db->selectValue('user', 'id', 'username="'.$username.'"'));
        return empty($user->id) ? false : $user;
    }
    
    public function isTempUser()
    {
        return is_numeric(expUtil::right($this->username,10)) ? true : false;
    }

	public static function getEmailById($id) {
		global $db;

		return $db->selectValue('user','email','id='.$id);
	}

	/** exdoc
	 * Gets a list of all user accounts in the system.  By giving different
	 * combinations of the two boolean arguments. three different lists
	 * of users can be returned.  Returns a list of users, according to the two parameters passed in.
	 *
	 * @param bool|int $allow_admin Whether or not to include admin accounts in the returned list.
	 * @param bool|int $allow_normal Whether or not to include normal accounts in the returned list.
	 * @return array
	 */
	public static function getAllUsers($allow_admin=1,$allow_normal=1) {
		global $db;

		if ($allow_admin && $allow_normal) return $db->selectObjects('user');
		else if ($allow_admin) return $db->selectObjects('user','is_admin=1 OR is_acting_admin = 1');
		else if ($allow_normal) return $db->selectObjects('user','is_admin=0 AND is_acting_admin = 0');
		else return array();
	}

	/** exdoc
	 * This function pulls a user object from the subsystems storage mechanism,
	 * according to the username.  For the default implementation, this is equivalent
	 * to a $db->selectObject() call, but it may not be the same for other implementations.
	 * Returns a basic user object, and null if no user was found.
	 *
	 * This function does NOT perform user caching like the getUserById
	 * function does.  Multiple calls to retrieve the same user result in multiple calls
	 * to the database.
	 *
	 * @param string $name The username of the user account to retrieve.
	 * @return \user
	 * @node Model:User
	 */
	public static function getUserByName($name) {
		global $db;

		$tmpu = $db->selectObject('user',"username='$name'");
		if ($tmpu && $tmpu->is_admin == 1) {
			// User is an admin.  Update is_acting_admin, just in case.
			// This can be removed as soon as 0.95 is deprecated.
			$tmpu->is_acting_admin = 1;
		}
		return $tmpu;
	}

	/** exdoc
	 * This function pulls a user object from the subsystems storage mechanism
	 * according to its ID.  For the default implementation, this is equivalent to a
	 * $db->selectObject() call, but it may not be the same for other implementations.
	 * Returns a basic user object, and null if no user was found.
	 *
	 * This function uses the exclusive global variable $SYS_USERS_CACHE to cache
	 * previously retrieved user accounts, so that subsequent requests for the same user
	 * object do not result in another trip to the database engine.
	 *
	 * @param integer $uid The id of the user account to retrieve.
	 * @return \user
	 * @node Model:User
	 */
	public static function getUserById($uid) {
		// Pull in the exclusive global variable $SYS_USERS_CACHE
		global $SYS_USERS_CACHE;

		if (!isset($SYS_USERS_CACHE[$uid])) {
			// If we haven't previously retrieved an object for this ID, pull it out from
			// the database and stick it in the cache array, for future calls.
			global $db;
			$tmpu = $db->selectObject('user','id='.$uid);
			if ($tmpu && $tmpu->is_admin == 1) {
				// User is an admin.  Update is_acting_admin, just in case.
				// This can be removed as soon as 0.95 is deprecated.
				$tmpu->is_acting_admin = 1;
			}
			$SYS_USERS_CACHE[$uid] = $tmpu;
		}
		// Regardless of whether or not the user had been retrieved prior to the calling of
		// this function, it is now in the cache array.
		return $SYS_USERS_CACHE[$uid];
	}

}

?>
