<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * Date Time Control
 * simple text entry date and/or time
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class datetimecontrol extends formcontrol {

    var $showdate = true;
    var $showtime = true;

    static function name() {
        return "Date / Time - Simple";
    }

    static function isSimpleControl() {
        return true;
    }

    static function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=> DB_DEF_TIMESTAMP);
    }

    function __construct($default = 0, $showdate = true, $showtime = true) {
        if ($default == 0) $default = time();
        $this->default  = $default;
        $this->showdate = $showdate;
        $this->showtime = $showtime;
    }

    function toHTML($label, $name) {
        if (!$this->showdate && !$this->showtime) return "";
        $html = "<div id=\"" . $name . "Control\" class=\"control";
        $html .= (!empty($this->required)) ? ' required">' : '">';
        //$html .= "<label>";
        if (empty($this->flip)) {
            $html .= "<span class=\"label\">" . $label . "</span>";
            $html .= $this->controlToHTML($name);
        } else {
            $html .= $this->controlToHTML($name);
            $html .= "<span class=\"label\">" . $label . "</span>";
        }
        //$html .= "</label>";
        $html .= "</div>";
        $html = '<script type="text/javascript" src="' . PATH_RELATIVE . 'framework/core/forms/controls/datetimecontrol.js"></script>' . $html;
        return $html;
    }

    function controlToHTML($name, $label = null) {
        if (!$this->showdate && !$this->showtime) return "";
        if ($this->default == 0) $this->default = time();
        $default_date = getdate($this->default);
        $hour         = $default_date['hours'];
        if ($hour > 12) $hour -= 12;
        if ($hour == 0) $hour = 12;

        $minute = $default_date['minutes'] . "";
        if ($minute < 10) $minute = "0" . $minute;
        $html = "<input type='hidden' id='__" . $name . "' name='__" . $name . "' value='" . ($this->showdate ? "1" : "0") . ($this->showtime ? "1" : "0") . "' />";
        if ($this->showdate) {
            $html .= '<div class="datetime date"><label style="display:inline;float:none;">' . gt('Date') . ': </label>';
            $html .= expDateTime::monthsDropdown($name . "_month", $default_date['mon']);
            $html .= '<input class="text" type="text" id="' . $name . '_day" name="' . $name . '_day" size="3" maxlength="2" value="' . $default_date['mday'] . '"';
            if (!empty($this->readonly) || !empty($this->disabled)) $html .= ' disabled="disabled"';
            $html .= ' />';
            $html .= '<input class="text" type="text" id="' . $name . '_year" name="' . $name . '_year" size="5" maxlength="4" value="' . $default_date['year'] . '"';
            if (!empty($this->readonly) || !empty($this->disabled)) $html .= ' disabled="disabled"';
            $html .= ' />';
            $html .= '</div>';
        }
        if ($this->showtime) {
            $framework = expSession::get('framework');
            if ($framework != 'bootstrap') {
                $html .= '<br /><label class="label spacer"> </label>';
            }
            $html .= '<div class="datetime date time"><label style="display:inline;float:none;">' . gt('Time') . ': </label>';
            $html .= '<input class="text" type="text" id="' . $name . '_hour" name="' . $name . '_hour" size="3" maxlength="2" value="' . $hour . '"';
            if (!empty($this->readonly) || !empty($this->disabled)) $html .= ' disabled="disabled"';
            $html .= ' />';
            $html .= '<input class="text" type="text" id="' . $name . '_minute" name="' . $name . '_minute" size="3" maxlength="2" value="' . $minute . '"';
            if (!empty($this->readonly) || !empty($this->disabled)) $html .= ' disabled="disabled"';
            $html .= ' />';
            $html .= '<select class="select" id="' . $name . '_ampm" name="' . $name . '_ampm" size="1"';
            if (!empty($this->readonly) || !empty($this->disabled)) $html .= ' disabled="disabled"';
            $html .= '>';
            $html .= '<option value="am"' . ($default_date['hours'] < 12 ? " selected" : "") . '>am</option>';
            $html .= '<option value="pm"' . ($default_date['hours'] < 12 ? "" : " selected") . '>pm</option>';
            $html .= '</select></div>';
        }
        if (!empty($this->description)) $html .= "<div class=\"control-desc\">" . $this->description . "</div>";
        return $html;
    }

    function onRegister(&$form) {
        $form->addScript('datetime_disable', PATH_RELATIVE . 'framework/core/forms/controls/datetimecontrol.js');
    }

    static function parseData($original_name, $formvalues, $for_db = false) {
        $time = 0;
        if (isset($formvalues[$original_name . "_month"])) $time = mktime(8, 0, 0, $formvalues[$original_name . '_month'], $formvalues[$original_name . '_day'], $formvalues[$original_name . '_year']) - 8 * 3600;
        if (isset($formvalues[$original_name . "_hour"])) {
            if ($formvalues[$original_name . '_hour'] == 12 && $formvalues[$original_name . '_ampm'] == 'am') {
                // 12 am (right after midnight) is 0:xx
                $formvalues[$original_name . '_hour'] = 0;
            } else if ($formvalues[$original_name . '_hour'] != 12 && $formvalues[$original_name . '_ampm'] == 'pm') {
                // 1:00 pm to 11:59 pm shifts 12 hours
                $formvalues[$original_name . '_hour'] += 12;
            }

            $time += $formvalues[$original_name . '_hour'] * 3600 + $formvalues[$original_name . '_minute'] * 60;
        }

        return $time;
    }

    static function convertData($original_name,$formvalues) {
		return (isset($formvalues[$original_name])?strtotime($formvalues[$original_name]):"");
	}

    /**
     * Display the date data in human readable format
     *
     * @param $db_data
     * @param $ctl
     *
     * @return string
     */
    static function templateFormat($db_data, $ctl) {
        if ($ctl->showdate && $ctl->showtime) {
//            return gmstrftime(DISPLAY_DATETIME_FORMAT, $db_data);
            $datetime = strftime(DISPLAY_DATETIME_FORMAT, $db_data);
            if (!$datetime) $datetime = strftime('%m/%d/%y %I:%M%p', $db_data);
            return $datetime;
        } elseif ($ctl->showdate) {
//            return gmstrftime(DISPLAY_DATE_FORMAT, $db_data);
            $date = strftime(DISPLAY_DATE_FORMAT, $db_data);
            if (!$date) $date = strftime('%m/%d/%y', $db_data);
            return $date;
        } elseif ($ctl->showtime) {
//            return gmstrftime(DISPLAY_TIME_FORMAT, $db_data);
            $time = strftime(DISPLAY_TIME_FORMAT, $db_data);
            if (!$time) $time = strftime('%I:%M%p', $db_data);
            return $time;
        } else {
            return "";
        }
    }

    static function form($object) {
        $form = new form();
        if (empty($object)) $object = new stdClass();
        if (!isset($object->identifier)) {
            $object->identifier  = "";
            $object->caption     = "";
            $object->description = "";
            $object->showdate    = true;
            $object->showtime    = true;
        }
        if (empty($object->description)) $object->description = "";
        $form->register("identifier", gt('Identifier/Field'), new textcontrol($object->identifier));
        $form->register("caption", gt('Caption'), new textcontrol($object->caption));
        $form->register("description", gt('Control Description'), new textcontrol($object->description));
        $form->register("showdate", gt('Show Date'), new checkboxcontrol($object->showdate, false));
        $form->register("showtime", gt('Show Time'), new checkboxcontrol($object->showtime, false));

        $form->register("submit", "", new buttongroupcontrol(gt('Save'), "", gt('Cancel'), "", 'editable'));
        return $form;
    }

    static function update($values, $object) {
        if ($object == null) {
            $object          = new datetimecontrol();
            $object->default = 0; //This will force the control to always show the current time as default
        }
        if ($values['identifier'] == "") {
            $post               = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST", $post);
            return null;
        }
        $object->identifier  = $values['identifier'];
        $object->caption     = $values['caption'];
        $object->description = $values['description'];
        $object->showdate    = isset($values['showdate']);
        $object->showtime    = isset($values['showtime']);
        return $object;
    }
}

?>
