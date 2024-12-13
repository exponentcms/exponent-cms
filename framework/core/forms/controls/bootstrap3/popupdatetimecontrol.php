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

if (!defined('EXPONENT')) {
    exit('');
}

/**
 * Popup Date/Time Picker Control using Bootstrap datetimepicker
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class popupdatetimecontrol extends formcontrol
{

    var $type     = 'datetime';
    var $disable_text = "";
    var $showtime = true;
    var $showdate = true;

    static function name()
    {
        return "Date / Time - Popup w/ Static Text";
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

    function __construct($default = null, $disable_text = "", $showtime = true)
    {
        $this->disable_text = $disable_text;
        $this->default = $default;
        $this->showtime = $showtime;

        if ($this->default == null) {
            if ($this->disable_text == "") {
                $this->default = time();
            } else {
                $this->disabled = true;
            }
        } elseif ($this->default == 0) {
            $this->default = time();
        }
    }

//    function toHTML($label,$name)
//    {
//        return $this->controlToHTML($name, $label);
//    }

    function controlToHTML($name, $label)
    {
        $idname = createValidId($name);
        if ($this->default == 0) {
            $this->default = time();
        }

//        if ($this->default == null) {
//            $myval = strftime(DISPLAY_DATE_FORMAT, time());
//        } else {
//            if (is_string($this->default))
//                $this->default = strtotime($this->default);
//            if ($this->showtime) {
//                $myval = strftime(DISPLAY_DATE_FORMAT, $this->default) . ' ' . strftime(
//                        DISPLAY_TIME_FORMAT,
//                        $this->default
//                    );
//            } else {
//                $myval = strftime(DISPLAY_DATE_FORMAT, $this->default);
//            }
//        }
        if (empty($this->default)) {
            $myval = time();
        }
        if (is_numeric($this->default)) {
            if ($this->showdate && !$this->showtime) {
                $myval = date('n/j/Y', $this->default);
            } elseif (!$this->showdate && $this->showtime) {
                $myval = date('H:i', $this->default);
            } else {
                $myval = date('n/j/Y H:i', $this->default);
            }
        } else {
            $myval = $this->default;
        }

        $date_input = new textcontrol($myval);
        $date_input->id = $idname;
        $date_input->name = $idname;
        $date_input->description = $this->description;
        $date_input->append = 'calendar';
        if ($this->horizontal)
            $date_input->horizontal_top = true;
        $html = $date_input->toHTML(null, $name);
        $html = str_replace('form-group', '', $html);  // we're a control within a control

//        $html = '';
//        if ($this->horizontal)
//            $html .= "<div class='col-sm-10'>";
//        $html .= "<div class='input-group' id='" . $idname . "'>
//                        <input type='text' class='text form-control' name='" . $name . "' value='".$myval."'/>
//                        <span class='input-group-addon'>
//                            <span class='fa fa-calendar'></span>
//                        </span>
//                    </div>";
//        if ($this->horizontal)
//            $html .= "</div>";

        $script = "
            $(document).ready(function() {
                $('#" . $idname."').datetimepicker({
                    format: '" .($this->showdate ? 'L' : '') . ($this->showdate && $this->showtime ? ' ' : '') . ($this->showtime ? 'LT' : '') ."',
                    stepping: 15,
                    locale: '" . LOCALE . "',
                    showTodayButton: true,
                    sideBySide: true,
                    icons: {
                        time: 'fa fa-clock-o',
                        date: 'fa fa-calendar',
                        up: 'fa fa-chevron-up',
                        down: 'fa fa-chevron-down',
                        previous: 'fa fa-chevron-left',
                        next: 'fa fa-chevron-right',
                        today: 'fa fa-crosshairs',
                        clear: 'fa fa-trash',
                        close: 'fa fa-times'
                    },
                  });
            });
        ";
        expJavascript::pushToFoot(
            array(
                "unique" => 'popcal' . $idname,
                "jquery"    => "moment,bootstrap-datetimepicker",
                "bootstrap" => "collapse,transitions",
                "content" => $script,
            )
        );
        return $html;
    }

    static function parseData($name, $values, $for_db = false)
    {
//        if (!isset($values[$name . '_disabled'])) {
////			return strtotime($values[$name]);
//            return $values[$name];
//        } else {
//            return 0;
//        }
        if (!empty($values[$name])) {
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
        if (empty($db_data))
            return gt('No Date Set');
        if ($ctl->showtime) {
//			return strftime(DISPLAY_DATETIME_FORMAT,$db_data);
            $datetime = date(strftime_to_date_format(DISPLAY_DATETIME_FORMAT), $db_data);
            if (!$datetime) {
                $datetime = date('m/d/y h:ma', $db_data);
            }
            return $datetime;
        } else {
//			return strftime(DISPLAY_DATE_FORMAT, $db_data);
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
        if (!isset($object->identifier)) {
            $object = new stdClass();
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
            $object = new popupdatetimecontrol();
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