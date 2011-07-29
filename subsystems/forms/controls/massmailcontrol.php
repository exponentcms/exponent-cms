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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Mass Mailing Control
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
 * Mass Mailing Control
 *
 * @package Subsystems
 * @subpackage Forms
 */
class massmailcontrol extends formcontrol {
	var $type = 0;

	function name() { return "Mass-Mailling Control"; }

	function massmailcontrol($default = "",$type = 0) {
		$this->default = $default;
		$this->type = $type;
	}

	function controlToHTML($name) {
		// First, grab the data for the users
		$html = "<script type='text/javascript' src='".PATH_RELATIVE."js/MassMailControl.js'></script>";
		$html .= "<table cellpadding='0' cellspacing='0' border='0'><tr><td>";
		$html .= '<input type="radio" id="r_'.$name.'_users" name="'.$name.'_type" value="0" onclick="activateMassMailControl(0,\''.$name.'\');" />All Users';
		$html .= '</td></tr><tr><td>';
		$html .= '<input type="radio" id="r_'.$name.'_email" name="'.$name.'_type" value="1" onclick="activateMassMailControl(1,\''.$name.'\');" />This Address:';
		$html .= '<input type="text" name="'.$name.'[1]" id="'.$name.'_email" ';
		if ($this->type == 1) $html .= 'value="'.$this->default.'" ';
		$html .= '/>';
		$html .= '</td></tr></table>';
		$html .= '<script type="text/javascript">activateMassMailControl('.$this->type.',"'.$name.'");</script>';
		return $html;
	}
}

?>
