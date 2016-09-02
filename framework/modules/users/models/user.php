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
 * @subpackage Models
 * @package    Modules
 */

class user extends expRecord {
    public $validates = array(
        'presence_of'    => array(
            'firstname' => array('message' => 'First name is a required field.'),
            'lastname'  => array('message' => 'Last name is a required field.'),
        ),
        'is_valid_email' => array(
            'email' => array('message' => 'The email address does not appear to be valid')
        ),
        'uniqueness_of'  => array(
            'username' => array('message' => 'That username is already taken. Please use another username.')
        ),
        'length_of'      => array(
            'username' => array('length' => 3)
        ),
    );

    function __construct($params = null, $get_assoc = false, $get_attached = false) {
        if (is_array($params) && isset($params['pass1']))
            $params['password'] = user::encryptPassword($params['pass1']);
        parent::__construct($params, $get_assoc, $get_attached);
        $this->getUserProfile();
        $this->groups = $this->getGroupMemberships();
        $this->getsToolbar = $this->getsToolbar();
    }

    public function update($params=array()) {
        if (!isset($params['is_admin'])) $params['is_admin'] = 0;
        if (!isset($params['is_acting_admin'])) $params['is_acting_admin'] = 0;
        parent::update($params);
    }

    public function save($overrideUsername = false, $force_no_revisions = false) {
        global $user;

        // if someone is trying to make this user an admin, lets make sure they have permission to do so.
        if (!empty($this->is_admin) && !$user->isAdmin()) $this->is_admin = 0;
        if (!empty($this->is_acting_admin) && !$user->isAdmin()) $this->is_acting_admin = 0;
        if (!empty($this->is_admin)) $this->is_acting_admin = 1;

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
//        if (empty($user->id)) return false;  //FIXME will be empty for new ldap user

        // try to authenticate the user - use the authentication type specified in the site config
        if (USE_LDAP == 1 && !function_exists('ldap_connect') && DEVELOPMENT) {
            flash('error', gt('LDAP support is not enabled for PHP!'));
        }
        if (USE_LDAP == 1 && (empty($user->id) || $user->is_ldap == 1) && function_exists('ldap_connect')) {
            $ldap = new expLDAP();
//            $ldap->connect();
//            $authenticated = $ldap->authenticate($ldap->getLdapUserDN($username), $password);
            $authenticated = $ldap->authenticate($username.'@'.LDAP_BASE_DN, $password);
            if ($ldap->errno() && DEVELOPMENT) {
                flash('error', $ldap->error());
            }
            if ($authenticated && empty($user->id)) {
                $user = $ldap->addLdapUserToDatabase($username, $password);
            }
            $ldap->close();
        } else {
            $authenticated = $user->authenticate($password);
        }

        if ($authenticated) {
            // Call on the Sessions subsystem to log the user into the site.
            expSession::login($user);
            //Update the last login timestamp for this user.
            $user->updateLastLogin();
        }
        if (ENABLE_TRACKING) {
            // save user login attempts
    		$obj = new stdClass();
    		$obj->user_id = $user->id;
    		$obj->timestamp = time();
    		$obj->ip_address = self::getRealIpAddr();
    		$obj->authenticated = $authenticated;
    		$db->insertObject($obj, "user_loginAttempts");
        }

//		return $user;
    }

    public function authenticate($password) {
        if (MAINTENANCE_MODE && !$this->isAdmin())
            return false; // if MAINTENANCE_MODE only allow admins
        if (empty($this->id))
            return false; // if the user object is null then fail the login
        // check password, if account is locked, or is admin(account locking doesn't to administrators)
//        return (($this->is_admin == 1 || $this->is_locked == 0) && $this->password == user::encryptPassword($password)) ? true : false;

        // fallback if md5() correct but crypt() incorrect, then auto-update password once
        if ($this->is_admin == 1 || $this->is_locked == 0) {
            if ($this->password == user::encryptPassword($password, $this->password)) {
                return true;
            } elseif ($this->password == md5($password)) {
                // convert old md5() password hash to more secure crypt() blowfish hash
                $params['password'] = user::encryptPassword($password);
                $params['is_admin'] = !empty($this->is_admin);
                $params['is_acting_admin'] = !empty($this->is_acting_admin);
                $this->update($params);
                return true;
            }
        }
        return false;
    }

