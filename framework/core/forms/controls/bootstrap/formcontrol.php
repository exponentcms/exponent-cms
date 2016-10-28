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

if (!defined('EXPONENT')) exit('');

/**
 * Base Form Control Class
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
abstract class formcontrol {

    var $id = null;
    var $name = "";
    var $description = "";
	var $accesskey = "";
    var $class = "";
	var $default = "";
	var $disabled = false;
    var $required = false;
    var $multiple = false;
    var $flip = false;
    var $is_hidden = false;
    var $focus = false;
	var $tabindex = -1;
	var $inError = 0; // This will ONLY be set by the parent form.
	var $type = 'text';
    var $horizontal = false;
    var $horizontal_top = false;
    var $jsHooks = array();

	static function name() { return "formcontrol"; }

    /**
   	 * Is this a Static Control?
   	 * Used to determine if control has field data behind it
        *
   	 * @return bool
   	 */
    static function isStatic() { return false; }

	/**
	 * Is this a Simple Control?
	 * Used to determine if control is available for the Forms (design) module
     *
	 * @return bool
	 */
	static function isSimpleControl() { return false; }

    /**
   	 * Use the Generic Control instead?
   	 * Used to determine if control is actually a generic control
        *
   	 * @return bool
   	 */
    static function useGeneric() { return false; }

    /**
     * returns the table field definition for this control
     *
     * @static
     * @return array
     */
    static function getFieldDefinition() { return array(); }

    /**
     * Place the control in the form with label and description
     *
     * @param $label
     * @param $name
     * @return string
     */
    function toHTML($label,$name) {
        if (!empty($this->_ishidden)) {
            $this->name = empty($this->name) ? $name : $this->name;
            $idname  = (!empty($this->id)) ? ' id="'.$this->id.'"' : "";
    		$html = '<input type="hidden"' . $idname . ' name="' . $this->name . '" value="'.$this->default.'"';
    		$html .= ' />';
    		return $html;
        } else {
            if (!empty($this->id)) {
                $divID = ' id="' . $this->id . 'Control"';
                $for = ' for="' . $this->id . '"';
            } else {
                $divID = ' id="' . $name . 'Control"';
//                $for = '';
                $for = ' for="' . $name . '"';
            }

            $disabled = $this->disabled != 0 ? "disabled='disabled'" : "";
            $class = empty($this->class) ? '' : $this->class;
            if ($this->horizontal_top)
                $class .= ' col-sm-10 ';

            $html = "<div" . $divID . " class=\"" . $this->type . "-control control " . (bs3()?'form-group':'control-group') . " " . $class . $disabled;
            $html .= !empty($this->required) ? ' required">' : '">';
            //$html .= "<label>";
            if ($this->required) {
                $labeltag = '<span class="required" title="' . gt(
                        'This entry is required'
                    ) . '">*&#160;</span>' . $label;
            } else {
                $labeltag = $label;
            }
            if (empty($this->flip)) {
                $html .= (!empty($label)) ? "<label" . $for . " class=\"".(bs3()?"control-label":"") . (($this->horizontal == 1)?' col-sm-2':'') ."\">" . $labeltag . "</label>" : "";
                $html .= $this->controlToHTML($name, $label);
            } else {
                $html .= $this->controlToHTML($name, $label);
                $html .= (!empty($label)) ? "<label" . $for . " class=\"".(bs3()?"control-label":"")."\">" . $labeltag . "</label>" : "";
            }
            //$html .= "</label>";
            $html .= "</div>";
            return $html;
        }
	}

    /**
     * Place the editable control on the edit form
     *
     * @param $name
     * @param $label
     * @return string
     */
    function controlToHTML($name,$label) {
		return "";
	}

    /**
     * Parse the control value
     *
     * @static
     * @param $original_name
     * @param $formvalues
     * @return string
     */
    static function parseData($original_name,$formvalues) {
		return (isset($formvalues[$original_name])?$formvalues[$original_name]:"");
	}

    /**
     * Convert a value to fit the control
     *
     * @static
     * @param $original_name
     * @param $formvalues
     * @return string
     */
    static function convertData($original_name,$formvalues) {
		return (isset($formvalues[$original_name])?trim($formvalues[$original_name]):"");
	}

    /**
     * Event hook for when control is un-registered (removed) on a form
     *
     * @param $form
     * @return bool
     */
    function onUnRegister(&$form) { // Do we need the explicit ref op??
		return true;
	}

    /**
     * Event hook for when control is registered on a form
     *
     * @param $form
     * @return bool
     */
    function onRegister(&$form) { // Do we need the explicit ref op??
		return true;
	}

    /**
     * Format the control's data for user display
     *
     * @param $db_data
     * @param $ctl
     * @return string
     */
    static function templateFormat($db_data, $ctl) {
		return isset($db_data)?$db_data:"";
	}

    /**
     * Create the Form to edit the control settings
     *
     * @param $object
     */
    static function form($object) {
        return;
    }

    /**
     * Update the control settings
     *
     * @param $values
     * @param $object
     */
    static function update($values, $object) {
        return;
    }

}

?>
