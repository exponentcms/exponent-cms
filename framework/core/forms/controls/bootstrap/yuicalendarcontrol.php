<?php

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

if (!defined('EXPONENT')) {
    exit('');
}

//FIXME this is NOT a bootstrap control, but jQuery
/**
 * Date Picker Control (compat w/ non-jQuery controls}
 * standard calendar control w/o time
 * places an update calendar field/button
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class yuicalendarcontrol extends formcontrol
{

//    var $disable_text = "";
//    var $showtime = true;

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
    function __construct($default = null)
    {
//        $this->disable_text = $disable_text;
        if (empty($default)) {
            $default = time();
        }
        $this->default = $default;
//        $this->showtime     = $showtime;

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

    function controlToHTML($name, $label = null)
    {
        $idname = str_replace(array('[', ']', ']['), '_', $name);
        if (is_numeric($this->default)) {
            $default = date('m/d/Y', $this->default);
        } else {
            $default = $this->default;
        }

        $date_input = new textcontrol($default);
        $date_input->id = $idname;
        $date_input->name = $idname;
        $html = $date_input->toHTML(null, $name);
//        $html .= "
//        <div style=\"clear:both\"></div>
//        ";

        $script = "
            $('#" . $idname . "').datetimepicker({
            	timepicker: false,
            	format: 'm/d/Y',
            	dayOfWeekStart: " . DISPLAY_START_OF_WEEK . ",
                inline: true
            });
        ";

        expJavascript::pushToFoot(
            array(
                "unique"  => 'zzyuical-' . $idname,
                "jquery"  => "jquery.datetimepicker",
                "content" => $script,
            )
        );
        return $html;
    }

    static function parseData($original_name, $formvalues)
    {
        if (!empty($formvalues[$original_name])) {
            return strtotime($formvalues[$original_name]);
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
        $date = strftime(DISPLAY_DATE_FORMAT, $db_data);
        if (!$date) {
            $date = strftime('%m/%d/%y', $db_data);
        }
        return $date;
//        }
    }

    static function form($object)
    {
        $form = new form();
        if (!isset($object->identifier)) {
            $object = new stdClass();
            $object->identifier = "";
            $object->caption = "";
//          $object->showtime = true;
        }
        $form->register("identifier", gt('Identifier/Field'), new textcontrol($object->identifier));
        $form->register("caption", gt('Caption'), new textcontrol($object->caption));
//      $form->register("showtime",gt('Show Time'), new checkboxcontrol($object->showtime,false));

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
            $post = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST", $post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
//        $object->showtime   = isset($values['showtime']);
        return $object;
    }

}

?>
