<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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
 * @package Installation
 * @subpackage Upgrade
 */

/**
 * This is the class upgradescript
 */
class upgradescript {
	protected $from_version = '9999.99.99'; //set this default ridiculously high so that it NEVER runs
	protected $to_version = '9999.99.99'; //set this default to something ridiculously high so it always runs

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	function name() { return "Base Model for the Upgrade Scripts"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "This Script attempts to ".$this->name(); }

	/**
	 * test version number (code vs database) to see if upgrade script should be run
	 * @param $version
	 * @return bool
	 */
	function checkVersion($version) {
		// if this upgrade applies to only one version then check to see if we have a match
		if ($this->from_version == $this->to_version && $version == $this->from_version) {
			return true;
		} elseif ($version >= $this->from_version && $version <= $this->to_version) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return false;  // subclasses MUST return true to be run
	}

	/**
	 * main routine of upgrade script to upgrade Exponent
	 * @return bool
	 */
	function upgrade() {
		return false;
	}
}

?>
