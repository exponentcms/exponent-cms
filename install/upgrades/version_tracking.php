<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the version_tracking class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */

/**
 * This is the class version_tracking
 *
 * @subpackage Upgrade
 * @package Installation
 */
class version_tracking extends upgradescript {
	protected $from_version = '1.99.0';
//	protected $to_version = '99';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	function name() { return "Install Version Tracking"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Beginning with Exponent 2.0.0 Beta3, the system keeps track of its versions and upgrades."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @param $version
	 * @return bool
	 */
	function needed($ver) {
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
		$vo = null;
		$vo->major = EXPONENT_VERSION_MAJOR;
		$vo->minor = EXPONENT_VERSION_MINOR;
		$vo->revision = EXPONENT_VERSION_REVISION;
		$vo->type = EXPONENT_VERSION_TYPE;
		$vo->iteration = EXPONENT_VERSION_ITERATION;
		$vo->builddate = EXPONENT_VERSION_BUILDDATE;
		$vo->created_at = time();
		$ins = $db->insertObject($vo,'version') or die($db->error());
        return $ins ? gt('Success') : gt('Failed');
	}
}

?>
