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
	function description() { return "Beginning with Exponent 2.0.0 Beta3, the system begins keeping track of its versions and upgrades."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @param $version
	 * @return bool
	 */
	function needed($ver) {
	    global $db;
		return true;
//		// we'll run when versions are equal since we may be doing an iteration update
//		$version = $db->selectObject('version',1);
//		if (empty($version)) {
//			$version->major = 0;
//			$version->minor = 0;
//			$version->revision = 0;
//			$version->type = '';
//			$version->iteration = '';
//		}
//		if ($version->major < EXPONENT_VERSION_MAJOR) {
//			return true;
//		} elseif ($version->minor < EXPONENT_VERSION_MINOR) {
//			return true;
//		} elseif ($version->revision < EXPONENT_VERSION_REVISION) {
//			return true;
//		} elseif ($version->minor < EXPONENT_VERSION_MINOR) {
//			return true;
//		} else {
//			switch ($version->type) {
//				case 'alpha':
//					$dbtype = 1;
//					break;
//				case 'beta':
//					$dbtype = 2;
//					break;
//				case 'release candidate':
//					$dbtype = 3;
//					break;
//				case 'develop':
//					$dbtype = 5;
//					break;
//				case '': // stable
//					$dbtype = 10;
//					break;
//				default:
//					$dbtype = 0;
//					break;
//			}
//			switch (EXPONENT_VERSION_TYPE) {
//				case 'alpha':
//					$swtype = 1;
//					break;
//				case 'beta':
//					$swtype = 2;
//					break;
//				case 'release candidate':
//					$swtype = 3;
//					break;
//				case 'develop':
//					$swtype = 5;
//					break;
//				case '': // stable
//					$swtype = 10;
//					break;
//				default:
//					$swtype = 0;
//					break;
//			}
//			if ($dbtype < $swtype) {
//				return true;
//			} elseif ($dbtype == $swtype && $version->type < EXPONENT_VERSION_ITERATION) {
//				return true;
//			}
//		}
//		return false;
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
