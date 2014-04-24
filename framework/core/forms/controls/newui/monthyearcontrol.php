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
/** @define "BASE" "../../.." */

/**
 * Month Year Picker
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class monthyearcontrol extends formcontrol {
	
	function __construct($default_month = null,$default_year = null) {
		if ($default_month == null) date("m");
		if ($default_year == null) date("Y");
		$this->default_month = $default_month;
		$this->default_year = $default_year;
	}

	function toHTML($label,$name) {
        $this->id = (empty($this->id)) ? $name : $this->id;
        $html     = "<div id=\"" . $this->id . "Control\" class=\"control";
        $html .= (!empty($this->required)) ? ' required">' : '">';
        $html .= "<label><span class=\"label\">" . $label . "</span></label>";
        $html .= $this->controlToHTML($name, $label);
        $html .= "</div>";
        return $html;
    }
	
	function controlToHTML($name,$label=null) {
		$html = '<select id="' . $name . '_month" name="' . $name . '_month">';
		for ($i = 1; $i <= 12; $i++) {
			$s = ((strlen($i) == 1)?"0".$i:$i);
			$html .= '<option value="' . $s . '"';
			if ($s == $this->default_month) $html .= " selected";
			$html .= '>' . $s . '</option>';
		}
		$html .= '</select>';
		$html .= "/";
		$html .= '<select id="' . $name . '_year" name="' . $name . '_year">';
		for ($i = date("Y"); $i <= (date("Y") + 15); $i++) {
			$html .= '<option value="' . $i . '"';
			if ($i == $this->default_year) $html .= " selected";
			$html .= '>' . $i . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
}

?>
