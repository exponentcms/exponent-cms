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

if (!defined('EXPONENT')) exit('');

/**
 * Custom Control
 *
 * @author James Hunt
 * @copyright 2004-2011 OIC Group, Inc.
 * @version 0.95
 *
 * @package Subsystems
 * @subpackage Forms
 */

/**
 * Manually include the class file for formcontrol, for PHP4
 * (This does not adversely affect PHP5)
 */
require_once(BASE."subsystems/forms/controls/formcontrol.php");

/**
 * Custom Control
 *
 * @package Subsystems
 * @subpackage Forms
 */
class customcontrol extends formcontrol {
	var $html;
	
	function name() { return "Custom Control"; }
	
	function parseData($name, $values, $for_db = false) {
		return;
	}
	function customcontrol($html = "") {
		$this->html = $html;
	}

	function controlToHTML($name) {
		return $this->html;
	}
}

?>
