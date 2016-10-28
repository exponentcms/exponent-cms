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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Check Box Control class
 *
 * An HTML checkbox
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class checkboxcontrol extends formcontrol {

    var $default = false;
    var $value = "1";
    var $newschool = false;
    var $postfalse = false;
    var $filter = '';
    var $caption = '';
    var $onchange = '';
    var $onclick = '';

    static function name() {
        return "Options - Checkbox";
    }

    static function isSimpleControl() {
        return true;
    }

    static function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=> DB_DEF_BOOLEAN);
    }

    function __construct($default = false, $flip = false, $required = false) {
        $this->default  = $default; //checked
        $this->flip     = $flip;
//        $this->jsHooks  = array();
        $this->required = $required;
    }

    function toHTML($label, $name) {
        if (!empty($this->_ishidden)) {
            $this->name = empty($this->name) ? $name : $this->name;
            $idname  = (!empty($this->id)) ? ' id="'.$this->id.'"' : "";
    		$html = '<input type="hidden"' . $idname . ' name="' . $this->name . '" value="'.(int)$this->default.'"';
    		$html .= ' />';
    		return $html;
        } else {
            if (!empty($this->id)) {
                $divID = $this->id . 'Control';
                $for = $this->id;
            } else {
                $divID = $name . 'Control';
                if (substr($name, -2) == '[]') {
                    $for   = $name . $this->value;
                } else {
                    $for   = $name;
                }
            }
            $divID = createValidId($divID, $this->value);
            $for = ' for="' . createValidId($for, $this->value) . '"';
            if (empty($label)) {
                $for = '';
            }
            $html = '<div id="' . $divID . '" class="checkbox control form-group';
            $html .= (!empty($this->class)) ? ' ' . $this->class : '';
            $html .= (!empty($this->required)) ? ' required">' : '">';
            $html .= ($this->horizontal) ? '<div class="col-sm-offset-2 col-sm-10">' : '';

//            $html .= "<div class=\"checkbox\"><label class=\"control-label\">".$label;
            $html .= "<label" . $for . " class=\"control-label\">";
            if (!$this->horizontal) $html .= $label;
            $html .= $this->controlToHTML($name, $label);
            if ($this->horizontal) $html .= $label;
//            $html .= "</label></div>";
            $html .= "</label>";

            if (!empty($this->description)) $html .= "<span class=\"help-block\">" . $this->description . "</span>";
            $html .= ($this->horizontal) ? '</div>' : '';
            $html .= "</div>";
            return $html;
        }
    }

    //control calls in the old school forms
    function controlToHTML($name, $label) {
        $this->value = isset($this->value) ? $this->value : 1;

        $idname     = (!empty($this->id)) ? $this->id  : $name;
//        if (substr($this->name, -2) == '[]') {
//            $idname .= $this->value;
//        }
        $idname = createValidId($idname, $this->value);

        $this->name = empty($this->name) ? $name : $this->name;

        $html = "";
        // hidden value to force a false value in to the post array
        // if unchecked, the index won't even get in to the post array
        if (!empty($this->postfalse)) {
            $html .= '<input type="hidden" name="' . $name . '" value="0" />';
        }

        $html .= '<input id="' . $idname . '" type="checkbox" name="' . $this->name . '" value="' . $this->value . '"';
        if (!empty($this->size)) $html .= ' size="' . $this->size . '"';
        if (!empty($this->default)) $html .= ' checked="checked"';
        $html .= !empty($this->class) ? ' class="' . $this->class . ' checkbox form-control"' : ' class="checkbox form-control"';
        if ($this->tabindex >= 0) $html .= ' tabindex="' . $this->tabindex . '"';
        if ($this->accesskey != "") $html .= ' accesskey="' . $this->accesskey . '"';

        if (!empty($this->filter)) {
            $html .= " onkeypress=\"return " . $this->filter . "_filter.on_key_press(this, event);\"";
            $html .= " onblur=\"" . $this->filter . "_filter.onblur(this);\"";
            $html .= " onfocus=\"" . $this->filter . "_filter.onfocus(this);\"";
            $html .= " onpaste=\"return " . $this->filter . "_filter.onpaste(this, event);\"";
        }
        if (!empty($this->readonly) || !empty($this->disabled)) $html .= ' disabled="disabled"';
        $html .= $this->focus ? " autofocus" : "";
        foreach ($this->jsHooks as $type=> $val) {
            $html .= ' ' . $type . '="' . $val . '"';
        }

        $caption = isset($this->caption) ? $this->caption : str_replace(array(":", "*"), "", ucwords($label));
        if (!empty($this->required)) $html .= ' required="' . rawurlencode($this->value) . '" caption="' . $caption . '"';
        if (!empty($this->onclick)) $html .= ' onclick="' . $this->onclick . '"';
        if (!empty($this->onchange)) $html .= ' onchange="' . $this->onchange . '"';

        $html .= ' />';
//        eLog('Checkbox:'.$name.', Value:\''.$this->value.'\', Checked:'.self::templateFormat($this->default, null));
        return $html;
    }

    function controlToHTML_newschool($name, $label) {
        return $this->controlToHTML($name, $label);
    }

    static function parseData($name, $values, $for_db = false) {
        return (isset($values[$name]) && !empty($values[$name])) ? 1 : 0;
    }

    static function convertData($original_name,$formvalues) {
        if (empty($formvalues[$original_name])) return false;
        if (strtolower($formvalues[$original_name]) == 'no') return false;
        if (strtolower($formvalues[$original_name]) == 'off') return false;
        if (strtolower($formvalues[$original_name]) == 'false') return false;
		return true;
	}

    static function templateFormat($db_data, $ctl) {
        return ($db_data == 1) ? gt("Yes") : gt("No");
    }

    static function form($object) {
        $form = new form();
        if (empty($object)) $object = new stdClass();
        if (!isset($object->identifier)) {
            $object->identifier  = "";
            $object->caption     = "";
            $object->description = "";
            $object->default     = false;
            $object->flip        = false;
            $object->required    = false;
            $object->is_hidden = false;
        }
        if (empty($object->description)) $object->description = "";
        $form->register("identifier", gt('Identifier/Field'), new textcontrol($object->identifier));
        $form->register("caption", gt('Caption'), new textcontrol($object->caption));
        $form->register("description", gt('Control Description'), new textcontrol($object->description));
        $form->register("default", gt('Default value'), new checkboxcontrol($object->default, false));
        $form->register("flip", "Caption on Left", new checkboxcontrol($object->flip, false));
        $form->register("required", gt('Make this a required field'), new checkboxcontrol($object->required, false));
        $form->register("is_hidden", gt('Make this a hidden field on initial entry'), new checkboxcontrol(!empty($object->is_hidden),false));
        if (!expJavascript::inAjaxAction())
            $form->register("submit", "", new buttongroupcontrol(gt('Save'), '', gt('Cancel'), "", 'editable'));

        return $form;
    }

    static function update($values, $object) {
        if ($object == null) $object = new checkboxcontrol();
        if ($values['identifier'] == "") {
            $post = expString::sanitize($_POST);
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST", $post);
            return null;
        }
        $object->identifier  = $values['identifier'];
        $object->caption     = $values['caption'];
        $object->description = $values['description'];
        $object->default     = !empty($values['default']);
        $object->flip        = !empty($values['flip']);
        $object->required    = !empty($values['required']);
        $object->is_hidden   = !empty($values['is_hidden']);
        return $object;
    }

}

?>
