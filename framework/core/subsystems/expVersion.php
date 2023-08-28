<?php
##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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

/** @define "BASE" "../../.." */
/**
 * This is the class expVersion
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expVersion {

    private static $check_site = 'http://www.exponentcms.org/';  // url to check source code current version

    /**
     * Return a string of the current version number.
     *
     * @param bool $full  Whether or not to return a full version number.  If passed as true,
     *                    a string in the form of '2.0.3-beta5' will be returned.  Otherwise, '2.0' would be returned.
     * @param bool $build Whether or not to return the build date in the string.
     * @param bool $type  Whether to include the type and interation of a full version number
     *
     * @return string
     *
     * @node Subsystems:expVersion
     */
    public static function getVersion($full = false, $build = false, $type = true) {
        if (!defined('EXPONENT_VERSION_MAJOR')) include_once(BASE . "exponent_version.php");
        $vers = EXPONENT_VERSION_MAJOR . "." . EXPONENT_VERSION_MINOR; // can be used for numerical comparison
        if ($full) {
            $vers .= "." . EXPONENT_VERSION_REVISION;
            if ($type && EXPONENT_VERSION_TYPE != '') $vers .= "-" . EXPONENT_VERSION_TYPE . EXPONENT_VERSION_ITERATION;
        }
        if ($build) {
            $vers .= " (Build Date: " . date("F-d-Y", EXPONENT_VERSION_BUILDDATE) . ")";
        }
        return $vers;
    }

    /**
     * Return a string of the current version number in the database.
     *
     * @param bool $full  Whether or not to return a full version number.  If passed as true,
     *                    a string in the form of '2.0.3-beta5' will be returned.  Otherwise, '2.0' would be returned.
     * @param bool $build Whether or not to return the build date in the string.
     * @param bool $type  Whether to include the type and interation of a full version number
     *
     * @return string
     *
     * @node Subsystems:expVersion
     */
    public static function getDBVersion($full = false, $type = false, $build = true) {
        $dbver = self::dbVersion();
        $vers = $dbver->major . "." . $dbver->minor; // can be used for numerical comparison
        if ($full) {
            $vers .= "." . $dbver->revision;
            if ($type && $dbver->type != '') $vers .= "-" . $dbver->type . (!empty($dbver->iteration) ? $dbver->iteration : '');
        }
        if ($build) {
            $vers .= " (Build Date: " . date("F-d-Y", $dbver->builddate) . ")";
        }
        return $vers;
    }

    /**
     * Return an object of the current version number of the software.
     *
     * @return object
     *
     * @node Subsystems:expVersion
     */
    public static function swVersion() {
        $swversion = new stdClass();
        $swversion->major = EXPONENT_VERSION_MAJOR;
        $swversion->minor = EXPONENT_VERSION_MINOR;
        $swversion->revision = EXPONENT_VERSION_REVISION;
        $swversion->type = EXPONENT_VERSION_TYPE;
        $swversion->iteration = EXPONENT_VERSION_ITERATION;
        $swversion->builddate = EXPONENT_VERSION_BUILDDATE;
        return $swversion;
    }

    /**
     * Return an object of the current version number stored in the database.
     *
     * @return object
     *
     * @node Subsystems:expVersion
     */
    public static function dbVersion() {
        global $db;

        $dbversion = $db->selectObject('version', 1);
        if (empty($dbversion)) {
            $dbversion = new stdClass();
            $dbversion->major = 0;
            $dbversion->minor = 0;
            $dbversion->revision = 0;
            $dbversion->type = '';
            $dbversion->iteration = '';
            $dbversion->builddate = 0;
        }
        return $dbversion;
    }

    /**
     * Routine to check for installation or upgrade
     *
     * @param bool $force
     *
     * @return bool
     */
    public static function checkVersion($force=false) {
        global $db, $user, $framework;

        $swversion = self::swVersion();
        $update = false;

        // check database version against installed software version
        if ($db->havedb) {
            $dbversion = self::dbVersion();
            $newversion = new stdClass();  // version of last major database change
            $newversion->major = 2;
            $newversion->minor = 2;
            $newversion->revision = 0;
            $newversion->type = 'release-candidate';
            $newversion->iteration = '1';
            $newversion->builddate = 0;
            if (self::compareVersion($dbversion, $newversion)) {  // notice to upgrade to v2.2.0 if needed
                flash('error', gt('The system database must be upgraded to display site content!'));
            }
            if ($user->isSuperAdmin()) {
                // check if software version is newer than database version
                if (self::compareVersion($dbversion, $swversion)) {
                    flash('message', gt('The database requires upgrading from') . ' v' . self::getDBVersion(true, false, true) . ' ' . gt('to') . ' v' . self::getVersion(true, false, true) .
                        '<br><a href="' . makelink(array("controller" => "administration", "action" => "install_exponent")) . '">' . gt('Click here to Upgrade your website') . '</a>');
                    $update = true;
                }
            }

            // check if online version is newer than installed software version, but only once per session
            if ((!(defined('SKIP_VERSION_CHECK') ? SKIP_VERSION_CHECK : 0) || $force) && $user->isSuperAdmin()) {
                if (!expSession::is_set('update-check')) {
                    //FIXME we need a good installation/server to place this on
                    $jsondata = json_decode(expCore::loadData(self::$check_site . 'getswversion.php'));
                    expSession::set('update-check', '1');
                    if (!empty($jsondata->data->major)) {
                        $onlineVer = $jsondata->data;
                        if (!empty($onlineVer)) {
                            if (self::compareVersion($swversion, $onlineVer)) {
                                if (self::compareVersion($swversion, $onlineVer, true)) {
                                    // is NOT a patch to the current version, so need a whole package
                                    $note = gt('A newer') . ' ' . gt('version of Exponent is available') . ':';
                                    $newvers = $onlineVer->major . '.' . $onlineVer->minor . '.' . $onlineVer->revision . (!empty($onlineVer->type) && $onlineVer->type !== 'patch' ? $onlineVer->type : '') . (!empty($onlineVer->iteration) && !empty($onlineVer->type) && $onlineVer->type != 'patch' ? $onlineVer->iteration : '');
                                } else {
                                    // only difference is a patch, so only the patch file is needed
                                    $note = gt('A patch for this') . ' ' . gt('version of Exponent is available') . ':';
                                    $newvers = $onlineVer->major . '.' . $onlineVer->minor . '.' . $onlineVer->revision . ($onlineVer->type ? $onlineVer->type : '') . ($onlineVer->iteration ? $onlineVer->iteration : '');
                                }
                                flash('message', $note . ' v' . $newvers . ' ' . gt('was released') . ' ' . expDateTime::format_date($onlineVer->builddate) .
                                    '<br><a href="https://sourceforge.net/projects/exponentcms/files/" target="_blank">' . gt('Click here to see available Downloads') . '</a>');
                                $update = true;
                            }
                        }
                    } else {
                        flash('error', gt('Unable to contact update server. Automatic Version Check is only performed once per Super-admin login.'));
                    }
                }
            }
            return $update;
        }

        // we're not up and running yet, new installation so launch installer
        if (@file_exists(BASE.'install/not_configured') || !(@file_exists(BASE.'framework/conf/config.php'))) {
            header('Location: '.URL_FULL.'install/index.php');
            exit('Redirecting to the Exponent Install Wizard');
        }

        // database is unavailable, so show us as being offline and stop
        if (empty($framework)) {
            $framework = expSession::get('framework');
        }
        expCore::setup_autoload($framework);
        $template = new standalonetemplate('_maintenance');
        $template->assign("db_down", true);
        $template->output();
        exit();
    }

    /**
     * Routine to compare passed versions
     *
     * @param object $version1
     * @param object $version2
     * @param bool   $onlypatchchk  returns false if major.minor.revision are equal and type is 'patch'
     *
     * @return bool set to true if $version1 is less than $version2
     */
    public static function compareVersion($version1, $version2, $onlypatchchk = false) {
        if (!is_object($version1) || !is_object($version2)) return false;
        if ($version1->major < $version2->major) {
            return true;
        } elseif ($version1->major == $version2->major && $version1->minor < $version2->minor) {
            return true;
        } elseif ($version1->major == $version2->major && $version1->minor == $version2->minor && $version1->revision < $version2->revision) {
            return true;
        } elseif ($version1->major == $version2->major && $version1->minor == $version2->minor && $version1->revision == $version2->revision) {
            if ($onlypatchchk && $version2->type == 'patch') {
                return false;
            }
            $ver1type = self::iterateType($version1->type);
            $ver2type = self::iterateType($version2->type);
            if ($ver1type < $ver2type) {
                return true;
            } elseif ($ver1type == $ver2type && $version1->iteration < $version2->iteration) {
                return true;
            }
        }
        return false;
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
            case 'rc':
                $typenum = 3;
                break;
            case 'develop': // code from the github develop branch
                $typenum = 5;
                break;
            case '': // stable release
                $typenum = 10;
                break;
            case 'patch': // a patch trumps the stable version of the same version number
                $typenum = 20;
                break;
            default:
                $typenum = 0;
                break;
        }
        return $typenum;
    }

}

?>