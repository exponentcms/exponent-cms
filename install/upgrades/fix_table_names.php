<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * This is the class fix_table_names
 */
class fix_table_names extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.0.9';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Attempt to fix mixed case table names"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "On some server filesystems (Windows), some tables may lose their mixed case names.  This script attempts to rename those tables."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        return true;
	}

	/**
	 * attempts to rename the mixed case tables
	 * @return bool
	 */
	function upgrade() {
        $renamed = count(expDatabase::fix_table_names());
        return ($renamed?$renamed:gt('No')).' '.gt('tables were renamed.');
	}
}

?>
