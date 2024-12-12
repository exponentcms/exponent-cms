<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * This is the class fix_siteurl
 *
 * @package Installation
 * @subpackage Upgrade
 */
class fix_siteurl extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.7.0';
    public $priority = 98; // set this to a low priority

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Set Site URL"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "To prevent a Host Header Injection, we need to know our site url.  This script site url."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
	    $tmp = defined('SITE_URL') && SITE_URL === '0';
        return (defined('SITE_URL') && SITE_URL === '0');
	}

	/**
	 * reads in and corrects the modstate table, esp. since it has no index and allows duplicate entries
     *   we will assume that all old school modules have been upgraded at this point
     *
	 * @return string
	 */
	function upgrade() {
        if (isset($_SERVER['HTTP_HOST'])) {
            $hostname = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $hostname =  $_SERVER['SERVER_NAME'];
        } else {
            $hostname =  '';
        }
        expSettings::change('SITE_URL', $hostname);

        return gt('Site URL was added to configuration.');
	}
}

?>
