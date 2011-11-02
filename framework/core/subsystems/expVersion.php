<?php
/**
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @package    Framework
 * @subpackage Subsystems
 * @author     Adam Kessler <adam@oicgroup.net>
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @version    Release: @package_version@
 * @link       http://www.exponent-docs.org/api/package/PackageName
 */
/** @define "BASE" "../../.." */

class expVersion {
          
	/**
	 * Return a string of the current version number.
	 *
	 * @param bool $full Whether or not to return a full version number.  If passed as true,
	 *	a string in the form of '0.96.3-beta5' will be returned.  Otherwise, '0.96' would be returned.
	 * @param bool $build Whether or not to return the build date in the string.
	 * @node Subsystems:expVersion
	 * @param bool $full
	 * @param bool $build
	 * @return string
	 */
	static function getVersion($full = false, $build = false) {
		if (!defined('EXPONENT_VERSION_MAJOR')) include_once(BASE."exponent_version.php");
		$vers = EXPONENT_VERSION_MAJOR.".".EXPONENT_VERSION_MINOR;  // can be used for numerical comparison
		if ($full) {
			$vers .= ".".EXPONENT_VERSION_REVISION;
			if (EXPONENT_VERSION_TYPE != '') $vers .= "-".EXPONENT_VERSION_TYPE.EXPONENT_VERSION_ITERATION;
		}
		if ($build) {
			$vers .= " (Build Date: ".strftime("%D",EXPONENT_VERSION_BUILDDATE).")";
		}
		return $vers;
	}

	/**
	 * Routine to check for installation or upgrade
	 */
	static function checkVersion() {
		global $db;

		// we're not up and running yet, so fix that first
		if (@file_exists(BASE.'install/not_configured') || !(@file_exists(BASE.'conf/config.php'))) {
			self::launchInstaller();
		}

		// version checking routine, check database version against software version
		$dbversion = $db->selectObject('version',1);
		if (empty($dbversion)) {
			$dbversion->major = 0;
			$dbversion->minor = 0;
			$dbversion->revision = 0;
			$dbversion->type = '';
			$dbversion->iteration = '';
		}
		if ($dbversion->major < EXPONENT_VERSION_MAJOR) {
			self::launchInstaller();
		} elseif ($dbversion->minor < EXPONENT_VERSION_MINOR) {
			self::launchInstaller();
		} elseif ($dbversion->revision < EXPONENT_VERSION_REVISION) {
			self::launchInstaller();
		} elseif ($dbversion->minor < EXPONENT_VERSION_MINOR) {
			self::launchInstaller();
		} else {
			$dbtype = self::iterateType($dbversion->type);
			$swtype = self::iterateType(EXPONENT_VERSION_TYPE);
			if ($dbtype < $swtype) {
				self::launchInstaller();
			} elseif ($dbtype == $swtype && $dbversion->iteration < EXPONENT_VERSION_ITERATION) {
				self::launchInstaller();
			}
		}
	}

	/**
	 * Routine to launch exponent installer
	 */
	static function launchInstaller() {
		// we'll need the not_configured file
		if (!@file_exists(BASE.'install/not_configured')) {
			$nc_file = fopen(BASE.'install/not_configured', "w");
			fclose($nc_file);
		}

		header('Location: '.URL_FULL.'install/index.php');
		exit('Redirecting to the Exponent Install Wizard');
	}

	/**
	 * Routine to convert iteration type to a number
	 * 
	 * @param string $type
	 * @return int
	 */
	static function iterateType($type) {
		switch ($type) {
			case 'alpha':
				$typenum = 1;
				break;
			case 'beta':
				$typenum = 2;
				break;
			case 'release-candidate':
				$typenum = 3;
				break;
			case 'develop':
				$typenum = 5;
				break;
			case '': // stable
				$typenum = 10;
				break;
			default:
				$typenum = 0;
				break;
		}
		return $typenum;
	}

}
