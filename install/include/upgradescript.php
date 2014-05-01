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
 * @package    Installation
 * @subpackage Upgrade
 */

/**
 * This is the abstract class upgradescript
 */
abstract class upgradescript {
    protected $from_version = '9999.99.99'; // set default ridiculously high so that it NEVER runs
    protected $to_version = '9999.99.99'; // set default to something ridiculously high so it always runs
    public $optional = false; // set default to not make this script optional
    public $priority = 50; // set default to average or middle, 1 = first, 100 = last

    /**
     * The name/title of upgrade script
     *
     * @return string
     */
    static function name() {
    }

    /**
     * The generic description of upgrade script
     *
     * @return string
     */
    function description() {
        return gt("This Script attempts to") . " " . $this->name() . ".";
    }

    /**
     * This routine should simply test version number (upgrade script requirements vs database version) to see if upgrade script should be run
     *
     * @param object $version
     *
     * @return bool
     */
    function checkVersion($version) {
        $db_version = $version->major . '.' . $version->minor . '.' . $version->revision;
        $to_version = explode('.', $this->to_version);
        $to_ver = new stdClass();
        $to_ver->major = $to_version[0];
        $to_ver->minor = $to_version[1];
        $to_ver->revision = $to_version[2];
        $to_ver->type = '';
        $to_ver->iteration = '';
        $from_version = explode('.', $this->from_version);
        $from_ver = new stdClass();
        $from_ver->major = $from_version[0];
        $from_ver->minor = $from_version[1];
        $from_ver->revision = $from_version[2];
        $from_ver->type = '';
        $from_ver->iteration = '';

        // check if db version is equal to or inside the versions
        if ($db_version == $this->from_version || $db_version == $this->to_version) {
            return true;
        } elseif (expVersion::compareVersion($from_ver, $version) && expVersion::compareVersion($version, $to_ver)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
     *
     * @return bool
     */
    function needed() {
        return false; // subclasses MUST return true to be run
    }

    /**
     * The main routine of upgrade script which is run to upgrade Exponent
     *
     * @return bool
     */
    function upgrade() {
    }

    /**
     * Comparison function to prioritize upgrade scripts
     *
     * @param $a
     * @param $b
     *
     * @return bool
     */
    public static function prioritize($a, $b) {
        return $a->priority > $b->priority;
    }

}

?>
