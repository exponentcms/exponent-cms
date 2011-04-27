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
 * HTML Control
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
 * HTML Control
 *
 * @package Subsystems
 * @subpackage Forms
 */class htmlcontrol extends formcontrol {
	var $html;
	var $span;
	
	function name() { return "Static Text"; }
	function isSimpleControl() { return true; }
	
	function htmlcontrol($html = "",$span = true) {
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
	
	function controlToHTML($name) {
		return $this->html;
	}
	
	function form($object) {
		if (!defined("SYS_FORMS")) require_once(BASE."subsystems/forms.php");
		exponent_forms_initialize();
	
		$form = new form();
		if (!isset($object->html)) {
			$object->html = "";
		} 
		
		$i18n = exponent_lang_loadFile('subsystems/forms/controls/htmlcontrol.php');
		
		$form->register("html",'',new htmleditorcontrol($object->html));
		$form->register("submit","",new buttongroupcontrol($i18n['save'],'',$i18n['cancel']));
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
