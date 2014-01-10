<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');

/**
 * Custom Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class customcontrol extends formcontrol {

	var $html;
	
	static function name() { return "Custom Control"; }
	
	function __construct($html = "") {
		$this->html = $html;
	}

	function controlToHTML($name,$label) {
		return $this->html;
	}

	static function parseData($name, $values, $for_db = false) {
		return;
	}

}

?>
