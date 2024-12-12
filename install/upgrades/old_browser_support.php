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
 * This is the class old_browser_support
 *
 * @package Installation
 * @subpackage Upgrade
 */
class old_browser_support extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.4.2';
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Activate Obsolete Browser Support"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.4.2 we remove support for obsolete browsers (non-HTML5), however compatibility may be turned back on."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
   		return true;  // subclasses MUST return true to be run
   	}

	/**
	 * Activates Obsolete Browser Support
	 * @return string
	 */
	function upgrade() {
        expSettings::change('OLD_BROWSER_SUPPORT', true);

		return gt("Obsolete Browser Support Activated.");
	}
}

?>
