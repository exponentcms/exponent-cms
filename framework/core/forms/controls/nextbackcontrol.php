<?php
//FIXME Deprecated! Not used
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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Next Back Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class nextbackcontrol extends formcontrol {

	var $next = "Next >";
	var $back = "< Back";
	var $cancel = "";
	var $validateJS = "";

	static function name() { return "Next/Back Button Group"; }

	function __construct($next = "Next >", $back = "< Back", $cancel = "") {
		$this->next = $next;
		$this->back = $back;
		$this->cancel = $cancel;
	}

	function toHTML($label,$name) {
		if ($this->next . $this->back . $this->cancel == "") return "";
		return parent::toHTML($label,$name);
	}

	function controlToHTML($name,$label) {
		if ($this->next . $this->back . $this->cancel == "") return "";
		$html = "";
		if ($this->back != "") {
			$html .= '<input type="submit" name="nextback" value="' . $this->back . '"';
			//if ($this->disabled) $html .= " disabled";
			$html .= ' onclick="if (checkRequired(this.form)) ';
			if ($this->validateJS != "") {
				$html .= '{ if (' . $this->validateJS . ') { return true; } else { return false; } }';
			} else {
				$html .= '{ return true; }';
			}
			$html .= ' else { return false; }"';
			$html .= ' />';

		}
		if ($this->next != "") {
			$html .= '<input type="submit" name="nextback" value="' . $this->next . '"';
			if ($this->disabled) $html .= " disabled";
			$html .= ' onclick="if (checkRequired(this.form)) ';
			if ($this->validateJS != "") {
				$html .= '{ if (' . $this->validateJS . ') { return true; } else { return false; } }';
			} else {
				$html .= '{ return true; }';
			}
			$html .= ' else { return false; }"';
			$html .= ' />';

		}
		if ($this->cancel != "") {
			$html .= '<input type="button" value="' . $this->cancel . '"';
			$html .= ' onclick="document.location.href=\''.expHistory::getLastNotEditable().'\'"';
			$html .= ' />';
		}
		return $html;
	}

	static function parseData($name, $values, $for_db = false) {
		return;
	}

}

?>
