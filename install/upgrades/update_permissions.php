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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class update_permissions
 */
class update_permissions extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.3.0';  // controller names were changed in 2.2.0
    public $priority = 4; // set this to a high priority

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update user and group permissions to correct module name"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.2.0, modules were reference using a full name w/ 'Controller', but now use the short name.
	Sometimes this update was not accomplished in the v2.2.0 upgrade."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // we'll just do it in every instance instead of testing if old name exist
	}

	/**
	 * converts all permission names into the new standardized ones
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// convert each old style module name to its short form
        $count = 0;
        foreach ($db->selectObjects('grouppermission',"module LIKE '%Controller%'") as $gp) {
            $old_gp_mod = $gp->module;
            $gp->module = expModules::getModuleName($gp->module);  // convert module name to 2.0 style
            $db->updateObject($gp,'grouppermission',"module = '". $old_gp_mod . "' AND source = '".$gp->source."' AND permission = '".$gp->permission."' AND internal = '".$gp->internal."'",'gid');
            $count++;
	    }
        foreach ($db->selectObjects('userpermission',"module LIKE '%Controller%'") as $up) {
            $old_up_mod = $up->module;
            $up->module = expModules::getModuleName($up->module);  // convert module name to 2.0 style
            $db->updateObject($up,'userpermission',"module = '". $old_up_mod . "' AND source = '".$up->source."' AND permission = '".$up->permission."' AND internal = '".$up->internal."'",'uid');
            $count++;
	    }

        return ($count?$count:gt('No'))." ".gt('Old permission module names were updated to the short format.');
	}
}

?>
