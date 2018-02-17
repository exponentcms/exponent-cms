<?php

##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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
 * Text Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class textcontrol extends formcontrol {

    var $filter = '';
    var $caption = "";
    var $placeholder = "";
    var $pattern = "";
    var $size = 40;
    var $maxlength = "";

    static function name() { return "Text Box"; }
    static function isSimpleControl() { return true; }
    static function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=>DB_DEF_STRING,
            DB_FIELD_LEN=>512);
    }

    function __construct($default = "", $size=40 , $disabled = false, $maxlength = 0, $filter = "", $required = false, $placeholder = "", $pattern="") {
        $this->disabled = $disabled;
        $this->default = $default;
        $this->placeholder = $placeholder;
        $this->pattern = $pattern;
        $this->size = $size;
        $this->maxlength = $maxlength;
        $this->filter = $filter;
        $this->required = $required;
    }

    function controlToHTML($name, $label) {
        $this->size = !empty($this->size) ? $this->size : 25;
        $idname  = (!empty($this->id)) ? $this->id : $name;
        $idname = createValidId($idname);
        if ($this->type != 'text') {
            $extra_class = ' ' . $this->type;
        } else {
            $extra_class = '';
        }
        $html  = '<input id="' . $idname . '" class="text form-control' . $extra_class . '" type="' . $this->type . '" name="' . $name . '"';
        $html .= " value=\"" . str_replace('"', "&quot;", $this->default) . "\"";
        $html .= $this->size ? " size=\"" . $this->size . "\"" : "";
        $html .= $this->multiple ? ' multiple="multiple"' : "";
        $html .= $this->disabled ? " disabled='disabled'" : "";
        $html .= $this->focus ? " autofocus" : "";
        $html .= $this->maxlength ? " maxlength=\"" . $this->maxlength . "\"" : "";
        $html .= $this->tabindex >= 0 ? " tabindex=\"" . $this->tabindex . "\"" : "";
        $html .= ($this->accesskey != "") ? " accesskey=\"" . $this->accesskey . "\"" : "";
        $html .= $this->placeholder ? " placeholder=\"" . $this->placeholder . "\"" : "";
        if (!empty($this->pattern)) $html .= " pattern=\"" . $this->pattern . "\"";
        if ($this->filter != "") {
            $html .= " onkeypress=\"return " . $this->filter . "_filter.on_key_press(this, event);\"";
            $html .= " onblur=\"" . $this->filter . "_filter.onblur(this);\"";
            $html .= " onfocus=\"" . $this->filter . "_filter.onfocus(this);\"";
            $html .= " onpaste=\"return " . $this->filter . "_filter.onpaste(this, event);\"";
        }
        $caption = !empty($this->caption) ? $this->caption : str_replace(array(":","*"), "", ucwords($label));
        if (!empty($this->required)) $html .= ' required="required" caption="'.$caption.'"';
        $html .= " />";
        if (!empty($this->description)) $html .= "<div class=\"".(bs3()?"help-block":"control-desc")."\">".$this->description."</div>";
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
            $object->placeholder = "";
            $object->pattern = "";
            $object->size = 0;
            $object->maxlength = 0;
            $object->required = false;
            $object->is_hidden = false;
        }
        if (empty($object->description)) $object->description = "";
        $form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier));
        $form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
        $form->register("default",gt('Default value'), new textcontrol($object->default));
        $form->register("placeholder",gt('Placeholder'), new textcontrol($object->placeholder));
        $form->register("pattern",gt('Pattern'), new textcontrol($object->pattern));
        $form->register("size",gt('Size'), new textcontrol((($object->size==0)?"":$object->size),4,false,3,"integer"));
        $form->register("maxlength",gt('Maximum Length'), new textcontrol((($object->maxlength==0)?"":$object->maxlength),4,false,3,"integer"));
        $form->register("required", gt('Make this a required field'), new checkboxcontrol($object->required,false));
        $form->register("is_hidden", gt('Make this a hidden field on initial entry'), new checkboxcontrol(!empty($object->is_hidden),false));
        if (!expJavascript::inAjaxAction())
            $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
        return $form;
    }

    static function update($values, $object) {
        $this_control = !empty($values['control_type']) ? $values['control_type'] : 'textcontrol';
//        if ($object == null) $object = new textcontrol();
        if ($object == null) $object = new $this_control();
        if ($values['identifier'] == "") {
            $post = expString::sanitize($_POST);
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
        if (isset($values['default'])) $object->default = $values['default'];
        if (isset($values['placeholder'])) $object->placeholder = $values['placeholder'];
        if (isset($values['pattern'])) $object->pattern = $values['pattern'];
        if (isset($values['size'])) $object->size = (int)($values['size']);
        if (isset($values['maxlength'])) $object->maxlength = (int)($values['maxlength']);
        $object->required = !empty($values['required']);
        $object->is_hidden = !empty($values['is_hidden']);
        return $object;
    }

}

?>
