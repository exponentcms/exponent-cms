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

/** Display jQuery UI Timepicker for each date and datetime field
* @link http://www.adminer.org/plugins/#use
* @uses jQuery-Timepicker, http://trentrichardson.com/examples/timepicker/
* @uses jQuery UI: core, widget, mouse, slider, datepicker
* @author Jakub Vrana, http://www.vrana.cz/
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerEditCalendar {
	/** @access protected */
	var $prepend, $langPath;

	/**
	* @param string text to append before first calendar usage
	* @param string path to language file, %s stands for language code
	*/
    function __construct($prepend = "<script type='text/javascript' src='jquery-ui/jquery.js'></script>\n<script type='text/javascript' src='jquery-ui/jquery-ui.js'></script>\n<script type='text/javascript' src='jquery-ui/jquery-ui-timepicker-addon.js'></script>\n<link rel='stylesheet' type='text/css' href='jquery-ui/jquery-ui.css'>\n", $langPath = "jquery-ui/i18n/jquery.ui.datepicker-%s.js") {
		$this->prepend = $prepend;
		$this->langPath = $langPath;
	}

	function head() {
		echo $this->prepend;
		if ($this->langPath && function_exists('get_lang')) { // since Adminer 3.2.0
			$lang = get_lang();
			$lang = ($lang == "zh" ? "zh-CN" : ($lang == "zh-tw" ? "zh-TW" : $lang));
			if ($lang != "en" && file_exists(sprintf($this->langPath, $lang))) {
				printf("<script type='text/javascript' src='$this->langPath'></script>\n", $lang);
				echo "<script type='text/javascript'>jQuery(function () { jQuery.timepicker.setDefaults(jQuery.datepicker.regional['$lang']); });</script>\n";
			}
		}
	}

	function selectVal(&$val, $link, $field) {
		if (preg_match("~date|time|_at|publish|_accessed|posted|created_on|last_|expires|shipped|purchased|updated|signup_cutoff|event~", $field["field"])) {
			$val = '<div title="'.htmlentities(html_entity_decode(strftime('%m/%d/%y %I:%M%p',$val)),true).'">'.$val.'</div>';
		}
	}

	function editInput($table, $field, $attrs, $value) {
		if (preg_match("~date|time|_at|publish|_accessed|posted|created_on|last_|expires|shipped|purchased|updated|signup_cutoff|event~", $field["field"])) {
			$dateFormat = "changeYear: true,changeMonth: true,defaultDate: null,dateFormat: '@',showOtherMonths: true,selectOtherMonths: true,showOn: 'both',buttonImage: '".PATH_RELATIVE."framework/core/forms/controls/assets/calendar/calbtn.gif',buttonImageOnly: true,
			    beforeShow: function(input,inst){
			        jQuery('#fields-" . js_escape($field['field']) . "c').val(parseInt(jQuery('#fields-" . js_escape($field['field']) . "').val()) * 1000);
                },
                onClose: function(){
                    jQuery('#fields-" . js_escape($field['field']) . "').val(parseInt(jQuery('#fields-" . js_escape($field['field']) . "c').val()) / 1000);
                }";
			$timeFormat = "timeFormat: 'HH:mm:ss',showOn: 'both',buttonImage: '".PATH_RELATIVE."framework/core/forms/controls/assets/calendar/calbtn.gif',buttonImageOnly: true,
			    beforeShow: function(input,inst){
			        var d = new Date(jQuery('#fields-" . js_escape($field['field']) . "').val() * 1000);
			        jQuery('#fields-" . js_escape($field['field']) . "c').val(jQuery.datepicker.formatTime('HH:mm:ss',{hour:d.getHours(),minute:d.getMinutes(),second:d.getSeconds()},{}));
                },
			    onClose: function(){
			        var d = new Date('01 January, 1970 ' + jQuery('#fields-" . js_escape($field['field']) . "c').val());
			        jQuery('#fields-" . js_escape($field['field']) . "').val(d.getTime() / 1000);
			    }";
            $datetimeFormat = "changeYear: true,changeMonth: true,defaultDate: null,dateFormat: '@',timeFormat: 'HH:mm:ss',separator: ' @ ',showOtherMonths: true,selectOtherMonths: true,showOn: 'both',buttonImage: '".PATH_RELATIVE."framework/core/forms/controls/assets/calendar/calbtn.gif',buttonImageOnly: true,
                beforeShow: function(input,inst){
                    var d = new Date(jQuery('#fields-" . js_escape($field['field']) . "').val() * 1000);
                    jQuery('#fields-" . js_escape($field['field']) . "c').val((parseInt(jQuery('#fields-" . js_escape($field['field']) . "').val()) * 1000) + ' @ ' + jQuery.datepicker.formatTime('HH:mm:ss',{hour:d.getHours(),minute:d.getMinutes(),second:d.getSeconds()},{}));
                },
                onClose: function(){
                    var d = parseInt(jQuery('#fields-" . js_escape($field['field']) . "c').val()) / 1000;
                    var tm = jQuery('#fields-" . js_escape($field['field']) . "c').val().split(' @ ');
                    var t = new Date('01 January, 1970 ' + tm[1] + ' +0000');
                    var dt = d + (t.getTime() / 1000);
                    jQuery('#fields-" . js_escape($field['field']) . "').val(dt);
                }";
			return "<input id='fields-" . h($field["field"]) . "' value='" . h($value) . "'" . (+$field["length"] ? " maxlength='" . (+$field["length"]) . "'" : "") . $attrs. ">".
                "<input type=hidden id='fields-" . h($field["field"]) . "c' value='" . h($value) . "'" . (+$field["length"] ? " maxlength='" . (+$field["length"]) . "'" : "") . $attrs. ">".
                "<script type='text/javascript'>jQuery('#fields-" . js_escape($field["field"]) . "c')."
                    . ((preg_match("~eventstart|eventend~", $field["field"])) ? "timepicker({ $timeFormat })" : "datetimepicker({ $datetimeFormat })"
//                    : (preg_match("~_at|publish|_accessed|posted|time~", $field["field"]) ? "datetimepicker({ $datetimeFormat })"
//				: "datepicker({ $dateFormat })"
//			)) . ";</script>";
			) . ";</script>";
		}
	}

}
