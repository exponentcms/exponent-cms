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
 * Generic HTML Input Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class genericcontrol extends formcontrol {

    var $flip = false;
    var $jsHooks = array();
    
    static function name() { return "generic"; }

    function __construct($type="", $default = false, $class="", $filter="", $checked=false, $required = false, $validate="", $onclick="", $label="", $maxlength="", $placeholder="", $pattern="") {
        $this->type = (empty($type)) ? "text" : $type;
        $this->default = $default;
        $this->class = $class;
        $this->checked = $checked;
        $this->jsHooks = array();
        $this->filter = $filter;
        $this->required = $required;
        $this->validate = $validate;
        $this->onclick = $onclick;
        $this->maxlength = $maxlength;
        $this->size = '';
        $this->min = '';
        $this->max = '';
        $this->step = '';
        $this->placeholder = $placeholder;
        $this->pattern = $pattern;
    }
    
    function toHTML($label,$name) {
        if (!empty($this->id)) {
            $divID  = ' id="'.$this->id.'Control"';
            $for = ' for="'.$this->id.'"';
        } else {
//            $divID  = '';
            $divID  = ' id="'.$name.'Control"';
            $for = '';
        }
//        if ($this->required) $label = "*" . $label;
        $disabled = $this->disabled == true ? "disabled" : "";
        if ($this->type != 'hidden') {
            $class = empty($this->class) ? '' : ' '.$this->class;
            $html = '<div'.$divID.' class="'.$this->type.'-control control'." ".$class." ".$disabled;
            $html .= (!empty($this->required)) ? ' required">' : '">';
      		//$html .= "<label>";
            if($this->required) {
                $labeltag = '<span class="required" title="'.gt('This entry is required').'">*</span>' . $label;
            } else {
                $labeltag = $label;
            }
            if(empty($this->flip)){
                    $html .= empty($label) ? "" : "<label".$for.">". $labeltag."</label>";
                    $html .= $this->controlToHTML($name, $label);
            } else {
                    $html .= $this->controlToHTML($name, $label);
                    $html .= empty($label) ? "" : "<label".$for.">". $labeltag."</label>";
            }
            $html .= "</div>";
        } else {
            $html = $this->controlToHTML($name, $label);
        }
        return $html;
    }
    
    function controlToHTML($name, $label) {
        $this->size = !empty($this->size) ? $this->size : 20;
        $this->name = empty($this->name) ? $name : $this->name;
        $inputID  = (!empty($this->id)) ? ' id="'.$this->id.'"' : "";
        $html = '';
        $framework = expSession::get('framework');
        if ($framework == 'bootstrap') {
            if (!empty($this->prepend)) {
                $html .= '<div class="input-prepend">';
                $html .= '<span class="add-on"><i class="icon-'.$this->prepend.'"></i></span>';
            }
        }
        $html .= '<input'.$inputID.' type="'.$this->type.'" name="' . $this->name . '" value="'.$this->default.'"';
        if ($this->size) $html .= ' size="' . $this->size . '"';
        if ($this->checked) $html .= ' checked="checked"';
        $html .= ' class="'.$this->type. " " . $this->class . '"';
        if ($this->tabindex >= 0) $html .= ' tabindex="' . $this->tabindex . '"';
        if ($this->maxlength != "") $html .= ' maxlength="' . $this->maxlength . '"';
        if ($this->accesskey != "") $html .= ' accesskey="' . $this->accesskey . '"';
        if ($this->min != "") $html .= ' min="' . $this->min . '"';
        if ($this->max != "") $html .= ' max="' . $this->max . '"';
        if ($this->step != "") $html .= ' step="' . $this->step . '"';
        if ($this->placeholder != "") $html .= " placeholder=\"".$this->placeholder."\"";
        if ($this->pattern != "") $html .= " pattern=\"".$this->pattern."\"";
        if ($this->filter != "") {
            $html .= " onkeypress=\"return ".$this->filter."_filter.on_key_press(this, event);\"";
            $html .= " onblur=\"".$this->filter."_filter.onblur(this);\"";
            $html .= " onfocus=\"".$this->filter."_filter.onfocus(this);\"";
            $html .= " onpaste=\"return ".$this->filter."_filter.onpaste(this, event);\"";
        }
        if ($this->disabled) $html .= ' disabled';
        foreach ($this->jsHooks as $type=>$val) {
            $html .= ' '.$type.'="'.$val.'"';
        }

        if (!empty($this->readonly)) $html .= ' readonly="readonly"';

        $caption = !empty($this->caption) ? $this->caption : '';
        if (!empty($this->required)) $html .= ' required="'.rawurlencode($this->default).'" caption="'.$caption.'"';
        if (!empty($this->onclick)) $html .= ' onclick="'.$this->onclick.'"';
        if (!empty($this->onchange)) $html .= ' onchange="'.$this->onchange.'"';

        $html .= ' />';
        if ($framework == 'bootstrap' && !empty($this->prepend)) {
            $html .= '</div>';
        }
        if (!empty($this->description)) $html .= "<div class=\"help-block\">".$this->description."</div>";
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
        $form->register("required", gt('Required'), new checkboxcontrol($object->required,true));
        $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
        
        return $form;
    }
    
    static function update($values, $object) {
        if ($object == null) $object = new genericcontrol();;
        if ($values['identifier'] == "") {
            $post = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        if (empty($object->type)) {
            $object->type = (empty($values['control_type'])) ? "text" : substr($values['control_type'],0,-7);
        }
        if (!empty($values['size'])) $object->size = $values['size'];
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->default = isset($values['default']);
        $object->flip = isset($values['flip']);
        $object->required = isset($values['required']);
        return $object;
    }
    
}

?>
