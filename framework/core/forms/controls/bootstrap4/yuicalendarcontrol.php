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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) {
    exit('');
}

/**
 * Date Picker Control using Bootstrap datetimepicker
 * standard calendar control
 * places an update calendar field/button
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class yuicalendarcontrol extends formcontrol {

//    var $disable_text = "";
    var $type     = 'datetime';
    var $showdate = true;
    var $showtime = false;

    static function name() {
        return "Date / Time - Calendar Display";
    }

    static function isSimpleControl() {
        return true;
    }

    static function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE => DB_DEF_TIMESTAMP
        );
    }

//    function __construct($default = null, $disable_text = "", $showtime = true) {  //FIXME $disable_text & $showtime are NOT used
    function __construct($default = null, $showdate = true, $showtime = false) {
//        $this->disable_text = $disable_text;
        if (empty($default)) {
            $default = time();
        }
        $this->default      = $default;
        $this->showdate     = $showdate;
        $this->showtime     = $showtime;

//        if ($this->default == null) {
//            if ($this->disable_text == "") $this->default = time();
//            else $this->disabled = true;
//        } elseif ($this->default == 0) {
//            $this->default = time();
//        }
    }

//    function onRegister(&$form)
//    {
//    }

    function toHTML($label, $name) {
        if (!$this->showdate && !$this->showtime) {
            return "";
        }
        $html = parent::toHTML($label, $name);
        return $html;
    }

    function controlToHTML($name, $label = null) {
        $idname = createValidId($name);
        if (empty($this->default)) {
            $this->default = time();
        }
        if (is_numeric($this->default)) {
            if ($this->showdate && !$this->showtime) {
                $default = date('n/j/Y', $this->default);
            } elseif (!$this->showdate && $this->showtime) {
                $default = date('H:i', $this->default);
            } else {
                $default = date('n/j/Y H:i', $this->default);
            }
        } else {
            $default = $this->default;
        }

        $date_input = new hiddenfieldcontrol($default);
        $control_offset = $label_offset = "";
        if ($this->horizontal) {
//            $date_input->horizontal_top = true;
            $control_offset = " col-sm-10 ";
            $label_offset = "offset-sm-2 col-sm-10 ";
        }
//        $date_input->id = $idname;
//        $date_input->name = $idname;
//        $date_input->disabled = 'disabled';
//        $html = "<!-- cke lazy -->";
        $html = '<div class="input-group date input-append' . $control_offset . '" id="'.$idname.'dateRangePicker" data-target-input="nearest">'.$date_input->toHTML(null, $name).'</div>';
        if (!empty($this->description)) $html .= "<small class=\"" . $label_offset . "form-text text-muted\">".$this->description."</small>";
//        $html .= "
//        <div style=\"clear:both\"></div>
//        ";

        $script = "
            $(document).ready(function() {
                $('#" . $idname . "dateRangePicker').datetimepicker({
                    format: '" .($this->showdate ? 'L' : '') . ($this->showdate && $this->showtime ? ' ' : '') . ($this->showtime ? 'LT' : '') ."',
                    stepping: 15,
                    locale: '" . LOCALE . "',
                    buttons: {
                        showToday: ".(!$this->showdate && $this->showtime ? 'false' : 'true').",
//                        showClear: false,
//                        showClose: false
                    },
                    inline: true,
                    sideBySide: true,
//                    icons: {
//                        time: 'far fa-clock',
//                        date: 'far fa-calendar-alt',
//                        up: 'fas fa-chevron-up',
//                        down: 'fas fa-chevron-down',
//                        previous: 'fas fa-chevron-left',
//                        next: 'fas fa-chevron-right',
//                        today: 'fas fa-crosshairs',
//                        clear: 'fas fa-trash-alt',
//                        close: 'fas fa-times'
//                    },
                });
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
                "unique"    => '00yuical-' . $idname,
                "jquery"    => "moment,tempusdominus-bootstrap-4",
                "bootstrap" => "collapse",
                "content"   => $script,
            )
        );
        return $html;
    }

    static function parseData($name, $values, $for_db = false) {
        if (!empty($values[$name]) && is_string($values[$name])) {
            return strtotime($values[$name]);
        } elseif (is_int($values[$name])) {
            return $values[$name];
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
    static function templateFormat($db_data, $ctl) {
        if ($ctl->showdate && !$ctl->showtime) {
            $date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT), $db_data);
        } elseif (!$ctl->showdate && $ctl->showtime) {
            $date = date(strftime_to_date_format(DISPLAY_TIME_FORMAT), $db_data);
        } else {
            $date = date(strftime_to_date_format(DISPLAY_DATETIME_FORMAT), $db_data);
        }

        if (!$date) {
            $date = date('m/d/y', $db_data);
        }
        return $date;
    }

    static function form($object) {
        $form = new form();
        if (empty($object))
            $object = new stdClass();
        if (!isset($object->identifier)) {
            $object->identifier = "";
            $object->caption    = "";
            $object->description = "";
            $object->showdate   = true;
            $object->showtime   = true;
            $object->width = '';
            $object->widths     = array(
                '' => 'Full',
                'col-sm-8' => '8 Col',
                'col-sm-6' => '6 Col',
                'col-sm-4' => '4 Col',
                'col-sm-3' => '3 Col',
                'col-sm-2' => '2 Col',
                'col-sm-1' => '1 Col'
            );
//            $object->is_hidden  = false;
        }
        if (empty($object->description))
            $object->description = "";
        $form->register("identifier", gt('Identifier/Field'), new textcontrol($object->identifier),true, array('required'=>true));
        $form->register("caption", gt('Caption'), new textcontrol($object->caption));
        $form->register("description", gt('Control Description'), new textcontrol($object->description));
        $form->register("showdate",gt('Show Date'), new checkboxcontrol($object->showdate,false));
        $form->register("showtime",gt('Show Time'), new checkboxcontrol($object->showtime,false));
        $form->register('width',gt('Width').': ',new dropdowncontrol($object->width, $object->widths));
//        $form->register("is_hidden", gt('Make this a hidden field on initial entry'), new checkboxcontrol(!empty($object->is_hidden),false));
        if (!expJavascript::inAjaxAction())
            $form->register("submit", "", new buttongroupcontrol(gt('Save'), "", gt('Cancel'), "", 'editable'));
        return $form;
    }

    static function update($values, $object) {
        if ($object == null) {
            $object = new yuicalendarcontrol();
            $object->default = 0;
        }
        if ($values['identifier'] == "") {
            $post = expString::sanitize($_POST);
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST", $post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption    = $values['caption'];
        $object->description = $values['description'];
        $object->showdate   = !empty($values['showdate']);
        $object->showtime   = !empty($values['showtime']);
        if (isset($values['width'])) $object->width = ($values['width']);
//        $object->is_hidden  = isset($values['is_hidden']);
        return $object;
    }

}

?>
