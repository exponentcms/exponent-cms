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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class fix_sectionref
 */
class fix_sectionref extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.0.9';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Attempt to fix the sectionref table"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "On some sites the sectionref table has incorrect entries which can cause permissions issues.  This script attempts to rebuild that table."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        return true;
	}

	/**
	 * attempts to rebuild the sectionref table
	 * @return bool
	 */
	function upgrade() {
        return navigationController::rebuild_sectionrefs();
	}
}

?>
