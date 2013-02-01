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
 * Password Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class passwordcontrol extends formcontrol {

	var $default = "";
	var $size = 0;
	var $maxlength = "";
	
	static function name() { return "Password Field"; }

	function __construct($default = "", $size = 0, $disabled = false, $maxlength = 0) {
		$this->default = $default;
		$this->size = $size;
		$this->disabled = $disabled;
		$this->maxlength = $maxlength;
	}
	
	function controlToHTML($name,$label) {
		$html = "<input type=\"password\" name=\"$name\" value=\"" . $this->default . "\" ";
		$html .= ($this->size?"size=\"".$this->size."\" ":"");
		$html .= ($this->disabled?"disabled ":"");
		$html .= ($this->maxlength?"maxlength=\"".$this->maxlength."\" ":"");
		$html .= ($this->tabindex >= 0?"tabindex=\"".$this->tabindex."\" ":"");
		$html .= ($this->accesskey != ""?"accesskey=\"".$this->accesskey."\" ":"");
		$html .= "/>";
		return $html;
	}
	
	static function form($object) {

	}
	
    static function update($values, $object) {

	}

}

?>
