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
 * Check Box Control class
 *
 * An HTML checkbox
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class checkboxcontrol extends formcontrol {

    var $flip = false;
    var $jsHooks = array();

    static function name() { return "Checkbox"; }
    static function isSimpleControl() { return true; }
    static function useGeneric() { return false; }
    static function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=>DB_DEF_BOOLEAN);
    }

    function __construct($default = 1,$flip = false,$required = false) {	
		
        $this->default = $default;
        $this->flip = $flip;
        $this->jsHooks = array();
        $this->required = $required;
    }
    
     function toHTML($label,$name) {
        if (!empty($this->id)) {
	        $divID  = ' id="'.$this->id.'Control"';
	        $for = ' for="'.$this->id.'"';
	    } else {
	        $divID  = '';
	        $for = '';
	    }
        $html = "<div".$divID." class=\"control checkbox";
        $html .= (!empty($this->required)) ? ' required">' : '">';
        if (!empty($this->flip)){
            $html .= "<label".$for." class=\"label\">".$label."</label>";
            $html .= "<table border=0 cellpadding=0 cellspacing=0><tr><td>";
            $html .= isset($this->newschool) ? $this->controlToHTML_newschool($name, $label) :$this->controlToHTML($name);
            $html .= "</td>";           
        } else {
            $html .= "<table border=0 cellpadding=0 cellspacing=0><tr>";
            $html .= "<td class=\"input\" nowrap>";
            $html .= "<label class=\"label\" style=\"background: transparent;\";></label>";
//            $html .= "</td><td>";
            $html .= isset($this->newschool) ? $this->controlToHTML_newschool($name, $label) :$this->controlToHTML($name);
            if (!empty($label) && $label != ' ') {
                $html .= "<label".$for." class=\"label\" style=\"text-align:left; white-space:nowrap; display:inline; width:auto;\">".$label."</label>";
            }
            $html .= "</td>";           
        }
        $html .= "</tr></table>";
        $html .= "</div>";
         if (!empty($this->description)) $html .= "<div class=\"control-desc\">".$this->description."</div>";
        return $html;
     }
    
/*
    function toHTML($label,$name) {
        if(empty($this->flipped)){
            $html = '<label>';
            $html .= $this->controlToHTML($name);
            $html .= "<span class=\"checkboxlabel\">".$label."</span>";
            $html .= "</label>";                
        }else{
            $html = '<label>';
            $html .= "<span class=\"checkboxlabel\">".$label."</span>";
            $html .= $this->controlToHTML($name);
            $html .= "</label>";                
        }
        return $html;
    }
*/

    function controlToHTML($name,$label=null) {
        $this->value = isset($this->value) ? $this->value : 1;
        $inputID  = (!empty($this->id)) ? ' id="'.$this->id.'"' : "";
        $html = '<input'.$inputID.' class="checkbox" type="checkbox" name="' . $name . '" value="'.$this->value.'"';
        if (!$this->flip) $html .= ' style="float:left;"';
        if ($this->default) $html .= ' checked="checked"';
        if ($this->tabindex >= 0) $html .= ' tabindex="' . $this->tabindex . '"';
        if ($this->accesskey != "") $html .= ' accesskey="' . $this->accesskey . '"';
        if ($this->disabled) $html .= ' disabled';
        foreach ($this->jsHooks as $type=>$val) {
            $html .= ' '.$type.'="'.$val.'"';
        }
        if (@$this->required) {
            $html .= 'required="'.rawurlencode($this->default).'" caption="'.rawurlencode($this->caption).'" ';
        }
        $html .= ' />';
        if (!empty($this->description)) $html .= "<div class=\"control-desc\">".$this->description."</div>";
        return $html;
    }
    
    //FIXME:  this is just here until we completely deprecate the old school checkbox
    //control calls in the old school forms
    function controlToHTML_newschool($name, $label) {
        $this->value = isset($this->value) ? $this->value : 1;
        
        $inputID  = (!empty($this->id)) ? ' id="'.$this->id.'"' : "";
        $this->name = empty($this->name) ? $name : $this->name;

		$html = "";
        // hidden value to force a false value in to the post array
        // if unchecked, the index won't even get in to the post array
        if ($this->postfalse) {
    	    $html .= '<input type="hidden" name="' . $name . '" value="0" />';	
        }

        $html .= '<input'.$inputID.' type="checkbox" name="' . $this->name . '" value="'.$this->value.'"';
        if ($this->size) $html .= ' size="' . $this->size . '"';
        if ($this->checked) $html .= ' checked="checked"';
        $html .= !empty($this->class)?' class="'.$this->class.' checkbox"':' class="checkbox"';
        if ($this->tabindex >= 0) $html .= ' tabindex="' . $this->tabindex . '"';
        if ($this->accesskey != "") $html .= ' accesskey="' . $this->accesskey . '"';
        if ($this->filter != "") {
            $html .= " onkeypress=\"return ".$this->filter."_filter.on_key_press(this, event);\" ";
            $html .= "onblur=\"".$this->filter."_filter.onblur(this);\" ";
            $html .= "onfocus=\"".$this->filter."_filter.onfocus(this);\" ";
            $html .= "onpaste=\"return ".$this->filter."_filter.onpaste(this, event);\" ";
        }
        if (!empty($this->readonly)||!empty($this->disabled)) $html .= ' disabled="disabled"';
        foreach ($this->jsHooks as $type=>$val) {
            $html .= ' '.$type.'="'.$val.'"';
        }
        //if (!empty($this->readonly)) $html .= ' disabled="disabled"';

        $caption = isset($this->caption) ? $this->caption : str_replace(array(":","*"), "", ucwords($label));
        if (!empty($this->required)) $html .= ' required="'.rawurlencode($this->default).'" caption="'.$caption.'" ';
        if (!empty($this->onclick)) $html .= ' onclick="'.$this->onclick.'" ';
        if (!empty($this->onchange)) $html .= ' onchange="'.$this->onchange.'" ';

        $html .= ' />';
        if (!empty($this->description)) $html .= "<div class=\"control-desc\">".$this->description."</div>";
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
            $object->description = "";
            $object->default = false;
            $object->flip = false;
            $object->required = false;
        } 
        if (empty($object->description)) $object->description = "";
        $form->register("identifier",gt('Identifier'),new textcontrol($object->identifier));
        $form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
        $form->register("default",gt('Default'), new checkboxcontrol($object->default,false));
        $form->register("flip","Caption on Left", new checkboxcontrol($object->flip,false));
        $form->register("required", gt('Required'), new checkboxcontrol($object->required,false));
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
        $object->description = $values['description'];
        $object->default = isset($values['default']);
        $object->flip = isset($values['flip']);
        $object->required = isset($values['required']);
        return $object;
    }
    
}

?>
