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
 * Popup YUI Date/Time Picker Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class yuicalendarcontrol extends formcontrol {

    var $disable_text = "";
    var $showtime = true;

    function name() { return "YAHOO! UI Calendar"; }
    function isSimpleControl() { return false; }
    function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=>DB_DEF_TIMESTAMP);
    }

    function __construct($default = null, $disable_text = "",$showtime = true) {
        $this->disable_text = $disable_text;
        $this->default = $default;
        $this->showtime = $showtime;

        if ($this->default == null) {
            if ($this->disable_text == "") $this->default = time();
            else $this->disabled = true;
        }
        elseif ($this->default == 0) {
            $this->default = time();
        }
    }

    function onRegister(&$form) {
        // $form->addScript("jscal-calendar",      PATH_RELATIVE."external/jscalendar/calendar.js");
        // $form->addScript("jscal-calendar-lang", PATH_RELATIVE."external/jscalendar/lang/calendar-en.js");
        // $form->addScript("jscal-calendar-setup",PATH_RELATIVE."external/jscalendar/calendar-setup.js");
        // $form->addScript("popupdatetimecontrol",PATH_RELATIVE."js/PopupDateTimeControl.js");
    }

    function controlToHTML($name) {
        $html = "
        <div class=\"yui-skin-sam\">
            <div id=\"cal".$name."Container\"></div>
            <div id=\"calinput\">
                <input class=\"text\" type=\"text\" name=\"".$name."\" id=\"".$name."\" />
                <button class=\"button\" type=\"button\" id=\"update-".$name."\">Update Calendar</button>
            </div>
        </div>
        <div style=\"clear:both\"></div>
        ";
        
        $script = "
        YAHOO.namespace(\"example.calendar\");

            YAHOO.example.calendar.init = function() {

                function handleSelect(type,args,obj) {
                    var dates = args[0]; 
                    var date = dates[0];
                    var year = date[0], month = date[1], day = date[2];

                    var txtDate1 = document.getElementById(\"".$name."\");
                    txtDate1.value = month + \"/\" + day + \"/\" + year;
                }

                function updateCal() {
                    var txtDate1 = document.getElementById(\"".$name."\");

                    if (txtDate1.value != \"\") {
                        YAHOO.example.calendar.cal".$name.".select(txtDate1.value);
                        var selectedDates = YAHOO.example.calendar.cal".$name.".getSelectedDates();
                        var firstDate = selectedDates[0];
                        YAHOO.example.calendar.cal".$name.".cfg.setProperty(\"pagedate\", (firstDate.getMonth()+1) + \"/\" + firstDate.getFullYear());
                        YAHOO.example.calendar.cal".$name.".render();

                    }
                }

                // For this example page, stop the Form from being submitted, and update the cal instead
                function handleSubmit(e) {
                    updateCal();
                    YAHOO.util.Event.preventDefault(e);
                }
                YAHOO.example.calendar.cal".$name." = new YAHOO.widget.Calendar(\"cal".$name."\",\"cal".$name."Container\",{selected:'".date('m/d/Y',$this->default)."'});
                YAHOO.example.calendar.cal".$name.".selectEvent.subscribe(handleSelect, YAHOO.example.calendar.cal".$name.", true);
                YAHOO.example.calendar.cal".$name.".select('".date('m/d/Y',$this->default)."');
                YAHOO.example.calendar.cal".$name.".render();
                YAHOO.util.Event.addListener(\"update-".$name."\", \"click\", updateCal);
                YAHOO.util.Event.addListener(\"dates-".$name."\", \"submit\", handleSubmit);
            }

            YAHOO.util.Event.onDOMReady(YAHOO.example.calendar.init);
        
        ";
        exponent_javascript_toFoot('calpop'.$name, 'calendar', null, $script);
        return $html;
    }

    static function parseData($original_name,$formvalues) {
        if (!empty($formvalues[$original_name])) {
            return strtotime($formvalues[$original_name]);
         } else return 0;
    }

    function templateFormat($db_data, $ctl) {
        // if ($ctl->showtime) {
        //  return strftime(DISPLAY_DATETIME_FORMAT,$db_data);
        // }
        // else {
        //  return strftime(DISPLAY_DATE_FORMAT, $db_data);
        // }
    }


    // function form($object) {
    //  if (!defined("SYS_FORMS")) require_once(BASE."subsystems/forms.php");
    //  exponent_forms_initialize();
    // 
    //  $form = new form();
    //  if (!isset($object->identifier)) {
    //      $object->identifier = "";
    //      $object->caption = "";
    //      $object->showtime = true;
    //  }
    // 
    //  $i18n = exponent_lang_loadFile('subsystems/forms/controls/popupdatetimecontrol.php');
    // 
    //  $form->register("identifier",$i18n['identifier'],new textcontrol($object->identifier));
    //  $form->register("caption",$i18n['caption'], new textcontrol($object->caption));
    //  $form->register("showtime",$i18n['showtime'], new checkboxcontrol($object->showtime,false));
    // 
    //  $form->register("submit","",new buttongroupcontrol($i18n['save'],"",$i18n['cancel']));
    //  return $form;
    // }

    function update($values, $object) {
        if ($object == null) {
            $object = new popupdatetimecontrol();
            $object->default = 0;
        }
        if ($values['identifier'] == "") {
            $i18n = exponent_lang_loadFile('subsystems/forms/controls/popupdatetimecontrol.php');
            $post = $_POST;
            $post['_formError'] = $i18n['id_req'];
            exponent_sessions_set("last_POST",$post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->showtime = isset($values['showtime']);
        return $object;
    }

}

?>
