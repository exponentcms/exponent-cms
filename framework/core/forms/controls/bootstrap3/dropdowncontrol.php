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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Dropdown Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class dropdowncontrol extends formcontrol {

    var $items = array();
    var $size = 1;
    var $jsHooks = array();
    var $include_blank = false;
    var $type = 'select';
    var $class = '';
    
    static function name() { return "Drop Down List"; }
    static function isSimpleControl() { return true; }
    static function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=>DB_DEF_STRING,
            DB_FIELD_LEN=>255);
    }
    
    function __construct($default = "",$items = array(), $include_blank = false, $multiple=false) {
        $this->default = $default;
        $this->items = $items;
        $this->include_blank = $include_blank;
        $this->required = false;
        $this->multiple = $multiple;
    }
    
    function controlToHTML($name,$label=null) {
        $inputID  = (!empty($this->id)) ? ' id="'.$this->id.'"' : (!empty($name)?' id="'.$name.'"':"");
        $disabled = $this->disabled != false ? "disabled" : "";
        $html = '<select'.$inputID.' name="' . $name;
        if ($this->multiple) $html.= '[]';
        $html .= '" size="' . $this->size . '"';
        $html .= ' class="'.$this->class.' form-control select control '.$disabled.'"';
        if ($this->disabled) $html .= ' disabled';
        if ($this->tabindex >= 0) $html .= ' tabindex="' . $this->tabindex . '"';
        foreach ($this->jsHooks as $hook=>$action) {
            $html .= " $hook=\"$action\"";
        }
        if (@$this->required) {
            $html .= 'required="'.rawurlencode($this->default).'" caption="'.rawurlencode($this->caption).'" ';
        }
        if (!empty($this->multiple)) $html .= ' multiple';
        if (!empty($this->onchange)) $html .= ' onchange="'.$this->onchange.'" ';
        $html .= '>';

        if (is_bool($this->include_blank) && $this->include_blank == true) {
            $html .= '<option value=""></option>';
        } elseif (is_string($this->include_blank) && !empty($this->include_blank)) {
            $html .= '<option value="">'.$this->include_blank.'</option>';
        }

        if (!empty($this->items)) foreach ($this->items as $value=>$caption) {
            $html .= '<option value="' . $value . '"';
            if (is_array($this->default)) {
                if (in_array($value, $this->default)) $html .= " selected";
            } else {
                if (!empty($this->default) && $value == $this->default) $html .= " selected";
            }
            $html .= '>' . $caption . '</option>';
        }
        $html .= '</select>';             
        if (!empty($this->description)) $html .= "<div class=\"help-block\">".$this->description."</div>";
        return $html;
    }
    
    static function form($object) {
        $form = new form();
        if (empty($object)) $object = new stdClass();
        if (!isset($object->identifier)) {
            $object->identifier = "";
            $object->caption = "";
            $object->description = "";
            $object->default = "";
            $object->size = 1;
            $object->items = array();
            $object->required = false;
        } 
        if (empty($object->description)) $object->description = "";
        $form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier));
        $form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
        $form->register("items",gt('Items'), new listbuildercontrol($object->items,null));
        $form->register("default",gt('Default'), new textcontrol($object->default));
        $form->register("size",gt('Size'), new textcontrol($object->size,3,false,2,"integer"));
        $form->register("required", gt('Make this a required field.'), new checkboxcontrol($object->required,true));
        $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
        return $form;
    }
    
    static function update($values, $object) {
        if ($values['identifier'] == "") {
            $post = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        if ($object == null) $object = new dropdowncontrol();
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
        $object->default = $values['default'];
        $object->items = listbuildercontrol::parseData($values,'items',true);
        if (isset($values['size'])) $object->size = (intval($values['size']) <= 0)?1:intval($values['size']);
        $object->required = isset($values['required']);
        return $object;
    }
}

?>
