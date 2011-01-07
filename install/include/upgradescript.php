<?php
##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class upgradescript {
	protected $from_version = '9999.99.99'; //set this ridiculously high so that it NEVER runs
	protected $to_version = '9999.99.99'; //set this to something ridiculously high so it always runs

	function name() {
		return "Base Upgrade Script";
	}

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

	function upgrade() {
		return $false;
	}
}

?>
