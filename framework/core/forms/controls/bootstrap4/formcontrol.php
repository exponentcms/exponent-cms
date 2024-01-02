<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
#[AllowDynamicProperties]
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
    var $horizontal = false;  // label on side
    var $horizontal_top = false; //fixme ??
    var $width = '';
    var $widths = array(
        '' => 'Full',
        'col-sm-8' => '8 Col',
        'col-sm-6' => '6 Col',
        'col-sm-4' => '4 Col',
        'col-sm-3' => '3 Col',
        'col-sm-2' => '2 Col',
        'col-sm-1' => '1 Col'
    );
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
     * Generic magic method
     *
     * @param $property
     * @return null
     */
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return null;
    }

    /**
     *  Generic magic method
     *  We MUST create/set non-existing properties for Exponent code to work
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value) {
//        if (property_exists($this, $property)) {
            $this->$property = $value;
//        }
    }

    /**
     * Generic magic method
     *
     * @param $property
     * @return bool
     */
    public function  __isset($property) {
        return isset($this->$property);
    }

    /**
     * Generic magic method
     *
     * @param $property
     */
    public function __unset($property) {
        unset($this->$property);
    }

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
                $for = ' for="' . createValidId($this->id) . '"';
            } else {
                $divID = ' id="' . $name . 'Control"';
//                $for = '';
                $for = ' for="' . createValidId($name) . '"';
            }

            $disabled = $this->disabled != 0 ? "disabled='disabled'" : "";
            $class = empty($this->class) ? '' : $this->class;
//            if ($this->horizontal_top)
            if ($this->horizontal)
                $class .= ' col-sm-10 ';
            elseif (empty($this->width)) {
                $class .= " col-sm-12";
            } else {
                $class .= " " . $this->width;
            }
            $html = "<div" . $divID . " class=\"" . $this->type . "-control control " . ($this->horizontal ? 'row ' : '') . 'form-group ' . $class . "\" " . $disabled;
            $html .= !empty($this->required) ? ' required>' : '>';
            //$html .= "<label>";
            if ($this->required) {
                $labeltag = '<span class="required" title="' . gt(
                        'This entry is required'
                    ) . '">*&#160;</span>' . $label;
            } else {
                $labeltag = $label;
            }
            if (empty($this->flip)) {
                $html .= (!empty($label)) ? "<label" . $for . " class=\"control-label" . (($this->horizontal == 1)?' col-sm-2 col-form-label':'') ."\">" . $labeltag . "</label>" : "";
                $html .= $this->controlToHTML($name, $label);
            } else {
                $html .= $this->controlToHTML($name, $label);
                $html .= (!empty($label)) ? "<label" . $for . " class=\"control-label"."\">" . $labeltag . "</label>" : "";
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
     * Parse the control value for storage in database
     *
     * @static
     * @param string $name
     * @param array $values
     * @param bool $for_db
     * @return string
     */
    static function parseData($name,$values, $for_db = false) {
		return (isset($values[$name])?$values[$name]:"");
	}

    /**
     * Convert a foreign value to fit the exp control
     *
     * @static
     * @param $name
     * @param $values
     * @return string
     */
    static function convertData($name,$values) {
		return (isset($values[$name])?trim($values[$name]):"");
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
