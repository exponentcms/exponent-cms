<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * YUI Date Picker Control
 * standard calendar control w/o time
 * places an update calendar field/button
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class yuicalendarcontrol extends formcontrol {

    var $disable_text = "";
    var $showtime = true;

    static function name() { return "YAHOO! UI Calendar"; }
    static function isSimpleControl() { return false; }
    static function getFieldDefinition() {
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

    function controlToHTML($name,$label=null) {
        $html = "
        <div class=\"yui3-skin-sam\">
            <div id=\"cal".$name."Container\"></div>
            <div id=\"calinput\">
                <input class=\"text\" type=\"text\" name=\"".$name."\" id=\"".$name."\" value=\"".date('m/d/Y',$this->default)."\"/>
                <button class=\"button\" type=\"button\" id=\"update-".$name."\">Update Calendar</button>
            </div>
        </div>
        <div style=\"clear:both\"></div>
        ";
        
        $script = "
            YUI(EXPONENT.YUI3_CONFIG).use('calendar','datatype-date',function(Y) {

                // Create a new instance of calendar, placing it in
                // #mycalendar container, setting its width to 340px,
                // the flags for showing previous and next month's
                // dates in available empty cells to true, and setting
                // the date to today's date.
                var calendar = new Y.Calendar({
                  contentBox: '#cal".$name."Container',
                  width:'340px',
                  showPrevMonth: true,
                  showNextMonth: true,
                });
                calendar.render();

                // Parsing the date string into JS Date value
                var date = Y.DataType.Date.parse('".date('m/d/Y',$this->default)."');
                if (date) {
                    // Highlighting the date stored in the text field
                    calendar.selectDates(date);
                } else {
                    date = new Date();
                }
                // Setting calendar date to show corresponding month
                calendar.set('date', date);

                // Get a reference to Y.DataType.Date
                var dtdate = Y.DataType.Date;

                // Listen to calendar's selectionChange event.
                calendar.on(\"selectionChange\", function (ev) {

                  // Get the date from the list of selected
                  // dates returned with the event (since only
                  // single selection is enabled by default,
                  // we expect there to be only one date)
                  var newDate = ev.newSelection[0];

                  // Format the date and output it to a DOM
                  // element.
                  Y.one('#".$name."').set('value',dtdate.format(newDate,{format:\"%m/%d/%Y\"}));
                });

                function updateCal() {
                    var txtDate1 = document.getElementById(\"".$name."\");
                    if (txtDate1.value != \"\") {
                        var date = Y.DataType.Date.parse(txtDate1.value);
                        calendar.deselectDates();
                        if (date) {
                            // Highlighting the date stored in the text field
                            calendar.selectDates(date);
                        } else {
                            date = new Date();
                            calendar.set('date',date);
                        }
                        calendar.set('date',date);
                    }
                }
                Y.on('click',updateCal,\"#update-".$name."\");
            });
            ";

        expJavascript::pushToFoot(array(
            "unique"=>'calpop-'.$name,
            "yui3mods"=>1,
            "content"=>$script,
            "src"=>""
         ));
        return $html;
    }

    static function parseData($original_name,$formvalues) {
        if (!empty($formvalues[$original_name])) {
            return strtotime($formvalues[$original_name]);
         } else return 0;
    }

    static function templateFormat($db_data, $ctl) {
        // if ($ctl->showtime) {
        //  return strftime(DISPLAY_DATETIME_FORMAT,$db_data);
        // }
        // else {
        //  return strftime(DISPLAY_DATE_FORMAT, $db_data);
        // }
    }


    // function form($object) {
    //  $form = new form();
    //  if (!isset($object->identifier)) {
    //      $object->identifier = "";
    //      $object->caption = "";
    //      $object->showtime = true;
    //  }
    //  $form->register("identifier",gt('Identifier'),new textcontrol($object->identifier));
    //  $form->register("caption",gt('Caption'), new textcontrol($object->caption));
    //  $form->register("showtime",gt('Show Time'), new checkboxcontrol($object->showtime,false));
    // 
    //  $form->register("submit","",new buttongroupcontrol(gt('Save'),"",gt('Cancel'),"",'editable'));
    //  return $form;
    // }

    static function update($values, $object) {
        if ($object == null) {
            $object = new popupdatetimecontrol();
            $object->default = 0;
        }
        if ($values['identifier'] == "") {
            $post = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->showtime = isset($values['showtime']);
        return $object;
    }

}

?>
