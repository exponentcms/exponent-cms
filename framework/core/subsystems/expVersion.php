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
 * This is the class expVersion
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expVersion {

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
            $vers .= " (Build Date: " . strftime("%D", EXPONENT_VERSION_BUILDDATE) . ")";
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
    public static function getDBVersion($full = false, $build = false, $type = true) {
        $dbver = self::dbVersion();
        $vers = $dbver->major . "." . $dbver->minor; // can be used for numerical comparison
        if ($full) {
            $vers .= "." . $dbver->revision;
            if ($type && $dbver->type != '') $vers .= "-" . $dbver->type . (!empty($dbver->iteration) ? $dbver->iteration : '');
        }
        if ($build) {
            $vers .= " (Build Date: " . strftime("%D", $dbver->builddate) . ")";
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
        }
        return $dbversion;
    }

    /**
     * Routine to check for installation or upgrade
     */
    public static function checkVersion() {
        global $db, $user;

        $swversion = self::swVersion();

        // check database version against installed software version
        if ($db->havedb) {
            if ($user->isSuperAdmin()) {
                $dbversion = self::dbVersion();
                // check if software version is newer than database version
                if (self::compareVersion($dbversion, $swversion)) {
                    flash('message', gt('The database requires upgrading from') . ' v' . self::getDBVersion(true) . ' ' . gt('to') . ' v' . self::getVersion(true) .
                        '<br><a href="' . makelink(array("controller" => "administration", "action" => "install_exponent")) . '">' . gt('Click here to Upgrade your website') . '</a>');
                }
            }
        } else {
            // database is unavailable, so show us as being offline
            $template = new standalonetemplate('_maintenance');
            $template->assign("db_down", true);
            $template->output();
            exit();
        }

        // check if online version is newer than installed software version, but only once per session
        if (!(defined('SKIP_VERSION_CHECK') ? SKIP_VERSION_CHECK : 0) && $user->isSuperAdmin()) {
            if (!expSession::is_set('update-check')) {
                //FIXME we need a good installation/server to place this on
//                $jsondata = json_decode(expCore::loadData('http://www.exponentcms.org/' . 'getswversion.php'));
                $jsondata = json_decode(expCore::loadData('http://www.harrisonhills.org/' . 'getswversion.php'));
                expSession::set('update-check', '1');
                if (!empty($jsondata->data)) {
                    $onlineVer = $jsondata->data;
                    if (!empty($onlineVer)) {
                        if (self::compareVersion($swversion, $onlineVer)) {
                            $note = ($onlineVer->type == 'patch' ? gt('A patch for the latest') : gt('A newer')) . ' ' . gt('version of Exponent is available') . ':';
                            $newvers = $onlineVer->major . '.' . $onlineVer->minor . '.' . $onlineVer->revision . ($onlineVer->type ? $onlineVer->type : '') . ($onlineVer->iteration ? $onlineVer->iteration : '');
                            flash('message', $note . ' v' . $newvers . ' ' . gt('was released') . ' ' . expDateTime::format_date($onlineVer->builddate) .
                                '<br><a href="https://github.com/exponentcms/exponent-cms/downloads" target="_blank">' . gt('Click here to see available Downloads') . '</a>');
                        }
                    }
                } else {
                    flash('error', gt('Unable to contact update server. Version check only performed once per Super-admin login.'));
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
    public static function compareVersion($version1, $version2) {
        if (!is_object($version1) || !is_object($version2)) return false;
        if ($version1->major < $version2->major) {
            return true;
        } elseif ($version1->major == $version2->major && $version1->minor < $version2->minor) {
            return true;
        } elseif ($version1->major == $version2->major && $version1->minor == $version2->minor && $version1->revision < $version2->revision) {
            return true;
        } elseif ($version1->major == $version2->major && $version1->minor == $version2->minor && $version1->revision == $version2->revision) {
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