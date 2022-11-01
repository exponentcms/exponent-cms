<?php

##################################################
#
# Copyright (c) 2004-2022 OIC Group, Inc.
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

if (!defined('EXPONENT')) {
    exit('');
}

/**
 * Date/Time Control w/ Popup Date/Time Picker using Bootstrap datetimepicker
 * text entry date and/or time w/ pop-up date/time selector
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class calendarcontrol extends formcontrol
{

//    var $disable_text = "";
    var $showtime = true;
    var $default_date = '';
    var $default_hour = '';
    var $default_min = '';
    var $default_ampm = '';

    static function name()
    {
        return "Date / Time - Popup w/ Text Input";
    }

    static function isSimpleControl()
    {
        return true;
    }

    static function getFieldDefinition()
    {
        return array(
            DB_FIELD_TYPE => DB_DEF_TIMESTAMP
        );
    }

    function __construct($default = null, $showtime = true)
    {
        if (empty($default)) {
            $default = time();
        }
        $this->default = $default;
        $this->showtime = $showtime;
    }

//    function toHTML($label, $name) {
//        if (!empty($this->id)) {
//            $divID = ' id="' . $this->id . 'Control"';
//            $for   = ' for="' . $this->id . '"';
//        } else {
//            $divID = '';
//            $for   = '';
//        }
//
//        $disabled = $this->disabled != 0 ? "disabled" : "";
//        $class    = empty($this->class) ? '' : $this->class;
//
//        $html = "<div" . $divID . " class=\"" . $this->type . "-control control " . $class . $disabled . "\"";
//        $html .= (!empty($this->required)) ? ' required">' : '>';
//        //$html .= "<label>";
//        if (empty($this->flip)) {
//            $html .= $this->controlToHTML($name, $label);
//        } else {
//            $html .= "<label" . $for . " class=\"label\">" . $label . "</label>";
//        }
//        //$html .= "</label>";
//        $html .= "</div>";
//        return $html;
//    }

    function controlToHTML($name, $label = null)
    {
        if (empty($this->default_date)) {
            if (empty($this->default)) {
                $this->default = time();
            }
            if (is_string($this->default))
                $this->default = strtotime($this->default);
            // parse out date into calendarcontrol fields
            $this->default_date = date('m/d/Y', $this->default);
            $this->default_hour = date('h', $this->default);
            $this->default_min = date('i', $this->default);
            $this->default_ampm = date('a', $this->default);
        }
        $this->default = strtotime($this->default_date . ' ' . $this->default_hour . ':' . $this->default_min . ' ' . $this->default_ampm);
        $default = date('n/j/Y g:i a', $this->default);

        $idname = createValidId($name);
//        $assets_path = SCRIPT_RELATIVE . 'framework/core/forms/controls/assets/';

//        $date_input = new textcontrol($default);
//        $date_input->id = $idname;
//        $date_input->name = $idname;
//        $date_input->append = 'calendar';
//        if ($this->horizontal)
//            $date_input->horizontal_top = true;
//        $html = $date_input->toHTML(null, $name);
        $html = '';
        if ($this->horizontal)
            $html .= "<div class='col-sm-10'>";
        $html .= "<div class='input-group date input-group-sm' id='" . $idname . "'>
                        <input type='text' class='text form-control datetimepicker-input' name='" . $name . "' value='".$default."' data-target='#" . $idname . "'/>
                        <span class='input-group-append' data-target='#" . $idname . "' data-toggle='datetimepicker'>
                            <span class='input-group-text far fa-calendar-alt'></span>
                        </span>
                    </div>";
        if (!empty($this->description)) $html .= "<small class=\"form-text text-muted\">".$this->description."</small>";
        if ($this->horizontal)
            $html .= "</div>";

        $script = "
            $('#" . $idname . "').datetimepicker({
                format: '" .'L' . ($this->showtime ? ' LT' : '') ."',
                stepping: 15,
                locale: '" . LOCALE . "',
                buttons: {
                    showToday: true,
                    showClear: false,
                    showClose: false
                },
                sideBySide: true,
//                icons: {
//                    time: 'far fa-clock',
//                    date: 'far fa-calendar-alt',
//                    up: 'fas fa-chevron-up',
//                    down: 'fas fa-chevron-down',
//                    previous: 'fas fa-chevron-left',
//                    next: 'fas fa-chevron-right',
//                    today: 'fas fa-crosshairs',
//                    clear: 'fas fa-trash-alt',
//                    close: 'fas fa-times'
//                },
            });
        ";

        global $less_vars;

        if (empty($less_vars['themepath'])) {
            $less_vars = array_merge($less_vars, array(
                'swatch' => SWATCH,
                'themepath' => '../../../themes/' . DISPLAY_THEME . '/less',
                'menu_width' => MENU_WIDTH,
            ));
        }

        expJavascript::pushToFoot(
            array(
                "unique"  => 'zzcal-' . $idname,
                "jquery"    => "moment,tempusdominus-bootstrap-4",
                "bootstrap" => "collapse",
                "content" => $script,
            )
        );

        return $html;
    }

    static function parseData($name, $values, $for_db = false)
    {
        if (!empty($values['date-' . $name])) {
            $date = strtotime($values['date-' . $name]);
            $time = 0;
            if (isset($values['time-h-' . $name])) {
                if ($values['time-h-' . $name] == 12 && $values['ampm-' . $name] == 'am') {
                    // 12 am (right after midnight) is 0:xx
                    $values['time-h-' . $name] = 0;
                } else {
                    if ($values['time-h-' . $name] != 12 && $values['ampm-' . $name] == 'pm') {
                        // 1:00 pm to 11:59 pm shifts 12 hours
                        $values['time-h-' . $name] += 12;
                    }
                }

                $time += $values['time-h-' . $name] * 3600 + $values['time-m-' . $name] * 60;
            }

            return $date + $time;
        } elseif (!empty($values[$name])) {
            return strtotime($values[$name]);
        } else {
            return 0;
        }
    }

    /**
     * Display the date data in human readable format
     *
     * @param $db_data
     * @param $ctl
     *
     * @return string
     */
    static function templateFormat($db_data, $ctl)
    {
        if ($ctl->showtime) {
//            return strftime(DISPLAY_DATETIME_FORMAT,$db_data);
//            return gmstrftime(DISPLAY_DATETIME_FORMAT, $db_data);
            $datetime = date(strftime_to_date_format(DISPLAY_DATETIME_FORMAT), $db_data);
            if (!$datetime) {
                $datetime = date('m/d/y h:ma', $db_data);
            }
            return $datetime;
        } else {
//            return strftime(DISPLAY_DATE_FORMAT, $db_data);
//            return gmstrftime(DISPLAY_DATE_FORMAT, $db_data);
            $date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT), $db_data);
            if (!$date) {
                $date = date('m/d/y', $db_data);
            }
            return $date;
        }
    }

    static function form($object)
    {
        $form = new form();
        if (empty($object)) $object = new stdClass();
        if (!isset($object->identifier)) {
            $object->identifier = "";
            $object->caption = "";
            $object->description = "";
            $object->showtime = true;
        }
        if (empty($object->description)) $object->description = "";
        $form->register("identifier", gt('Identifier/Field'), new textcontrol($object->identifier),true, array('required'=>true));
        $form->register("caption", gt('Caption'), new textcontrol($object->caption));
        $form->register("description", gt('Control Description'), new textcontrol($object->description));
        $form->register("showtime", gt('Show Time'), new checkboxcontrol($object->showtime, false));
        if (!expJavascript::inAjaxAction())
            $form->register("submit", "", new buttongroupcontrol(gt('Save'), "", gt('Cancel'), "", 'editable'));
        return $form;
    }

    static function update($values, $object)
    {
        if ($object == null) {
            $object = new calendarcontrol();
            $object->default = 0;
        }
        if ($values['identifier'] == "") {
            $post = expString::sanitize($_POST);
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST", $post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
        $object->showtime = !empty($values['showtime']);
        return $object;
    }

}

?>
