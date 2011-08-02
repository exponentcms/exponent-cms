<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Hidden Field Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class hiddenfieldcontrol extends formcontrol {

	var $flip = false;
	var $jsHooks = array();
	
	function name() { return "generic"; }
	function isSimpleControl() { return false; }
	function getFieldDefinition() { 
		return array();
	}

	function __construct() {
	}
	
	function toHTML($label,$name) {
		$html = $this->controlToHTML();
		return $html;
	}

	function controlToHTML() {
		$html = '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="'.$this->default.'"';
		$html .= ' />';
		return $html;
	}
	
	static function parseData($name, $values, $for_db = false) {
		return isset($values[$name])?1:0;
	}
	
	function templateFormat($db_data, $ctl) {
		return ($db_data==1)?"Yes":"No";
	}
	
	function form($object) {
		$i18n = exponent_lang_loadFile('subsystems/forms/controls/checkboxcontrol.php');
	
//		if (!defined("SYS_FORMS")) require_once(BASE."framework/core/subsystems-1/forms.php");
		require_once(BASE."framework/core/subsystems-1/forms.php");
//		exponent_forms_initialize();
	
		$form = new form();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
			$object->default = false;
			$object->flip = false;
			$object->required = false;
		} 
		
		$form->register("identifier",$i18n['identifier'],new textcontrol($object->identifier));
		$form->register("caption",$i18n['caption'], new textcontrol($object->caption));
		$form->register("default",$i18n['default'], new checkboxcontrol($object->default,false));
		$form->register("flip",$i18n['caption_right'], new checkboxcontrol($object->flip,false));
		$form->register(null, null, new htmlcontrol('<br />'));
				$form->register("required", $i18n['required'], new checkboxcontrol($object->required,true));
				$form->register(null, null, new htmlcontrol('<br />')); 
		$form->register("submit","",new buttongroupcontrol($i18n['save'],'',$i18n['cancel']));
		
		return $form;
	}
	
	function update($values, $object) {
		if ($object == null) $object = new checkboxcontrol();
		if ($values['identifier'] == "") {
			$i18n = exponent_lang_loadFile('subsystems/forms/controls/checkboxcontrol.php');
		
			$post = $_POST;
			$post['_formError'] = $i18n['id_required'];
			exponent_sessions_set("last_POST",$post);
			return null;
		}
		$object->identifier = $values['identifier'];
		$object->caption = $values['caption'];
		$object->default = isset($values['default']);
		$object->flip = isset($values['flip']);
		$object->required = isset($values['required']);
		return $object;
	}
	
}

?>
