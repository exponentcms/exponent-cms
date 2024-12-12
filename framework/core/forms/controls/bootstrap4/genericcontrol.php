<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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

    static function name() { return "generic"; }

    var $placeholder = "";
    var $prepend = "";
    var $append = "";

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
            $for = ' for="'.createValidId($this->id).'"';
        } else {
            $divID  = ' id="'.$name.'Control"';
//            $for = '';
            $for = ' for="' . createValidId($name) . '"';
        }
//        if ($this->required) $label = "*" . $label;
        $disabled = $this->disabled == true ? "disabled" : "";
        if ($this->type !== 'hidden') {
            $class = empty($this->class) ? '' : ' '.$this->class;
            $html = '<div' . $divID . ' class="' . $this->type . '-control control ' . ($this->horizontal ? 'row ' : '') . 'form-group ' . $class . '" ' . $disabled;
            $html .= (!empty($this->required)) ? ' required="required">' : '>';
      		//$html .= "<label>";
            if($this->required) {
                $labeltag = '<span class="required" title="'.gt('This entry is required').'">*&#160;</span>' . $label;
            } else {
                $labeltag = $label;
            }
            if(empty($this->flip)){
                    $html .= empty($label) ? "" : "<label".$for." class=\"control-label\"".(($this->horizontal)?"col-sm-2 col-form-label":"" ).">". $labeltag."</label>";
                    $html .= $this->controlToHTML($name, $label);
            } else {
                    $html .= $this->controlToHTML($name, $label);
                    $html .= empty($label) ? "" : "<label".$for." class=\"control-label\"".">". $labeltag."</label>";
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
        $idname  = (!empty($this->id)) ? ' id="'.$this->id.'"' : ' id="'.$this->name.'"';
        $html = '';
        $html .= ($this->type != 'hidden' && $this->horizontal) ? '<div class="col-sm-10">' : '<div>';
        if (!empty($this->prepend)) {
            $html .= '<div class="input-group input-group-sm">';
            $html .= '<span class="input-group-prepend"><i class="input-group-text fas fa-'.$this->prepend.'"></i></span>';
        }
        if (!empty($this->append)) {
            $html .= '<div class="input-group input-group-sm">';
        }
        $html .= '<input'.$idname.' type="'.$this->type.'" name="' . $this->name . '" value="'.$this->default.'"';
        if ($this->size) $html .= ' size="' . $this->size . '"';
        if ($this->checked) $html .= ' checked="checked"';
        $html .= ' class="'.$this->type. " " . $this->class . ' form-control"';
        if ($this->tabindex >= 0) $html .= ' tabindex="' . $this->tabindex . '"';
        if ($this->maxlength != "") $html .= ' maxlength="' . $this->maxlength . '"';
        if ($this->accesskey != "") $html .= ' accesskey="' . $this->accesskey . '"';
        if ($this->min !== "") $html .= ' min="' . $this->min . '"';
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
        if ($this->multiple) $html .= ' multiple="multiple"';
        if ($this->disabled) $html .= ' disabled';
        $html .= $this->focus ? " autofocus" : "";
        foreach ($this->jsHooks as $type=>$val) {
            $html .= ' '.$type.'="'.$val.'"';
        }

        if (!empty($this->readonly)) $html .= ' readonly="readonly"';

//        $caption = !empty($this->caption) ? $this->caption : '';
        if (!empty($this->required)) $html .= ' required="required" ';
        if (!empty($this->onclick)) $html .= ' onclick="'.$this->onclick.'"';
        if (!empty($this->onchange)) $html .= ' onchange="'.$this->onchange.'"';

        $html .= ' />';
        if (!empty($this->prepend)) {
            $html .= '</div>';
        }
        if (!empty($this->append)) {
            $html .= '<span class="input-group-append"><i class="input-group-text fas fa-'.$this->append.'"></i></span>';
            $html .= '</div>';
        }
        if (!empty($this->description)) $html .= "<small class=\"form-text text-muted\">".$this->description."</small>";
        $html .= '</div>';
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

        $form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier),true, array('required'=>true));
        $form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("default",gt('Default'), new checkboxcontrol($object->default,false));
        $form->register("flip",gt('Caption on Right'), new checkboxcontrol($object->flip,false));
        $form->register("required", gt('Required'), new checkboxcontrol($object->required,true));
        if (!expJavascript::inAjaxAction())
            $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));

        return $form;
    }

    static function update($values, $object) {
        if ($object == null) $object = new genericcontrol();;
        if ($values['identifier'] == "") {
            $post = expString::sanitize($_POST);
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
        $object->flip = !empty($values['flip']);
        $object->required = !empty($values['required']);
        return $object;
    }

}

?>
