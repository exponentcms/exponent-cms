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
    }

    function __destruct() {
        self::close();
    }

    public function authenticate($context, $password) {
        if (empty($password) || empty($context)) return false; //if the password isn't set, return false to safeguard against anon login
        return self::bind($context, $password);
    }

    public function getLdapUser($username) {
        // figure out our context for searching
        $search_context = empty($context) ? LDAP_BASE_DN : $context;

        // look up the LDAP user object
//		$results = @ldap_search($this->connection,$search_context, "cn=".$username);
        $results = @ldap_search($this->connection, $search_context, "sAMAccountName=" . $username);
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
//			return exponent_users_create($userdata); //FIXME function was deprecated
            $newuser = new user($userdata);
            $newuser->update();
            return $newuser;
        } else {
            return null;
        }
    }

    public function connectAndBind($server = '', $username = "", $password = "", $context = '') {
        self::connect($server);
        self::bind($username, $password, $context);
    }

    public function connect($server = '') {
        if (!empty($this->connection)) self::close();

        if (empty($server) && !defined(LDAP_SERVER)) $this->connection = false;

        $ldap_server = empty($server) ? LDAP_SERVER : $server;
        $this->connection = @ldap_connect($ldap_server);
        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    }

    public function bind($context = '', $password = "") {
        if (!empty($this->connection)) {
            $bind_context = empty($context) ? LDAP_BIND_USER : $context;
            return @ldap_bind($this->connection, $bind_context, $password);
        }
    }

    public function close() {
        if (!empty($this->connection)) @ldap_close($this->connection);
    }
}

?>