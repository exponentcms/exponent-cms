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
 * Radio Button Control class
 *
 * An HTML Radio Button
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class radiogroupcontrol extends formcontrol {

	var $flip = false;
	var $items = array();
	var $spacing = 100;
	var $cols = 1;
	var $onclick = null;
	
	static function name() { return "Radio Button Group"; }
	static function isSimpleControl() { return true; }
	static function getFieldDefinition() {
		return array(
			DB_FIELD_TYPE=>DB_DEF_STRING,
			DB_FIELD_LEN=>512);
	}
	
	function __construct($default = "", $items = array(), $flip=false, $spacing=100, $cols = 1) {
		$this->default = $default;
		$this->items = $items;
		$this->flip = $flip;
		$this->spacing = $spacing;
		$this->cols = $cols;
		$this->required = false;
	}

	function toHTML($label,$name) {
		$this->id  = (empty($this->id)) ? $name : $this->id;
		$html = "<div id=\"".$this->id."Control\" class=\"radiogroup control";
		$html .= (!empty($this->required)) ? ' required">' : '">';
//		$html .= "<table border=0 cellspacing=0 cellpadding=0><tr>";
//		$html .= (!empty($label))?"<td><span class=\"label\">".$label."</span></td></tr><tr>":"";
        $html .= (!empty($label))?"<span class=\"label\">".$label."</span>":"";
//        $html .= "<table border=0 cellspacing=0 cellpadding=0><tr>";
//		$html .= "<td>".$this->controlToHTML($name, $label)."</td>";
//		$html .= "</tr></table>";
		$html .= $this->controlToHTML($name, $label);
        $html .= "</div>";
        if (!empty($this->description)) $html .= "<div class=\"control-desc\">".$this->description."</div>";
		return $html;
	}
	
	function controlToHTML($name, $label) {
        //eDebug($this->items);
		$html = '<table cellspacing="0" cellpadding="0" border="0"><tr>';
		$i = 0;
		foreach ($this->items as $value=>$rname) {  //FJD
			$radio = null;
			
			$checked = false;
			if (!empty($this->checked)) {
			    $checked = $value == $this->checked ? true : false;
			}	

			$radio = new radiocontrol($checked, $value, $rname, $this->flip, $this->onclick);

			$radio->newschool = !empty($this->newschool) ? $this->newschool : false;
			$radio->value = $value;
			
			$radio->checked = (isset($this->default) && $this->default==$radio->value) ? true : false;

			
            if ($this->cols!=0 && $i==$this->cols) {
    			$html .= '</tr><tr>';
    			$i = 0;
            }
			$html .= '<td style="border:none; padding-left:5px">'.$radio->toHTML($rname, $name).'</td>';
			$i++; 
		}	
		$html .= '</tr></table>';
        if (!empty($this->description)) $html .= "<div class=\"control-desc\">".$this->description."</div>";
		return $html;
	}
	
	static function form($object) {
		$form = new form();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
            $object->description = "";
			$object->default = "";
			$object->flip = false;
			$object->spacing = 100;
			$object->cols = 1;
			$object->items = array();
		} 
        if (empty($object->description)) $object->description = "";
		$form->register("identifier",gt('Identifier'),new textcontrol($object->identifier));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
		$form->register("items",gt('Items'), new listbuildercontrol($object->items,null));
		$form->register("default",gt('Default'), new textcontrol($object->default));
		$form->register("flip","Caption on Left", new checkboxcontrol($object->flip,false));
		$form->register("cols",gt('Columns'), new textcontrol($object->cols,4,false,2,"integer"));
		$form->register(null,"", new htmlcontrol(gt('Setting Number of Columns to zero will put all items on one row.')));
		$form->register("spacing",gt('Column Spacing'), new textcontrol($object->spacing,5,false,4,"integer"));
		$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		
		return $form;
	}
	
	function update($values, $object) {
		if ($object == null) $object = new radiogroupcontrol();
		if ($values['identifier'] == "") {
			$post = $_POST;
			$post['_formError'] = gt('Identifier is required.');
			expSession::set("last_POST",$post);
			return null;
		}
		$object->identifier = $values['identifier'];
		$object->caption = $values['caption'];
        $object->description = $values['description'];
		$object->default = $values['default'];
		$object->items = listbuildercontrol::parseData($values,'items',true);
		$object->flip = isset($values['flip']);
		$object->cols = intval($values['cols']);
		$object->spacing = intval($values['spacing']);
		$object->required = isset($values['required']);
		
		return $object;
	}
}

?>
