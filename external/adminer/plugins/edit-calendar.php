<?php

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
	function AdminerEditCalendar($prepend = null, $langPath = null) {
        $prepend = "<script type='text/javascript' src='".JQUERY_SCRIPT."'></script>\n<script type='text/javascript' src='".JQUERYUI_SCRIPT."'></script>\n<script type='text/javascript' src='".JQUERY_RELATIVE."addons/js/jquery-ui-timepicker-addon.js'></script>\n<link rel='stylesheet' type='text/css' href='".JQUERYUI_CSS."'>\n<link rel='stylesheet' type='text/css' href='".JQUERY_RELATIVE."addons/css/jquery-ui-timepicker-addon.css'>\n";
        $langPath = JQUERY_RELATIVE."development-bundle/ui/i18n/jquery.ui.datepicker-%s.js";
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

	function editInput($table, $field, $attrs, $value) {
//		if (ereg("date|time", $field["type"])) {
		if (ereg("date|_at|publish|_accessed|posted|timestamp", $field["field"])) {
			$dateFormat = "changeYear: true,changeMonth: true,defaultDate: null,dateFormat: '@', showOn: 'both', buttonImage: '".PATH_RELATIVE."framework/core/forms/controls/assets/calendar/calbtn.gif', buttonImageOnly: true,beforeShow: function(input,inst){jQuery('#fields-" . js_escape($field['field']) . "c').val(parseInt(jQuery('#fields-" . js_escape($field['field']) . "').val()) * 1000);},onClose: function() {jQuery('#fields-" . js_escape($field['field']) . "').val(parseInt(jQuery('#fields-" . js_escape($field['field']) . "c').val()) / 1000);}";
			$timeFormat = "showSecond: true,timeFormat: 'hh:mm:ss',showOn: 'both',buttonImage: '".PATH_RELATIVE."framework/core/forms/controls/assets/calendar/calbtn.gif', buttonImageOnly: true,beforeShow: function(input,inst){jQuery('#fields-" . js_escape($field['field']) . "c').val(parseInt(jQuery('#fields-" . js_escape($field['field']) . "').val()));},onClose: function() {jQuery('#fields-" . js_escape($field['field']) . "').val(parseInt(jQuery('#fields-" . js_escape($field['field']) . "c').val()) );}";
			return "<input id='fields-" . h($field["field"]) . "' value='" . h($value) . "'" . (+$field["length"] ? " maxlength='" . (+$field["length"]) . "'" : "") . $attrs. ">".
                "<input type=hidden id='fields-" . h($field["field"]) . "c' value='" . h($value) . "'" . (+$field["length"] ? " maxlength='" . (+$field["length"]) . "'" : "") . $attrs. ">".
                "<script type='text/javascript'>jQuery('#fields-" . js_escape($field["field"]) . "c')."
                    . ((ereg("eventstart", $field["field"]) || ereg("eventend", $field["field"])) ? "timepicker({ $timeFormat })"
                    : (ereg("time", $field["type"]) ? "datetimepicker({ $dateFormat, $timeFormat })"
                    : "datepicker({ $dateFormat })"
                )) . ";</script>";
		}
	}
	
}
