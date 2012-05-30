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
 * HTML Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */class htmlcontrol extends formcontrol {

	var $html;
	var $span;
	
	static function name() { return "Static Text"; }
	static function isSimpleControl() { return true; }
	
	function __construct($html = "",$span = true) {
		$this->span = $span;
		$this->html = $html;
	}

	function toHTML($label,$name) {
		if ($this->span) {
			return '<div class="control htmlcontrol">' . $this->html . '</div>';
		} else {
			return parent::toHTML($label,$name);
		}
	}
	
	function controlToHTML($name,$label) {
		return $this->html;
	}
	
	function form($object) {
		$form = new form();
		if (!isset($object->html)) {
			$object->html = "";
		} 
		$form->register("html",'',new htmleditorcontrol($object->html));
		$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		return $form;
	}
	
	function update($values, $object) {
		if ($object == null) $object = new htmlcontrol();
		$object->html = preg_replace("/<br ?\/>$/","",trim($values['html']));
		$object->caption = '';
		$object->identifier = uniqid("");
		$object->is_static = 1;
		return $object;
	}
	
}

?>
