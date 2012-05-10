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
 * This is the class version_tracking
 */
class version_tracking extends upgradescript {
	protected $from_version = '1.99.0';
//	protected $to_version = '99';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return gt("Update Version Information"); }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return gt("The system keeps track of its version for upgrades"); }

	/**
	 * additional test(s) to see if upgrade script should be run
	 *
	 * @return bool
	 */
	function needed() {
		return true;
	}

	/**
	 * adds or updates version tracking information in database
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// version tracking
		$db->delete('version',1);  // clear table of old accumulated entries
		$vo = new stdClass();
		$vo->major = EXPONENT_VERSION_MAJOR;
		$vo->minor = EXPONENT_VERSION_MINOR;
		$vo->revision = EXPONENT_VERSION_REVISION;
		$vo->type = EXPONENT_VERSION_TYPE;
		$vo->iteration = EXPONENT_VERSION_ITERATION;
		$vo->builddate = EXPONENT_VERSION_BUILDDATE;
		$vo->created_at = time();
		$ins = $db->insertObject($vo,'version') or die($db->error());
        return $ins ? gt('Database updated to version').' '.expVersion::getVersion(true) : gt('Failed');
	}
}

?>
