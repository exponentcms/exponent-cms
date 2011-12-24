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
	 *	a string in the form of '2.0.3-beta5' will be returned.  Otherwise, '2.0' would be returned.
	 * @param bool $build Whether or not to return the build date in the string.
	 * @param bool $full
	 * @param bool $build
     *
	 * @return string
     *
     * @node Subsystems:expVersion
	 */
	public static function getVersion($full = false, $build = false) {
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
	public static function checkVersion() {
		global $db, $user;

        $swversion->major = EXPONENT_VERSION_MAJOR;
        $swversion->minor = EXPONENT_VERSION_MINOR;
        $swversion->revision = EXPONENT_VERSION_REVISION;
        $swversion->type = EXPONENT_VERSION_TYPE;
        $swversion->iteration = EXPONENT_VERSION_ITERATION;
        $swversion->builddate = EXPONENT_VERSION_BUILDDATE;

		// check database version against installed software version
        if ($db->havedb) {
            if ($user->isAdmin()) {
                $dbversion = $db->selectObject('version',1);
                if (empty($dbversion)) {
                    $dbversion->major = 0;
                    $dbversion->minor = 0;
                    $dbversion->revision = 0;
                    $dbversion->type = '';
                    $dbversion->iteration = '';
                }
                // check if software version is newer than database version
                if (self::compareVersion($dbversion,$swversion)) {
                    $oldvers = $dbversion->major.'.'.$dbversion->minor.'.'.$dbversion->revision.($dbversion->type?$dbversion->type:'').($dbversion->iteration?$dbversion->iteration:'');
                    $newvers = $swversion->major.'.'.$swversion->minor.'.'.$swversion->revision.($swversion->type?$swversion->type:'').($swversion->iteration?$swversion->iteration:'');
                    flash('message',gt('The database requires upgrading from').' v'.$oldvers.' '.gt('to').' v'.$newvers.
                        '<br><a href="'.makelink(array("controller"=>"administration","action"=>"install_exponent")).'">'.gt('Click here to Upgrade your website').'</a>');
                }
            }
        } else {
            // database is unavailable, so show us as being offline
            $template = new standalonetemplate('_maintenance');
            $template->assign("db_down",true);
           	$template->output();
            exit();
        }

        // check if online version is newer than installed software version, but only once per session
        if ($user->isAdmin()) {
            if (!expSession::is_set('update-check')) {
                $onlineVer = self::getOnlineVersion();
                expSession::set('update-check','1');
                if (self::compareVersion($swversion,$onlineVer)) {
                    $newvers = $onlineVer->major.'.'.$onlineVer->minor.'.'.$onlineVer->revision.($onlineVer->type?$onlineVer->type:'').($onlineVer->iteration?$onlineVer->iteration:'');
                    flash('message',gt('A newer version of Exponent is available').', v'.$newvers.' '.gt('was released').' '.expDateTime::format_date($onlineVer->builddate).
                        '<br><a href="https://github.com/exponentcms/exponent-cms/downloads" target="_blank">'.gt('Click here to see Downloads').'</a>');
                }
            }
        }
	}

    /**
     * Routine to compare passed versions
     *
     * @param object $version1
     * @param object $version2
     *
     * @return bool set to true if $version1 is less than $version2
     */
    private static function compareVersion($version1, $version2) {
        if ($version1->major < EXPONENT_VERSION_MAJOR) {
            return true;
        } elseif ($version1->major == $version2->major && $version1->minor < $version2->minor) {
            return true;
        } elseif ($version1->major == $version2->major && $version1->minor == $version2->minor && $version1->revision < $version2->revision) {
            return true;
        } elseif ($version1->major == $version2->major && $version1->minor == $version2->minor && $version1->revision == $version2->revision) {
            $vertype = self::iterateType($version1->type);
            $swtype = self::iterateType($version2->type);
            if ($vertype < $swtype) {
                return true;
            } elseif ($vertype == $swtype && $version1->iteration < $version2->iteration) {
                return true;
            }
        }
        return false;
    }

    /**
     * Routine to obtain online version information
     *
     * @return object
     */
    private static function getOnlineVersion() {
        //FIXME we need a good installation to place this in
        $onlineversion = json_decode(expCore::loadData('http://localhost/exp2/getswversion.php'))->data;
        if (empty($onlineversion)) {
            $onlineversion->major = 0;
            $onlineversion->minor = 0;
            $onlineversion->revision = 0;
            $onlineversion->type = '';
            $onlineversion->iteration = '';
            $onlineversion->builddate = '';
        }
        return $onlineversion;
    }

	/**
	 * Routine to convert version iteration type to a rank
	 * 
	 * @param string $type
     *
	 * @return int
	 */
	private static function iterateType($type) {
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
