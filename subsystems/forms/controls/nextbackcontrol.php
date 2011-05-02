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

if (!defined('EXPONENT')) exit('');

/**
 * Button Group Control
 *
 * A group of buttons
 *
 * @author Adam Kessler
 * @copyright 2004-2011 OIC Group, Inc.
 * @version 0.95
 *
 * @package Subsystems
 * @subpackage Forms
 */

/**
 * Manually include the class file for formcontrol, for PHP4
 * (This does not adversely affect PHP5)
 */
require_once(BASE."subsystems/forms/controls/formcontrol.php");

/**
 * Button Group Control Class
 *
 * A group of buttons
 *
 * @package Subsystems
 * @subpackage Forms
 */
class nextbackcontrol extends formcontrol {
	var $next = "Next >";
	var $back = "< Back";
	var $cancel = "";

	var $validateJS = "";

	function name() { return "Next/Back Button Group"; }

	function parseData($name, $values, $for_db = false) {
		return;
	}

	function nextbackcontrol($next = "Next >", $back = "< Back", $cancel = "") {
		$this->next = $next;
		$this->back = $back;
		$this->cancel = $cancel;
	}

	function toHTML($label,$name) {
		if ($this->next . $this->back . $this->cancel == "") return "";
		return parent::toHTML($label,$name);
	}

	function controlToHTML($name) {
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
			$html .= ' onclick="document.location.href=\''.exponent_flow_get().'\'"';
			$html .= ' />';
		}
		return $html;
	}

}

?>
