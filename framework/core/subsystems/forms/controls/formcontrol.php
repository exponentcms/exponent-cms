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
 * Base Form Control Class
 *
 * @author Phillip Ball
 * @copyright 2007-2009 OIC Group, Inc.
 * @version 2
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class formcontrol {

	var $accesskey = "";
	var $default = "";
	var $disabled = false;
    var $required = false;  
	var $tabindex = -1;
	var $inError = 0; // This will ONLY be set by the parent form.
	var $type = 'text';

	function name() { return "formcontrol"; }

	/**
	 * Is this a Simple Control?
	 * Used to determine if control is available for the Form (Builder) module
	 * @return bool
	 */
	function isSimpleControl() { return false; }
	function getFieldDefinition() { return array(); }

	function toHTML($label,$name) {
		if (!empty($this->id)) {
		    $divID  = ' id="'.$this->id.'Control"';
		    $for = ' for="'.$this->id.'"';
		} else {
		    $divID  = '';
		    $for = '';
		}
		
		$disabled = $this->disabled != 0 ? "disabled" : "";
		$class = empty($this->class) ? '' : $this->class;
		 
		$html = "<div".$divID." class=\"".$this->type."-control control ".$class.$disabled."\"";
		$html .= (!empty($this->required)) ? ' required">' : '>';
		//$html .= "<label>";
        if($this->required) $label = "* " . $label;
		if(empty($this->flip)){
			$html .= (!empty($label)) ? "<label".$for." class=\"label\">".$label."</label>" : "";
			$html .= $this->controlToHTML($name, $label);
		} else {
			$html .= $this->controlToHTML($name, $label);
			$html .= (!empty($label)) ? "<label".$for." class=\"label\">".$label."</label>" : "";
		}
		//$html .= "</label>";
		$html .= "</div>";			
		return $html;
	}
	
	function controlToHTML($name) {
		return "";
	}
	
	static function parseData($original_name,$formvalues) {
		return (isset($formvalues[$original_name])?$formvalues[$original_name]:"");
	}
	
	function onUnRegister(&$form) { // Do we need the explicit ref op??
		return true;
	}
	
	function onRegister(&$form) { // Do we need the explicit ref op??
		return true;
	}
	
	function templateFormat($db_data, $ctl = null) {
		return isset($db_data)?$db_data:"";
	}
}

?>
