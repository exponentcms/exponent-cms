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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Text Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class textcontrol extends formcontrol {

    var $size = 40;
    var $maxlength = "";
    var $caption = "";

    function name() { return "Text Box"; }
    function isSimpleControl() { return true; }
    function useGeneric() { return false; }
    function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=>DB_DEF_STRING,
            DB_FIELD_LEN=>512);
    }

    function __construct($default = "", $size=40 , $disabled = false, $maxlength = 0, $filter = "", $required = false) {
        $this->default = $default;
        $this->size = $size;
        $this->disabled = $disabled;
        $this->maxlength = $maxlength;
        $this->filter = $filter;
        $this->required = $required;
        
    }

    function controlToHTML($name, $label) {
        $this->size = !empty($this->size) ? $this->size : 25;
        $inputID  = (!empty($this->id)) ? ' id="'.$this->id.'"' : "";
        $html  = '<input'.$inputID.' class="text" type="text" name="'.$name.'" ';
        $html .= "value=\"" . str_replace('"',"&quot;",$this->default) . "\" ";
        $html .= ($this->size?"size=\"".$this->size."\" ":"");
        $html .= ($this->disabled?"disabled ":"");
        $html .= ($this->maxlength?"maxlength=\"".$this->maxlength."\" ":"");
        $html .= ($this->tabindex>=0?"tabindex=\"".$this->tabindex."\" ":"");
        $html .= ($this->accesskey != ""?"accesskey=\"".$this->accesskey."\" ":"");
        if ($this->filter != "") {
            $html .= "onkeypress=\"return ".$this->filter."_filter.on_key_press(this, event);\" ";
            $html .= "onblur=\"".$this->filter."_filter.onblur(this);\" ";
            $html .= "onfocus=\"".$this->filter."_filter.onfocus(this);\" ";
            $html .= "onpaste=\"return ".$this->filter."_filter.onpaste(this, event);\" ";
        }

        $caption = !empty($this->caption) ? $this->caption : str_replace(array(":","*"), "", ucwords($label));
        if (!empty($this->required)) $html .= ' required="'.rawurlencode($this->default).'" caption="'.$caption.'" ';
        $html .= "/>";
        return $html;
    }

    function form($object) {
        if (!defined("SYS_FORMS")) require_once(BASE."subsystems/forms.php");
        exponent_forms_initialize();

        $form = new form();
        if (!isset($object->identifier)) {
            $object->identifier = "";
            $object->caption = "";
            $object->default = "";
            $object->size = 0;
            $object->maxlength = 0;
            $object->required = false;
        }
        $i18n = exponent_lang_loadFile('subsystems/forms/controls/textcontrol.php');

        $form->register("identifier",$i18n['identifier'],new textcontrol($object->identifier));
        $form->register("caption",$i18n['caption'], new textcontrol($object->caption));
        $form->register("default",$i18n['default'], new textcontrol($object->default));
        $form->register("size",$i18n['size'], new textcontrol((($object->size==0)?"":$object->size),4,false,3,"integer"));
        $form->register("maxlength",$i18n['maxlength'], new textcontrol((($object->maxlength==0)?"":$object->maxlength),4,false,3,"integer"));
        $form->register("required", $i18n['required'], new checkboxcontrol($object->required,false));
        $form->register("submit","",new buttongroupcontrol($i18n['save'],'',$i18n['cancel']));
        return $form;
    }

    function update($values, $object) {
        if ($object == null) $object = new textcontrol();
        if ($values['identifier'] == "") {
            $i18n = exponent_lang_loadFile('subsystems/forms/controls/textcontrol.php');
            $post = $_POST;
            $post['_formError'] = $i18n['id_req'];
            exponent_sessions_set("last_POST",$post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->default = $values['default'];
        $object->size = intval($values['size']);
        $object->maxlength = intval($values['maxlength']);
        $object->required = isset($values['required']);
        return $object;
    }

}

?>
