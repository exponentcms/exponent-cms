<?php
//FIXME Deprecated! Not used, missing js file any way
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
 * Mass Mailing Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class massmailcontrol extends formcontrol {

	var $type = 0;

	static function name() { return "Mass-Mailing Control"; }

	function __construct($default = "",$type = 0) {
		$this->default = $default;
		$this->type = $type;
	}

	function controlToHTML($name,$label) {
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
