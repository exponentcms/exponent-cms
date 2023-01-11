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
class yuicalendarcontrol extends formcontrol
{

//    var $disable_text = "";
    var $type     = 'datetime';
    var $showdate = true;
    var $showtime = false;

    static function name()
    {
        return "Date / Time - Calendar Display";
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

//    function __construct($default = null, $disable_text = "", $showtime = true) {  //FIXME $disable_text & $showtime are NOT used
    function __construct($default = null, $showdate = true, $showtime = false)
    {
//        $this->disable_text = $disable_text;
        if (empty($default)) {
            $default = time();
        }
        $this->default = $default;
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

    function toHTML($label, $name)
    {
        if (!$this->showdate && !$this->showtime) {
            return "";
        }
        return parent::toHTML($label, $name);
    }

    function controlToHTML($name, $label = null)
    {
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
        if ($this->horizontal)
            $date_input->horizontal_top = true;
//        $date_input->id = $idname;
//        $date_input->name = $idname;
//        $date_input->disabled = 'disabled';
//        $html = "<!-- cke lazy -->";
        $html = '<div class="input-group input-append col-sm-10" id="'.$idname.'dateRangePicker" data-td-target-input="nearest" style="width:inherit;">'.$date_input->toHTML(null, $name).'</div>';
        if (!empty($this->description))
            $html .= "<div id=\"" . $name . "HelpBlock\" class=\"form-text text-muted\">".$this->description."</div>";
//        $html .= "
//        <div style=\"clear:both\"></div>
//        ";

        $script = "
            $(document).ready(function() {
                var tclock = new tempusDominus.TempusDominus(document.getElementById('" . $idname . "dateRangePicker'),{
                    localization: {
                        locale: '" . str_replace("_", "-", LOCALE) . "',
                        format: '" .'L' . ($this->showtime ? ' LT' : '') ."',
                    },
                    stepping: 15,
                    display: {
                        buttons: {
                            today: true,
        //                    clear: false,
        //                    close: false
                        },
                        inline: true,
                        sideBySide: true,
                    }
                });

                if (" . (USE_BOOTSTRAP_ICONS ? '1' : '0') . ") {
                    tclock.updateOptions({
                        display: {
                            icons: {
                                time: 'bi bi-clock',
                                date: 'bi bi-calendar3',
                                up: 'bi bi-arrow-up',
                                down: 'bi bi-arrow-down',
                                previous: 'bi bi-chevron-left',
                                next: 'bi bi-chevron-right',
                                today: 'bi bi-calendar-check',
                                clear: 'bi bi-trash',
                                close: 'bi bi-x',
                            },
                        }
                    });
                }
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
                "jquery"    => "tempus-dominus",
                "bootstrap" => "collapse",
                "content"   => $script,
            )
        );
        return $html;
    }

    static function parseData($name, $values, $for_db = false)
    {
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
    static function templateFormat($db_data, $ctl)
    {
//        if ($ctl->showtime) {
//            return strftime(DISPLAY_DATETIME_FORMAT,$db_data);
//        } else {
//            return strftime(DISPLAY_DATE_FORMAT, $db_data);
//        return gmstrftime(DISPLAY_DATE_FORMAT, $db_data);
        $date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT), $db_data);
        if (!$date) {
            $date = date('m/d/y', $db_data);
        }
        return $date;
//        }
    }

    static function form($object)
    {
        $form = new form();
        if (empty($object)) $object = new stdClass();
        if (!isset($object->identifier)) {
            $object->identifier = "";
            $object->caption    = "";
            $object->description = "";
            $object->showtime   = true;
//            $object->is_hidden  = false;
        }
        if (empty($object->description)) $object->description = "";
        $form->register("identifier", gt('Identifier/Field'), new textcontrol($object->identifier),true, array('required'=>true));
        $form->register("caption", gt('Caption'), new textcontrol($object->caption));
        $form->register("description", gt('Control Description'), new textcontrol($object->description));
        $form->register("showtime",gt('Show Time'), new checkboxcontrol($object->showtime,false));
//        $form->register("is_hidden", gt('Make this a hidden field on initial entry'), new checkboxcontrol(!empty($object->is_hidden),false));
        if (!expJavascript::inAjaxAction())
            $form->register("submit", "", new buttongroupcontrol(gt('Save'), "", gt('Cancel'), "", 'editable'));
        return $form;
    }

    static function update($values, $object)
    {
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
        $object->showtime   = !empty($values['showtime']);
//        $object->is_hidden  = isset($values['is_hidden']);
        return $object;
    }

}

?>
