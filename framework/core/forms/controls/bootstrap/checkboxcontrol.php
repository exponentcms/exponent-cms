<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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

    var $flip = false;
    var $jsHooks = array();

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

    function __construct($default = 1, $flip = false, $required = false) {
        $this->default  = $default;
        $this->flip     = $flip;
        $this->jsHooks  = array();
        $this->required = $required;
    }

    /**
     * Fully formated control including label and description
     *
     * @param $label
     * @param $name
     *
     * @return string
     */
    function toHTML($label, $name) {
        if (!empty($this->_ishidden)) {
            $this->name = empty($this->name) ? $name : $this->name;
            $inputID  = (!empty($this->id)) ? ' id="'.$this->id.'"' : "";
    		$html = '<input type="hidden"' . $inputID . ' name="' . $this->name . '" value="'.$this->default.'"';
    		$html .= ' />';
    		return $html;
        } else {
            if (!empty($this->id)) {
                $divID = ' id="' . $this->id . 'Control"';
                $for = ' for="' . $this->id . '"';
            } else {
//            $divID = '';
                $divID = ' id="' . $name . 'Control"';
//            $for   = '';
                $for = ' for="' . $name . '"';
            }
            if (empty($label)) {
                $for = '';
            }
            $html = "<div" . $divID . " class=\"checkbox control form-group";
            $html .= (!empty($this->required)) ? ' required">' : '">';
            $html .= "<label" . $for . " class=\" ".(bs3()?"control-label ":"")."checkbox control\" style=\"display:inline;\">";
            if (!empty($this->flip)) $html .= $label;
            $html .= isset($this->newschool) ? $this->controlToHTML_newschool($name, $label) : $this->controlToHTML(
                $name
            );
            if (empty($this->flip)) $html .= $label;
            $html .= "</label>";
            if (!empty($this->description)) $html .= "<span class=\"help-block\">" . $this->description . "</span>";
            $html .= "</div>";
            return $html;
        }
    }

    function controlToHTML($name, $label = null) {
        $this->value = isset($this->value) ? $this->value : 1;
//        $inputID     = (!empty($this->id)) ? ' id="' . $this->id . '"' : "";
        $inputID     = (!empty($this->id)) ? ' id="' . $this->id . '"' : ' id="' . $name . '"';
//        $html        = '<input' . $inputID . ' class="checkbox control" type="checkbox" name="' . $name . '" value="' . $this->value . '"';
        $html        = '<input' . $inputID . ' class="checkbox form-control" type="checkbox" name="' . $name . '" value="' . $this->value . '"';
        if (!$this->flip) $html .= ' style="float:left;"';
        if (!empty($this->checked) && $this->checked) $html .= ' checked="checked"';
//        if ($this->default) $html .= ' checked="checked"';
        if ($this->tabindex >= 0) $html .= ' tabindex="' . $this->tabindex . '"';
        if ($this->accesskey != "") $html .= ' accesskey="' . $this->accesskey . '"';
        if ($this->disabled) $html .= ' disabled';
        $html .= $this->focus ? " autofocus" : "";
        foreach ($this->jsHooks as $type=> $val) {
            $html .= ' ' . $type . '="' . $val . '"';
        }
        if (@$this->required) {
            $html .= 'required="' . rawurlencode($this->default) . '" caption="' . rawurlencode($this->caption) . '" ';
        }
        $html .= ' />';
//        if (!empty($this->description)) $html .= "<div class=\"control-desc\">".$this->description."</div>";
        return $html;
    }

    //FIXME:  this is just here until we completely deprecate the old school checkbox
    //control calls in the old school forms
    /**
     * Input control only, label and description left to calling function
     *
     * @param $name
     * @param $label
     *
     * @return string
     */
    function controlToHTML_newschool($name, $label) {
        $this->value = isset($this->value) ? $this->value : 1;

        $inputID    = (!empty($this->id)) ? ' id="' . $this->id . '"' : "";
        $this->name = empty($this->name) ? $name : $this->name;

        $html = "";
        // hidden value to force a false value in to the post array
        // if unchecked, the param won't even get in to the post array
        if (!empty($this->postfalse) && $this->postfalse) {
            $html .= '<input type="hidden" name="' . $name . '" value="0" />';
        }

        $html .= '<input' . $inputID . ' type="checkbox" name="' . $this->name . '" value="' . $this->value . '"';
        if (!empty($this->size) && $this->size) $html .= ' size="' . $this->size . '"';
        if (!empty($this->checked) && $this->checked) $html .= ' checked="checked"';
        $html .= !empty($this->class) ? ' class="' . $this->class . ' checkbox control"' : ' class="checkbox control"';
        if ($this->tabindex >= 0) $html .= ' tabindex="' . $this->tabindex . '"';
        if ($this->accesskey != "") $html .= ' accesskey="' . $this->accesskey . '"';
//        if ($this->filter != "") {
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
        //if (!empty($this->readonly)) $html .= ' disabled="disabled"';

        $caption = isset($this->caption) ? $this->caption : str_replace(array(":", "*"), "", ucwords($label));
        if (!empty($this->required)) $html .= ' required="' . rawurlencode($this->default) . '" caption="' . $caption . '"';
        if (!empty($this->onclick)) $html .= ' onclick="' . $this->onclick . '"';
        if (!empty($this->onchange)) $html .= ' onchange="' . $this->onchange . '"';

        $html .= ' />';
        return $html;
    }

    static function parseData($name, $values, $for_db = false) {
        return isset($values[$name]) ? 1 : 0;
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
        $form->register("submit", "", new buttongroupcontrol(gt('Save'), '', gt('Cancel'), "", 'editable'));

        return $form;
    }

    static function update($values, $object) {
        if ($object == null) $object = new checkboxcontrol();
        if ($values['identifier'] == "") {
            $post               = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST", $post);
            return null;
        }
        $object->identifier  = $values['identifier'];
        $object->caption     = $values['caption'];
        $object->description = $values['description'];
        $object->default     = isset($values['default']);
        $object->flip        = isset($values['flip']);
        $object->required    = isset($values['required']);
        $object->is_hidden = isset($values['is_hidden']);
        return $object;
    }

}

?>
