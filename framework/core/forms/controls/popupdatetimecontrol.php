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
 * YUI Popup Date/Time Picker Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class popupdatetimecontrol extends formcontrol {

	var $disable_text = "";
	var $showtime = true;

	static function name() { return "Date / Time - YUI Popup"; }
	static function isSimpleControl() { return true; }
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
		} elseif ($this->default == 0) {
			$this->default = time();
		}
	}

	function onRegister(&$form) {
//		$form->addScript("jscal-calendar",      PATH_RELATIVE."external/jscalendar/calendar.js");
//		$form->addScript("jscal-calendar-lang", PATH_RELATIVE."external/jscalendar/lang/calendar-en.js");
//		$form->addScript("jscal-calendar-setup",PATH_RELATIVE."external/jscalendar/calendar-setup.js");
//		$form->addScript("popupdatetimecontrol",PATH_RELATIVE."js/PopupDateTimeControl.js");
	}

	function controlToHTML($name,$label) {
		if ($this->default == 0) {
			$this->default = time();
		}
		$imgsrc = PATH_RELATIVE."framework/modules/events/assets/images/date_month.png";
		if (is_readable(THEME_ABSOLUTE."icons/calendar_trigger.gif")) {
			$imgsrc = THEME_RELATIVE."icons/calendar_trigger.gif";
		}

        $img = '<img align="texttop" src="'.$imgsrc.'" id="'.$name.'_trigger" ';
        if ($this->disabled) {
            $img .= 'style="visibility: hidden;" ';
        } else {
            $img .= 'style="cursor: pointer;" ';
        }
        $img .= 'title="'.gt('Select Date').'" onclick="return true;" onmouseover="this.style.background=\'red\';" onmouseout="this.style.background=\'\'" />';
        $img .= "\n";

        $html = "";
		$html .= '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.($this->default).'" />';
		$html .= "\n";
		$html .= '<span class="';
		if ($this->disabled) $html .= 'datefield_disabled';
		else $html .= 'datefield';
		$html .= '" id="'.$name.'_span">';
		# for testing
		#$this->default = time();
		if ($this->default == null) {
			$html .= '&lt;'.gt('No Date Selected').'&gt;';
		} else {
			if ($this->showtime) {
                $html .= strftime(DISPLAY_DATE_FORMAT,$this->default).' '.strftime(DISPLAY_TIME_FORMAT,$this->default);
            } else $html .= strftime(DISPLAY_DATE_FORMAT,$this->default);
		}
		$html .= '</span>';
		$html .= "\n";
		if ($this->disable_text != "") {// popupdatetimecontrol_enable(this.form,\''.$name.'\');
			$html .= '<input align="texttop" style="margin-top: -2px;" type="checkbox" name="'.$name.'_disabled" onchange="handleCheck' . $name.'(this.form,\''.$name.'\');" onclick="handleCheck' . $name.'(this.form,\''.$name.'\');" ';
			if ($this->disabled) $html .= ' checked="checked"';
			$html .= '/>'.$this->disable_text;
		} else {
		#	$html .= '<input type="hidden" name="'.$name.'_enabled" value="1" />';
		}
        $html .= '<a class="module-actions" style="z-index:999;" href="javascript:void(0);" id="J_popup_closeable_'.$name.'">'.$img.'</a>';

        $script = "
            EXPONENT.YUI3_CONFIG.modules = {
                'gallery-calendar': {
                    fullpath: '".PATH_RELATIVE."framework/modules/events/assets/js/calendar.js',
                    requires: ['node']
                }
            }

            YUI(EXPONENT.YUI3_CONFIG).use('gallery-calendar','datatype-date',function(Y){
                var today = new Date(".$this->default."*1000);

                //Popup
                var cal = new Y.Calendar('J_popup_closeable_".$name."',{
                    popup:true,
                    closeable:true,
                    startDay:".DISPLAY_START_OF_WEEK.",
                    date:today,
                    withtime:".(!empty($this->showtime)?'true':'false').",
                    action:['click'],
            //        useShim:true
                }).on('select',function(d){
                    var unixtime = parseInt(d / 1000);
                    Y.one('#".$name."').set('value',unixtime);
                    Y.one('#".$name."_span').setHTML(Y.DataType.Date.format(d,{format:'".DISPLAY_DATE_FORMAT.(!empty($this->showtime)?' '.DISPLAY_TIME_FORMAT:'')."'}));
                }).on('timeselect',function(d){
                    var unixtime = parseInt(d / 1000);
                    Y.one('#".$name."').set('value',unixtime);
                    Y.one('#".$name."_span').setHTML(Y.DataType.Date.format(d,{format:'".DISPLAY_DATE_FORMAT.(!empty($this->showtime)?' '.DISPLAY_TIME_FORMAT:'')."'}));
                });
                Y.one('#J_popup_closeable_".$name."').on('click',function(d){
                    cal.show();
                });

                var handleCheck".$name." = function(e) {
                    var cal = Y.one('#J_popup_closeable_".$name."');
                    if (cal.getStyle('display')=='none') {
                        cal.setStyle('display','block');
                    } else {
                        cal.setStyle('display','none');
                    }
                };

//                Y.Global.on('lazyload:cke', function() {
//                    Y.one('#".$name."_disabled').detach('click',handleCheck".$name.");
//                    Y.one('#".$name."_disabled').on('click',handleCheck".$name.");
//                });

            });
        ";

        expCSS::pushToHead(array(
    	    "unique"=>"popcalcss" . $name,
    	    "link"=>PATH_RELATIVE.'framework/modules/events/assets/css/default.css',
//            "css"=>$css
        ));
        expJavascript::pushToFoot(array(
            "unique"  => 'popcal' . $name,
            "yui3mods"=> 1,
            "content" => $script,
        ));
		return $html;
	}

	static function parseData($original_name,$formvalues) {
		if (!isset($formvalues[$original_name.'_disabled'])) {
//			return strtotime($formvalues[$original_name]);
			return $formvalues[$original_name];
		} else return 0;
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
		if ($ctl->showtime) {
//			return strftime(DISPLAY_DATETIME_FORMAT,$db_data);
            $datetime = strftime(DISPLAY_DATETIME_FORMAT, $db_data);
            if (!$datetime) $datetime = strftime('%m/%d/%y %I:%M%p', $db_data);
            return $datetime;
		} else {
//			return strftime(DISPLAY_DATE_FORMAT, $db_data);
            $date = strftime(DISPLAY_DATE_FORMAT, $db_data);
            if (!$date) $date = strftime('%m/%d/%y', $db_data);
            return $date;
		}
	}

	static function form($object) {
		$form = new form();
		if (!isset($object->identifier)) {
            $object = new stdClass();
			$object->identifier = "";
			$object->caption = "";
			$object->showtime = true;
		}
		$form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
		$form->register("showtime",gt('Show Time'), new checkboxcontrol($object->showtime,false));

		$form->register("submit","",new buttongroupcontrol(gt('Save'),"",gt('Cancel'),"",'editable'));
		return $form;
	}

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