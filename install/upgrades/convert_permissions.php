<?php

##################################################
#
# Copyright (c) 2004-2017 OIC Group, Inc.
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
 * This is the class convert_permissions
 */
class convert_permissions extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.5';  // permissions names were changed in 2.0.5
    public $priority = 3; // set this to a high priority

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Change 1.0 module permissions to the 2.0 common naming standard"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.0.5, old school module permissions had various names.
	    These were updated to the standard permission names used across all the modules."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // we'll just do it in every instance instead of testing
	}

	/**
	 * converts all permission names into the new standardized ones
	 * @return string
	 */
	function upgrade() {
	    global $db;

		// convert each old school permission name to its 2.0 equivalent
        $perms = array(
            'administrate'=>'manage',
            'post'=>'create',
            'add_module'=>'create',
            'edit_module'=>'edit',
            'delete_module'=>'delete',
            'order_modules'=>'configure',
        );
        foreach ($perms as $oldperm=>$newperm) {
            $db->columnUpdate('userpermission',"permission",$newperm,"permission='".$oldperm."'");
            $db->columnUpdate('grouppermission',"permission",$newperm,"permission='".$oldperm."'");
	    }

        return gt('Old-School Permissions converted to the new format.');
	}
}

?>
