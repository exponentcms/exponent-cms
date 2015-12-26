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

/**
 * This is the class expDefinableFieldController
 *
 * @package Core
 * @subpackage Controllers
 */

class expDefinableFieldController extends expController {
	public $basemodel_name = 'expDefinableField';

	/**
	 * name of module
	 * @return string
	 */
    static function displayname() { return gt("Definable Field"); }

	/**
	 * description of module
	 * @return string
	 */
    static function description() { return gt("This module is for managing definable fields"); }

	/**
	 * does module have sources available?
	 * @return bool
	 */
	static function hasSources() { return false; }

    /**
   	 * default view for individual field
   	 */
   	function show() {
        assign_to_template(array('record'=>$record,'tag'=>$tag));  //FIXME $record & $tag are undefined
    }

	/**
	 * manage definable field
	 */
	function manage() {
        global $db;
		
        expHistory::set('manageable', $this->params);
		$fields = $db->selectObjects("expDefinableFields",'1','rank');
		$types = expTemplate::listControlTypes();
        uasort($types, "strnatcmp");
		array_unshift($types,'['.gt('Please Select'.']'));
        assign_to_template(array('fields'=>$fields, 'types'=>$types));
    }
	
	function edit() {
		global $db;
		 
		$control_type = "";
		$ctl = null;
		if (isset($this->params['id'])) {
			$control = $db->selectObject("expDefinableFields","id=".$this->params['id']);
			if ($control) {
				$ctl = unserialize($control->data);
				$ctl->identifier = $control->name;
				$ctl->id = $control->id;
				$control_type = get_class($ctl);
			}
		}
		if ($control_type == "") $control_type = $this->params['control_type'];
		$form = call_user_func(array($control_type,"form"),$ctl);
		if ($ctl) { 
			$form->controls['identifier']->disabled = true;
			$form->meta("id",$ctl->id);
			$form->meta("identifier",$ctl->identifier);
		}
		$form->meta("action","save");
		$form->meta('module',"expDefinableField");
		$form->meta('control_type',$control_type);
		$form->meta("type", $control_type);
		$types = expTemplate::listControlTypes();

		assign_to_template(array('form_html'=>$form->toHTML(), 'types'=>$types[$control_type]));			
	}
	
	function save() {	
		global $db;
		$ctl = null;
		$control = null;
		if (isset($this->params['id'])) {
			$control = $db->selectObject('expDefinableFields','id='.$this->params['id']);
			if ($control) {
				$ctl = unserialize($control->data);
				$ctl->name = $ctl->identifier;
			}
		}

		if (call_user_func(array($_POST['control_type'],'useGeneric')) == true) { 	
			$ctl = call_user_func(array('genericcontrol','update'),expString::sanitize($_POST),$ctl);
		} else {
			$ctl = call_user_func(array($_POST['control_type'],'update'),expString::sanitize($_POST),$ctl);
		}
		
		if ($ctl != null) {
			$name = substr(preg_replace('/[^A-Za-z0-9]/','_',$ctl->identifier),0,20);
	
			if (!isset($this->params['id'])) {
				$control->name =  $name;
			}
	
            if (!empty($ctl->pattern)) $ctl->pattern = addslashes($ctl->pattern);
			$control->data = serialize($ctl);
			$control->type = $this->params['type'];
			
			if (isset($control->id)) {
				$db->updateObject($control,'expDefinableFields');
			} else {
				$db->insertObject($control,'expDefinableFields');
			}
		}
		
		redirect_to(array('controller'=>'expDefinableField','action'=>'manage'));
	}

}

?>
