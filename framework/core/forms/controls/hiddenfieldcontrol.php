<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

	static function name() { return "Hidden Field"; }

	function __construct($default = "") {
        $this->default = $default;
	}
	
	function toHTML($label,$name) {
		$html = $this->controlToHTML($name,$label);
		return $html;
	}

	function controlToHTML($name=null,$label=null) {
        $this->name = empty($this->name) ? $name : $this->name;
        $idname  = (!empty($this->id)) ? ' id="'.$this->id.'"' : "";
		$html = '<input type="hidden"' . $idname . ' name="' . $this->name . '" value="'.$this->default.'"';
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
		
		$form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
		$form->register("default",gt('Default'), new checkboxcontrol($object->default,false));
		$form->register("flip",gt('Caption on Right'), new checkboxcontrol($object->flip,false));
		$form->register(null, null, new htmlcontrol('<br />'));
        $form->register("required", gt('Make this a required field.'), new checkboxcontrol($object->required,true));
        $form->register(null, null, new htmlcontrol('<br />'));
		if (!expJavascript::inAjaxAction())
			$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		
		return $form;
	}
	
    static function update($values, $object) {
		if ($object == null) $object = new checkboxcontrol();
		if ($values['identifier'] == "") {
			$post = expString::sanitize($_POST);
			$post['_formError'] = gt('Identifier is required.');
			expSession::set("last_POST",$post);
			return null;
		}
		$object->identifier = $values['identifier'];
		$object->caption = $values['caption'];
		$object->default = isset($values['default']);
		$object->flip = !empty($values['flip']);
		$object->required = !empty($values['required']);
		return $object;
	}
	
}

?>
