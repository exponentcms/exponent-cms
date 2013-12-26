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
 * Upgrade Script
 *
 * @package Installation
 * @subpackage Upgrade
 */

/**
 * This is the class update_corecss
 */
class update_corecss extends upgradescript {
	protected $from_version = '0.0.0';
//	protected $to_version = '1.99.2';
    public $priority = 94; // set this to a very low priority BUT before clear_cache

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return gt("Update the Core CSS Files"); }

    /**
   	 * generic description of upgrade script
   	 * @return string
   	 */
   	function description() { return "In v2.2.3 we converted the core css files to .less format.  We must recompile them to ensure they are current since they are sometimes referenced directly."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;
	}

	/**
	 * updates all the core css files
	 * @return bool
	 */
	function upgrade() {
        expCSS::updateCoreCss();
		return gt("All Core CSS files were updated.");
	}

}

?>
