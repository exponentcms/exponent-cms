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
 * Contact Control
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
 * Contact Control
 *
 * @package Subsystems
 * @subpackage Forms
 */
class contactcontrol extends formcontrol {
	var $type = 0;

	function name() { return "Contact"; }

	function parseData($name, $values, $for_db = false) {
		return;
	}

	function contactcontrol($default = "",$type = 0) {
		$this->default = $default;
		$this->type = $type;
	}

	function controlToHTML($name) {
		// First, grab the data for the users
		if (!defined("SYS_USERS")) require_once(BASE."subsystems/users.php");
		$users = array();

		foreach (exponent_users_getAllUsers() as $u) {
			$users[$u->id] = $u->firstname." ".$u->lastname.' ('.$u->username.')';
		}

		uasort($users,'strnatcmp');

		$html = "<script type='text/javascript' src='".PATH_RELATIVE."js/ContactControl.js'></script>";
		$html .= "<table cellpadding='0' cellspacing='0' border='0'><tr><td>";
		$html .= '<input class="contactcontrol" type="radio" id="r_'.$name.'_users" name="'.$name.'_type" value="0" onclick="activateContactControl(0,\''.$name.'\');" />User:';
		$html .= '<select name="'.$name.'[0]" id="'.$name.'_users">';
		foreach ($users as $id=>$uname) {
			$html .= '<option ';
			if ($this->default == $id && $this->type == 0) $html .= 'selected ';
			$html .= 'value="'.$id.'">'.$uname.'</option>';
		}
		$html .= '</select>';
		$html .= '</td></tr><tr><td>';
		$html .= '<input class="contactcontrol" type="radio" id="r_'.$name.'_email" name="'.$name.'_type" value="1" onclick="activateContactControl(1,\''.$name.'\');" />Email:';
		$html .= '<input class="contactcontrol" type="text" name="'.$name.'[1]" id="'.$name.'_email" ';
		if ($this->type == 1) $html .= 'value="'.$this->default.'" ';
		$html .= '/>';
		$html .= '</td></tr></table>';
		$html .= '<script type="text/javascript">activateContactControl('.$this->type.',"'.$name.'");</script>';
		return $html;
	}
}

?>
