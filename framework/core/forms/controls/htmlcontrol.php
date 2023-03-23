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

/** @define "BASE" "../../../../.." */
/**
 * HTML Control - displays static wysiwyg text
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class htmlcontrol extends formcontrol {

	var $html;
	var $span = false;

	static function name() { return "Static - WYSIWYG Text"; }
    static function isStatic() { return true; }
	static function isSimpleControl() { return true; }

	function __construct($html = "",$span = true) {
		$this->span = $span;
		$this->html = $html;
	}

	function toHTML($label,$name) {
//		if ($this->span) {
//			return '<div class="htmlcontrol control form-group">' . ($this->horizontal && bs() ? '<div class="' . $this->label_class() . '">' : '') . $this->html . ($this->horizontal && bs() ? '</div>':'') . '</div>';
//		} else {
            if ($this->horizontal && (bs3() || bs4() || bs5()))
                $this->html = '<div class="' . $this->label_class() . '">' . $this->html . '</div>';
			return parent::toHTML($label, $name);
//		}
	}

	function controlToHTML($name,$label) {
        if ($this->horizontal && (bs3() || bs4() || bs5()))
            return '<div class="' . $this->label_class() . '">' . $this->html . '</div>';
		return $this->html;
	}

    function label_class() {
        $label_class = "";
        if (bs2()){
//            if ($this->horizontal) {
//                $label_class .= "span10 offset2";
//            }
        } elseif (bs3()) {
            if ($this->horizontal) {
                if ($this->span) {
                    $label_class = "col-sm-12";
                } else {
                    $label_class = "col-sm-offset-2 col-sm-10";
                }
            }
        } elseif (bs4() || bs5()) {
            if ($this->horizontal) {
                if ($this->span) {
                    $label_class = "col-sm-12";
                } else {
                    $label_class = "offset-sm-2 col-sm-10";
                }
            }
        } else {
            $label_class = "label";
        }
        return $label_class;
    }

	static function form($object) {
		$form = new form();
        if (empty($object)) $object = new stdClass();
		if (!isset($object->html)) {
			$object->html = "";
		}
        $form->register("html", '', new htmleditorcontrol($object->html));
        $form->register("span",gt('Full Width when Labels on Side?'), new checkboxcontrol($object->span,false));
		if (!expJavascript::inAjaxAction())
            $form->register("submit", "", new buttongroupcontrol(gt('Save'), '', gt('Cancel'), "", 'editable'));
		return $form;
	}

    static function update($values, $object) {
		if ($object == null) $object = new htmlcontrol();
        $object->html = preg_replace("/<br ?\/>$/", "", trim($values['html']));
        $object->span = !empty($values['span']);
		$object->caption = '';
		$object->identifier = uniqid("");
		$object->is_static = 1;
		return $object;
	}

}

?>