    public function updateLastLogin() {
//        global $db, $user;
        global $db;

        $obj = new stdClass();
        $obj->id = $this->id;
        $obj->last_login = time();
        $db->updateObject($obj, 'user', 'id=' . $obj->id, 'uid');
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
     * Is the user the system (root) admin?
     *
     * @return bool
     */
    public function isSystemAdmin() {
        return $this->is_system_user;
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

    public function getUserProfile() {
        global $db;

        if (!empty($this->id)) {
            $active_extensions = $db->selectObjects('profileextension', 'active=1');
            foreach ($active_extensions as $ext) {
                include_once(BASE . $ext->classfile);
                $extension = new $ext->classname();
                $data = $db->selectObjects($extension->tablename, 'user_id=' . $this->id);
                if (!empty($data)) {
                    foreach ($data as $items) {
                        foreach ($items as $key => $item) {
                            if ($key === 'expeAlerts_id') {
                                $this->expeAlerts[] = $db->selectObject('expeAlerts', 'id=' . $item);
                            } elseif ($key != 'user_id') {
                                $this->$key = preg_match('/^([a-zA-Z]+):([0-9]+):{/', $item) ? unserialize($item) : $item;
                            }
                        }
                    }
                }
            }
        }
    }

    public function setPassword($pass1, $pass2) {
        // make sure the password is good to go
        if (empty($pass1) || empty($pass2)) {
            return gt('You must fill out both password fields.');
        } elseif ($pass1 != $pass2) {
            return 'The passwords do not match';
        }

        if (strcasecmp($this->username, $pass1) == 0) {
            return gt('The password cannot be the same as the username');
        }
        # For example purposes, the next line forces passwords to be over 8 characters long.
//        if (strlen($pass1) < MIN_PWD_LEN) {
//            return gt('Passwords must be at least') . ' ' . MIN_PWD_LEN . ' ' . gt('characters long');
//        }
        $pw_check = expValidator::checkPasswordStrength($pass1);
        if (!empty($pw_check)) {
            return $pw_check;
        }

        // if we get here the password must be good
        $this->password = user::encryptPassword($pass1);
        return true;
    }

    /**
     * Generate password hash
     *
     * @param $password
     * @return string
     */
    public static function encryptPassword($password, $salt = null) {
        if (!defined('NEW_PASSWORD') || !NEW_PASSWORD) {
            return md5($password);
        } else {
            if (empty($salt)) {
                if (is_integer(NEW_PASSWORD) && NEW_PASSWORD > 0) {
                    $cost = NEW_PASSWORD;
                } else {
                    $cost = 10;
                }
                $salt = substr(base64_encode(openssl_random_pseudo_bytes(17)), 0, 22);
                $salt = str_replace("+", ".", $salt);
                $salt = '$' . implode(
                        '$',
                        array(
                            "2y",
                            str_pad($cost, 2, "0", STR_PAD_LEFT),
                            $salt
                        )
                    );
            }
            return crypt($password, $salt);
        }
    }

    public function getGroupMemberships() {
//        global $db, $user;
        global $db;

        // Don't have enough information to consult the membership tables. Return an empty array.
        if (!$this->isLoggedIn()) return array();

        // For administrators, we synthesize group memberships - they effectively belong to all groups.
        if ($this->isAdmin()) return group::getAllGroups();

        $groups = array(); // Holding array for the groups.
        foreach ($db->selectObjects('groupmembership', 'member_id=' . $this->id) as $m) {
            $groups[] = $db->selectObject('group', 'id=' . $m->group_id);
        }

        return $groups;
    }

    private function getsToolbar() {
        global $db;

        if ($this->isAdmin()) return true;
        if ($this->globalPerm('hide_slingbar')) return false;

        //FIXME who should get a slingbar? any non-view permissions? new group setting?
        // check userpermissions to see if the user has the ability to edit anything
        if (!empty($this->id) && $db->selectValue('userpermission', 'uid', 'uid=\'' . $this->id . '\' AND permission!=\'view\'')) return true;
        // check groups to see if assigned groups have the ability to edit anything
        foreach ($this->groups as $group) {
            if ($db->selectValue('grouppermission', 'gid', 'gid=\'' . $group->id . '\' AND permission!=\'view\'')) return true;
        }

        return false;
    }

    public function isTempUser() {
        return is_numeric(expUtil::right($this->username, 10)) ? true : false;
    }

    function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function customerInfo() {
        global $db;

        // build out a SQL query that gets all the data we need and is sortable.
        $sql = 'SELECT o.*, b.firstname as firstname, b.billing_cost as total, b.middlename as middlename, b.lastname as lastname, os.title as status, ot.title as order_type ';
        $sql .= 'FROM ' . $db->prefix . 'orders o, ' . $db->prefix . 'billingmethods b, ';
        $sql .= $db->prefix . 'order_status os, ';
        $sql .= $db->prefix . 'order_type ot ';
        $sql .= 'WHERE o.id = b.orders_id AND o.order_status_id = os.id AND o.order_type_id = ot.id AND o.purchased > 0 AND user_id =' . $this->id;
        $customer = new stdClass();
        $customer->records = $db->selectObjectsBySql($sql);
        $customer->total_orders = count($customer->records);
        $customer->total_spent = 0;
        foreach ($customer->records as $invoice) {
            $customer->total_spent += $invoice->grand_total;
        }
        return $customer;
    }

    /** exdoc
     * Gets a list of all user accounts in the system.  By giving different
     * combinations of the two boolean arguments. three different lists
     * of users can be returned.  Returns a list of users, according to the two parameters passed in.
     *
     * @param bool|int $allow_admin  Whether or not to include admin accounts in the returned list.
     * @param bool|int $allow_normal Whether or not to include normal accounts in the returned list.
     *
     * @return array
     */
    public static function getAllUsers($allow_admin = 1, $allow_normal = 1) {
        global $db;

        if ($allow_admin && $allow_normal) return $db->selectObjects('user');
        else if ($allow_admin) return $db->selectObjects('user', 'is_admin=1 OR is_acting_admin = 1');
        else if ($allow_normal) return $db->selectObjects('user', 'is_admin=0 AND is_acting_admin = 0');
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
     *
     * @return user
     * @node Model:User
     */
    public static function getUserByName($name) {
        global $db;

        $tmpu = $db->selectObject('user', "username='$name'");
        if ($tmpu && $tmpu->is_admin == 1) {
            // User is an admin.  Update is_acting_admin, just in case.
            $tmpu->is_acting_admin = 1;
        }
        return $tmpu;
    }

    /** exdoc
     * This function pulls a user object from the subsystems storage mechanism,
     * according to the email.  For the default implementation, this is equivalent
     * to a $db->selectObject() call, but it may not be the same for other implementations.
     * Returns a basic user object, and null if no user was found.
     *
     * This function does NOT perform user caching like the getUserById
     * function does.  Multiple calls to retrieve the same user result in multiple calls
     * to the database.
     *
     * @param string $name The username of the user account to retrieve.
     *
     * @return user
     * @node Model:User
     */
    public static function getUserByEmail($name) {
        global $db;

        $tmpu = $db->selectObject('user', "email='$name'");
        if ($tmpu) {
            $tmpu->count = $db->countObjects('user', "email='$name'");
            if ($tmpu->is_admin == 1) {
                // User is an admin.  Update is_acting_admin, just in case.
                $tmpu->is_acting_admin = 1;
            }
        }
        return $tmpu;
    }

    /**
     * simple function to return the user's email
     *
     * @static
     *
     * @param integer $id
     *
     * @return string
     */
    public static function getEmailById($id) {
        global $db;

        return $db->selectValue('user', 'email', 'id=' . $id);
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
     *
     * @return user
     * @node Model:User
     */
    public static function getUserById($uid) {
        // Pull in the exclusive global variable $SYS_USERS_CACHE
        global $SYS_USERS_CACHE;

        if (!isset($SYS_USERS_CACHE[$uid])) {
            // If we haven't previously retrieved an object for this ID, pull it out from
            // the database and stick it in the cache array, for future calls.
            global $db;
            $tmpu = $db->selectObject('user', 'id=' . $uid);
            if ($tmpu && $tmpu->is_admin == 1) {
                // User is an admin.  Update is_acting_admin, just in case.
                $tmpu->is_acting_admin = 1;
            }
            $SYS_USERS_CACHE[$uid] = $tmpu;
        }
        // Regardless of whether or not the user had been retrieved prior to the calling of
        // this function, it is now in the cache array.
        return $SYS_USERS_CACHE[$uid];
    }

    /**
     * simple function to return the user's attribution based on system setting
     *
     * @static
     *
     * @param integer $id
     * @param string  $display
     *
     * @return string
     */
    public static function getUserAttribution($id, $display=DISPLAY_ATTRIBUTION) {
        $u = new user($id);
        if (!empty($u->id)) {
            switch ($display) {
             case "firstlast":
                 $str = $u->firstname . " " . $u->lastname;
                 break;
             case "lastfirst":
                 $str = $u->lastname . ", " . $u->firstname;
                 break;
             case "first":
                 $str = $u->firstname;
                 break;
             case "last":
                 $str = $u->lastname;
                 break;
             case "username":
             default:
                 $str = $u->username;
                 break;
            }
            $str = trim($str);
            if (empty($str)) {
                $str = $u->username;
            }
        } else {
            $str = gt('Anonymous User');
        }
        return $str;
    }

    /**
     * This function determines a group global permission/restriction for this user
     *
     * @param $perm
     *
     * @return bool
     */
    public function globalPerm($perm) {
        if ($this->isAdmin()) return false;  //fixme might cause issues, admin users never tax exempt?
//        $groups = $this->getGroupMemberships();
        foreach ($this->groups as $group) {
            if (!empty($group->$perm)) {
                return true;
            }
        }
        return false;
    }
}

?>