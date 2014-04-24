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
 * Color Picker Control
 * pop-up color selector
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class colorcontrol extends textcontrol {

    var $type = 'color';

    static function name() { return "Color Selector"; }

    /**
     * Place the control in the form
     *
     * @param $label
     * @param $name
     * @return string
     */
    function toHTML($label,$name) {
		if (!empty($this->id)) {
		    $divID  = ' id="'.$this->id.'Control"';
		    $for = ' for="'.$this->id.'"';
		} else {
//		    $divID  = '';
            $divID  = ' id="'.$name.'Control"';
		    $for = '';
		}

		$disabled = $this->disabled != 0 ? "disabled" : "";
		$class = empty($this->class) ? '' : $this->class;

        $html = '';
//		$html = "<div".$divID." class=\"".$this->type."-control control ".$class.$disabled;
//		$html .= !empty($this->required) ? ' required">' : '">';
		//$html .= "<label>";
        if($this->required) {
            $labeltag = '<span class="required" title="'.gt('This entry is required').'">*&#160;</span>' . $label;
        } else {
            $labeltag = $label;
        }
		if(empty($this->flip)){
			$html .= (!empty($label)) ? "&#160;<label style=\"display:inline-block\">".$labeltag."</label>" : "";
			$html .= $this->controlToHTML($name, $label);
		} else {
			$html .= $this->controlToHTML($name, $label);
			$html .= (!empty($label)) ? "&#160;<label style=\"display:inline-block\">".$labeltag."</label>" : "";
		}
		//$html .= "</label>";
//		$html .= "</div>";
		return $html;
	}

    static function form($object) {
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
        }

        $form = parent::form($object);

        $form->register("default",gt('Default'), new colorcontrol($object->default));
        $form->unregister("placeholder");
        $form->unregister("pattern");
        $form->unregister("size");
        $form->unregister("maxlength");
        return $form;
    }

}

?>
