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
 * Date Time Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class datetimecontrol extends formcontrol {

	var $showdate = true;
	var $showtime = true;
	
	function name() { return "Date / Time Field"; }
	function isSimpleControl() { return true; }
	function getFieldDefinition() {
		return array(
			DB_FIELD_TYPE=>DB_DEF_TIMESTAMP);
	}
	
	function __construct($default = 0, $showdate = true, $showtime = true) {
//		if (!defined("SYS_DATETIME")) include_once(BASE."framework/core/subsystems-1/datetime.php");
		include_once(BASE."framework/core/subsystems-1/datetime.php");
		if ($default == 0) $default = time();
		$this->default = $default;
		$this->showdate = $showdate;
		$this->showtime = $showtime;
	}

	function toHTML($label,$name) {
		if (!$this->showdate && !$this->showtime) return "";
		$html = "<div id=\"".$name."Control\" class=\"control";
		$html .= (!empty($this->required)) ? ' required">' : '">';
		//$html .= "<label>";
		if(empty($this->flip)){
			$html .= "<span class=\"label\">".$label."</span>";
			$html .= $this->controlToHTML($name);
		} else {
			$html .= $this->controlToHTML($name);
			$html .= "<span class=\"label\">".$label."</span>";
		}
		//$html .= "</label>";
		$html .= "</div>";			
		return $html;
	}
	
	function controlToHTML($name) {
		if (!$this->showdate && !$this->showtime) return "";
		if ($this->default == 0) $this->default = time();
		$default_date = getdate($this->default);
		$hour = $default_date['hours'];
		if ($hour > 12) $hour -= 12;
		if ($hour == 0) $hour = 12;
		
		$minute = $default_date['minutes']."";
		if ($minute < 10) $minute = "0".$minute;
		$html = "<input type='hidden' id='__".$name."' name='__".$name."' value='".($this->showdate?"1":"0").($this->showtime?"1":"0")."' />";
		if ($this->showdate) {
//			if (!defined("SYS_DATETIME")) require_once(BASE."framework/core/subsystems-1/datetime.php");
			require_once(BASE."framework/core/subsystems-1/datetime.php");
			$html .= '<div class="datetime date"><label>Date: </label>';
			$html .= exponent_datetime_monthsDropdown($name . "_month",$default_date['mon']);
			$html .= '<input class="text" type="text" id="' . $name . '_day" name="' . $name . '_day" size="3" maxlength="2" value="' . $default_date['mday'] . '" />';
			$html .= '<input class="text" id="' . $name . '_year" name="' . $name . '_year" size="5" maxlength="4" value="' . $default_date['year'] . '" />';
			$html .= '</div>';
		}
		if ($this->showtime) {
		    $html .= '<div class="datetime time"><label>Time: </label>';
			$html .= '<input class="text" type="text" id="' . $name . '_hour" name="' . $name . '_hour" size="3" maxlength="2" value="' . $hour . '" />';
			$html .= '<input class="text" type="text" id="' . $name . '_minute" name="' . $name . '_minute" size="3" maxlength="2" value="' . $minute . '" />';
			$html .= '<select class="select" id="' . $name . '_ampm" name="' . $name . '_ampm" size="1">';
			$html .= '<option value="am"' . ($default_date['hours'] < 12 ? " selected":"") . '>am</option>';
			$html .= '<option value="pm"' . ($default_date['hours'] < 12 ? "":" selected") . '>pm</option>';
			$html .= '</select></div>';
		}
		return $html;
	}
	
	function onRegister(&$form) {
		$form->addScript('datetime_disable',PATH_RELATIVE.'subsystems/forms/controls/datetimecontrol.js');
	}
	
	static function parseData($original_name,$formvalues,$for_db = false) {
		$time = 0;
		if (isset($formvalues[$original_name."_month"])) $time = mktime(8,0,0,$formvalues[$original_name.'_month'],$formvalues[$original_name.'_day'],$formvalues[$original_name.'_year']) - 8*3600;
		if (isset($formvalues[$original_name."_hour"])) {
			if ($formvalues[$original_name.'_hour'] == 12 && $formvalues[$original_name.'_ampm'] == 'am') {
				// 12 am (right after midnight) is 0:xx
				$formvalues[$original_name.'_hour'] = 0;
			} else if ($formvalues[$original_name.'_hour'] != 12 && $formvalues[$original_name.'_ampm'] == 'pm') {
				// 1:00 pm to 11:59 pm shifts 12 hours
				$formvalues[$original_name.'_hour'] += 12;
			}
			
			$time += $formvalues[$original_name.'_hour']*3600 + $formvalues[$original_name.'_minute']*60;
		}
		
		return $time;
	}
	
	function templateFormat($db_data, $ctl) {
		if ($ctl->showdate && $ctl->showtime) {
			return gmstrftime(DISPLAY_DATETIME_FORMAT,$db_data);
		} 
		elseif ($ctl->showdate) {
			return gmstrftime(DISPLAY_DATE_FORMAT, $db_data);
		}
		elseif ($ctl->showtime) {
			return gmstrftime(DISPLAY_TIME_FORMAT, $db_data);
		}
		else {
			return "";
		}
	}
	
	function form($object) {
//		if (!defined("SYS_FORMS")) require_once(BASE."framework/core/subsystems-1/forms.php");
		require_once(BASE."framework/core/subsystems-1/forms.php");
//		exponent_forms_initialize();
	
		$form = new form();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
			$object->showdate = true;
			$object->showtime = true;
		} 
		
		$i18n = exponent_lang_loadFile('subsystems/forms/controls/datetimecontrol.php');
		
		$form->register("identifier",$i18n['identifier'],new textcontrol($object->identifier));
		$form->register("caption",$i18n['caption'], new textcontrol($object->caption));
		$form->register("showdate",$i18n['showdate'], new checkboxcontrol($object->showdate,false));
		$form->register("showtime",$i18n['showtime'], new checkboxcontrol($object->showtime,false));
		
		$form->register("submit","",new buttongroupcontrol($i18n['save'],"",$i18n['cancel']));
		return $form;
	}
	
	function update($values, $object) {
		if ($object == null) { 
			$object = new datetimecontrol();
			$object->default = 0; //This will force the control to always show the current time as default
		}
		if ($values['identifier'] == "") {
			$i18n = exponent_lang_loadFile('subsystems/forms/controls/datetimecontrol.php');
			
			$post = $_POST;
			$post['_formError'] = $i18n['id_req'];
			exponent_sessions_set("last_POST",$post);
			return null;
		}
		$object->identifier = $values['identifier'];
		$object->caption = $values['caption'];
		$object->showdate = isset($values['showdate']);
		$object->showtime = isset($values['showtime']);
		return $object;
	}
}

?>
