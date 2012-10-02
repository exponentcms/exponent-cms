<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
/** @define "BASE" "../../../../.." */

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
	
	static function name() { return "generic"; }
	static function isSimpleControl() { return false; }
	static function getFieldDefinition() {
		return array();
	}

	function __construct() {
	}
	
	function toHTML($label,$name) {
		$html = $this->controlToHTML();
		return $html;
	}

	function controlToHTML($name=null,$label=null) {
		$html = '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="'.$this->default.'"';
		$html .= ' />';
		return $html;
	}
	
	static function parseData($name, $values, $for_db = false) {
		return isset($values[$name])?1:0;
	}
	
    static function templateFormat($db_data, $ctl) {
		return ($db_data==1)?gt("Yes"):gt("No");
	}
	
	static function form($object) {
		$form = new form();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
			$object->default = false;
			$object->flip = false;
			$object->required = false;
		} 
		
		$form->register("identifier",gt('Identifier'),new textcontrol($object->identifier));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
		$form->register("default",gt('Default'), new checkboxcontrol($object->default,false));
		$form->register("flip",gt('Caption on Right'), new checkboxcontrol($object->flip,false));
		$form->register(null, null, new htmlcontrol('<br />'));
				$form->register("required", gt('Make this a required field.'), new checkboxcontrol($object->required,true));
				$form->register(null, null, new htmlcontrol('<br />')); 
		$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		
		return $form;
	}
	
    static function update($values, $object) {
		if ($object == null) $object = new checkboxcontrol();
		if ($values['identifier'] == "") {
			$post = $_POST;
			$post['_formError'] = gt('Identifier is required.');
			expSession::set("last_POST",$post);
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
