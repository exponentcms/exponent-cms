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
 * Popup Date/Time Picker Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class calendarcontrol extends formcontrol {

    var $disable_text = "";
    var $showtime = true;

    function name() { return "YAHOO! UI Calendar"; }
    function isSimpleControl() { return false; }
    function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=>DB_DEF_TIMESTAMP);
    }

    // function yuicalendarcontrol($default = null, $disable_text = "",$showtime = true) {
    //     $this->disable_text = $disable_text;
    //     $this->default = $default;
    //     $this->showtime = $showtime;
    // 
    //     if ($this->default == null) {
    //         if ($this->disable_text == "") $this->default = time();
    //         else $this->disabled = true;
    //     }
    //     elseif ($this->default == 0) {
    //         $this->default = time();
    //     }
    // }

	function toHTML($label,$name) {
		if (!empty($this->id)) {
		    $divID  = ' id="'.$this->id.'Control"';
		    $for = ' for="'.$this->id.'"';
		} else {
		    $divID  = '';
		    $for = '';
		}
		
		$disabled = $this->disabled != 0 ? "disabled" : "";
		$class = empty($this->class) ? '' : $this->class;
		 
		$html = "<div".$divID." class=\"".$this->type."-control control ".$class.$disabled."\"";
		$html .= (!empty($this->required)) ? ' required">' : '>';
		//$html .= "<label>";
		if(empty($this->flip)){
			$html .= $this->controlToHTML($name, $label);
		} else {
			$html .= "<label".$for." class=\"label\">".$label."</label>";
		}
		//$html .= "</label>";
		$html .= "</div>";			
		return $html;
	}

    function controlToHTML($name,$label) {
    	$assets_path = SCRIPT_RELATIVE.'subsystems/forms/controls/assets/';
        $html = "
        <div id=\"cal-container-".$name."\" class=\"yui-skin-sam control calendar-control\">
            <label for=\"".$name."\" class=\"label\">".$label."</label><input size=26 type=\"text\" id=\"date-".$name."\" name=\"date-".$name."\" value=\"\" class=\"text datebox\" /> 
            @ <input size=3 type=\"text\" id=\"time-h-".$name."\" name=\"time-h-".$name."\" value=\"\" class=\"text timebox\" maxlength=2/>
            : <input size=3 type=\"text\" id=\"time-m-".$name."\" name=\"time-m-".$name."\" value=\"\" class=\"text timebox\" maxlength=2/>
            <select id=\"ampm-".$name."\" name=\"ampm-".$name."\">
                <option>am</option>
                <option>pm</option>
            </select>
        </div>
        <div style=\"clear:both\"></div>
        ";
        
        $script = "
        YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {

            var Event = YAHOO.util.Event,
                Dom = YAHOO.util.Dom,
                dialog,
                calendar;

            // time input restriction to 12 hour
            Y.one('#time-h-".$name."').on('keyup',function(e){
                if (e.target.get('value')>12) {
                    e.target.set('value',12);
                }
                
                if (e.target.get('value')<0) {
                    e.target.set('value',0);
                }
            });
            
            // time input restriction to 12 hour
            Y.one('#time-m-".$name."').on('keyup',function(e){
                if (e.target.get('value')>59) {
                    e.target.set('value',59);
                }
                
                if (e.target.get('value')<0) {
                    e.target.set('value',0);
                }
            });
            

            Event.on(\"date-".$name."\", \"click\", function() {
                
                // Lazy Dialog Creation - Wait to create the Dialog, and setup document click listeners, until the first time the button is clicked.
                if (!dialog) {

                    // Hide Calendar if we click anywhere in the document other than the calendar
                    Event.on(document, \"click\", function(e) {
                        var el = Event.getTarget(e);
                        var dialogEl = dialog.element;
                        if (el != dialogEl && !Dom.isAncestor(dialogEl, el) && el != showBtn && !Dom.isAncestor(showBtn, el)) {
                            dialog.hide();
                        }
                    });

                    function resetHandler() {
                        // Reset the current calendar page to the select date, or 
                        // to today if nothing is selected.
                        var selDates = calendar.getSelectedDates();
                        var resetDate;

                        if (selDates.length > 0) {
                            resetDate = selDates[0];
                        } else {
                            resetDate = calendar.today;
                        }

                        calendar.cfg.setProperty(\"pagedate\", resetDate);
                        calendar.render();
                    }

                    function closeHandler() {
                        dialog.hide();
                    }

                    var dialog = new YAHOO.widget.Dialog(\"container-".$name."\", {
                        visible:false,
                        context:[\"date-".$name."\", \"tl\", \"bl\"],
                        buttons:[ {text:\"Reset\", handler: resetHandler, isDefault:true}, {text:\"Done\", handler: closeHandler}],
                        draggable:false,
                        width:310,
                        close:true
                    });
                    dialog.setHeader('Pick A Date');
                    dialog.setBody('<div id=\"cal-".$name."\" class=\"cal\"></div>');
                    dialog.render(\"cal-container-".$name."\");
                    YAHOO.util.Dom.addClass(\"container-".$name."\", 'calpop');
                    
                    dialog.showEvent.subscribe(function() {
                        if (YAHOO.env.ua.ie) {
                            // Since we're hiding the table using yui-overlay-hidden, we 
                            // want to let the dialog know that the content size has changed, when
                            // shown
                            dialog.fireEvent(\"changeContent\");
                        }
                    });
                }

                // Lazy Calendar Creation - Wait to create the Calendar until the first time the button is clicked.
                if (!calendar) {

                    var calendar = new YAHOO.widget.Calendar(\"cal-".$name."\", {
                        iframe:false,          // Turn iframe off, since container has iframe support.
                        hide_blank_weeks:true  // Enable, to demonstrate how we handle changing height, using changeContent
                    });
                    calendar.render();
                    
                    calendar.selectEvent.subscribe(function() {
                        if (calendar.getSelectedDates().length > 0) {

                            var selDate = calendar.getSelectedDates()[0];

                            // Pretty Date Output, using Calendar's Locale values: Friday, 8 February 2008
                            var wStr = calendar.cfg.getProperty(\"WEEKDAYS_LONG\")[selDate.getDay()];
                            var dStr = selDate.getDate();
                            var mStr = calendar.cfg.getProperty(\"MONTHS_LONG\")[selDate.getMonth()];
                            var yStr = selDate.getFullYear();

                            Dom.get(\"date-".$name."\").value = wStr + \", \" + dStr + \" \" + mStr + \" \" + yStr;
                        } else {
                            Dom.get(\"date-".$name."\").value = \"\";
                        }
                        //dialog.hide();
                    });

                    calendar.renderEvent.subscribe(function() {
                        // Tell Dialog it's contents have changed, which allows 
                        // container to redraw the underlay (for IE6/Safari2)
                        dialog.fireEvent(\"changeContent\");
                    });
                }

                var seldate = calendar.getSelectedDates();

                if (seldate.length > 0) {
                    // Set the pagedate to show the selected date if it exists
                    calendar.cfg.setProperty(\"pagedate\", seldate[0]);
                    calendar.render();
                }

                dialog.show();
            });
        });
        "; // end JS
        
        // css
        expCSS::pushToHead(array(
		    "unique"=>"cal0",
		    "link"=>PATH_RELATIVE."external/yui2/build/button/assets/skins/sam/button.css"
		    )
		);
		
        expCSS::pushToHead(array(
		    "unique"=>"cal1",
		    "link"=>PATH_RELATIVE."external/yui2/build/calendar/assets/skins/sam/calendar.css"
		    )
		);
		
        expCSS::pushToHead(array(
    	    "unique"=>"cal2",
    	    "link"=>$assets_path."calendar/calendarcontrol.css"
    	    )
    	);
	
       
        exponent_javascript_toFoot('calpop'.$name, "button,calendar,container,dragdrop,slider", null, $script);
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
