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
 * This is the class expLDAP
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */

class expLDAP {

    public $connection = false;

    function __construct($server = '') {
        if (!empty($this->connection)) self::close();
        if (empty($server) && !defined(LDAP_SERVER)) $this->connection = false;
        $ldap_server = empty($server) ? LDAP_SERVER : $server;
        $this->connection = @ldap_connect($ldap_server);
        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    }

    function __destruct() {
        self::close();
    }

    public function authenticate($username, $password) {
        if (empty($password) || empty($username)) return false; //if the password isn't set, return false to safeguard against anon login
        return self::bind($username, $password);
    }

    public function getLdapUser($username) {
        // figure out our context for searching
        $search_context = empty($context) ? LDAP_BASE_CONTEXT : $context;

        // look up the LDAP user object
//		$results = @ldap_search($this->connection,$search_context, "cn=".$username);
        $results = @ldap_search($this->connection, $search_context, "sAMAccountName=" . $username);
        if ($this->errno() && DEVELOPMENT) {
            flash('error', $this->error());
        }
        $info = @ldap_get_entries($this->connection, $results);

        return ($info['count'] > 0) ? $info[0] : array();
    }

    public function getLdapUserContext($username) {
        if (empty($username)) return "";

        $user = self::getLdapUser($username);
        if (empty($user['dn'])) return '';

        return substr_replace($user['dn'], '', 0, stripos($user['dn'], ',') + 1);
    }

    public function getLdapUserDN($username) {
        if (empty($username)) return "";
        $user = self::getLdapUser($username);
        return empty($user['dn']) ? '' : $user['dn'];
    }

//	public function addLdapUserToDatabase($username, $password, $context) {
    public function addLdapUserToDatabase($username, $password) {
        // figure out our context for searching
        $user = self::getLdapUser($username);

        // populate user data
        if (!empty($user)) {
            $userdata = array('username'  => $username,
                              'password'  => $password,
                              'firstname' => $user['givenname'][0],
                              'lastname'  => $user['sn'][0],
                              'email'     => $user['mail'][0],
                              'is_ldap'   => 1);
            $newuser = new user($userdata);
            $newuser->update();
            return $newuser;
        } else {
            return null;
        }
    }

//    public function connectAndBind($server = '', $username = "", $password = "", $context = '') {
//        self::connect($server);
//        self::bind($username, $password, $context);
//    }

//    public function connect($server = '') {
//        if (!empty($this->connection)) self::close();
//        if (empty($server) && !defined(LDAP_SERVER)) $this->connection = false;
//        $ldap_server = empty($server) ? LDAP_SERVER : $server;
//        $this->connection = @ldap_connect($ldap_server);
//        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
//        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
//    }

    public function bind($username = '', $password = "") {
        if (!empty($this->connection)) {
            $bind_username = empty($username) ? LDAP_BIND_USER : $username;
            $bind_password = empty($password) ? LDAP_BIND_PASS : $password;
            return @ldap_bind($this->connection, $bind_username, $bind_password);
        }
    }

    public function close() {
        if (!empty($this->connection)) @ldap_close($this->connection);
    }

    public function errno() {
        return ldap_errno($this->connection);
    }

    public function error() {
        return ldap_error($this->connection);
    }

    /**
     * Attempts to update Exponent 'ldap' user profiles using ldap server data
     */
    public function syncLDAPUsers() {
        $usr = new user();
        $ldap_users = $usr->find('all', 'is_ldap=1');
        if (!empty($ldap_users)) {
            self::bind();
            foreach ($ldap_users as $ldap_user) {
                $newuser = new user(array('username' => $ldap_user->username));
                $user = self::getLdapUser($ldap_user->username);
                $userdata = array(
                    'firstname' => $user['givenname'][0],
                    'lastname'  => $user['sn'][0],
                    'email'     => $user['mail'][0]
                );
                $newuser->update($userdata);
            }
            return count($ldap_users);
        }
    }

}

?>