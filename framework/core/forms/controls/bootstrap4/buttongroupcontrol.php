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
 * Button Group Control
 *
 * A group of buttons
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class buttongroupcontrol extends formcontrol {

    var $type   = 'button';
	var $submit = "Submit";
	var $reset = "";
	var $cancel = "";
	var $returntype = "";
	var $validateJS = "";
    var $onclick = null;
    var $wide = false;
    var $size = BTN_SIZE;
    var $color = 'blue';
    var $cancel_color = 'white';

	static function name() { return "Button Group"; }

	function __construct($submit = "Submit", $reset = "", $cancel = "", $class="", $returntype="") {
		$this->submit = $submit;
		$this->reset = $reset;
		$this->cancel = $cancel;
		$this->class = $class;
		$this->returntype = $returntype;
	}

	function toHTML($label,$name) {
	    $disabled = $this->disabled != false ? " disabled" : "";
		if ($this->submit . $this->reset . $this->cancel == "") return "";
		$html = "<div id=\"".$name."Control\" class=\"buttongroup control form-group col-sm-12".$disabled."\">";
		$html .= ($this->horizontal) ? '<div class="offset-sm-2 col-sm-10">' : '';
		$html .= $this->controlToHTML($name);
		$html .= ($this->horizontal) ? '</div>' : '';
		$html .= "</div>";
		return $html;
	}

	function controlToHTML($name,$label=null) {
		if ($this->submit . $this->reset . $this->cancel == "") return "";
		if (empty($this->id)) $this->id = $name;
        $html = '';
		if ($this->submit != "") {
            $btn_size = expTheme::buttonSize($this->size);
            if ($this->wide) {
                $btn_size .= ' btn-block';
            }
            $btn_color = expTheme::buttonColor($this->color);
            $icon_size = expTheme::iconSize($this->size);
            if (stripos($this->submit, 'save') !== false) {
                $icon = 'far fa-save';
            } elseif (stripos($this->submit, 'log') !== false) {
                $icon = 'fas fa-sign-in-alt';
            } else {
                $icon = 'far fa-check-circle';
            }
			$html .= '<button type="submit" id="'.$this->id.'Submit" class="submit btn '.$btn_color.' '.$btn_size.' '.$this->class;
			if ($this->disabled) $html .= " disabled";  // disabled class
			$html .='" value="' . $this->submit . '"';
			if ($this->disabled) $html .= " disabled";  // disabled attribute
//			$html .= ' onclick="if (checkRequired(this.form)';
//			if (isset($this->onclick)) $html .= ' '.$this->onclick;
            if (!empty($this->onclick)) $html .= ' onclick="' . $this->onclick . '"';
//			$html .= ') ';
//			if ($this->validateJS != "") {
//				$html .= '{ if (' . $this->validateJS . ') { return true; } else { return false; } }';
//			} else {
//				$html .= '{ return true; }';
//			}
//			$html .= ' else { return false; }"';
			$html .= ' ><i class="'.$icon.' '.$icon_size.'"></i> ';
			$html .= $this->submit;
			$html .= ' </button>';
		}
		//if ($this->reset != "") $html .= '<input class="button" type="reset" value="' . $this->reset . '"' . ($this->disabled?" disabled":"") . ' />';
		if ($this->cancel != "") {
            $btn_color = expTheme::buttonColor($this->cancel_color);
			if ($this->returntype == "") {
				$html .= '<button type="cancel" class="cancel btn '.$btn_color.' '.$btn_size.'" onclick="document.location.href=\''.expHistory::getLastNotEditable().'\'; return false;"';
			} else {
			    $html .= '<button type="cancel" class="cancel btn '.$btn_color.' '.$btn_size.'" onclick="document.location.href=\''.expHistory::getLast($this->returntype).'\'; return false;"';
			}
            $html .= ' ><i class="fas fa-ban '.$icon_size.'"></i> ';
			$html .= $this->cancel;
			$html .= '</button>';
		}

//		expCSS::pushToHead(array(
////		    "unique"=>"button",
//		    "corecss"=>"button",
//		    )
//		);

		return $html;
	}

	static function parseData($name, $values, $for_db = false) {
		return;
	}

}

?>
