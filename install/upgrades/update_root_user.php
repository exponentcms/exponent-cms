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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class update_root_user
 */
class update_root_user extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.7';  // code was corrected in 2.0.7

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Implements 'root' user"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.0.3, there was only one Super-Admin. However, there is a need for a single admin user of higher order to always be available in the system.  This Script flags that user"; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

        return $db->selectObject('user',"is_system_user = '1'") == null ? true : false;
	}

	/**
	 * converts the main/root super-admin to be marked as such
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// Get the current and first super-admin
	    $rootuser = $db->selectObject('user',"is_admin = '1'");
        if (!$rootuser->is_system_user) {
            $rootuser->is_system_user = 1;
            $db->updateObject($rootuser,'user');
            return gt('Root user created.');
        } else {
            return gt('Root user already exists.');
        }
	}
}

?>
