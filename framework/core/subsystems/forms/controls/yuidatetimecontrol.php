<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
 * Popup YUI Date/Time Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class yuidatetimecontrol extends formcontrol {

    var $showdate = true;
    var $showtime = true;
    
    function name() { return "YUI Date / Time Field"; }
    function isSimpleControl() { return false; }
    function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=>DB_DEF_TIMESTAMP);
    }
    
    function __construct($default = 0, $edit_text = "", $showdate = true, $showtime = true, $display_only=false, $checked=false) {
        $this->default = ($default == 0) ? time() : $default;
        $this->edit_text = $edit_text;
        $this->showdate = $showdate;
        $this->showtime = $showtime;
        $this->display_only = $display_only;        
        $this->checked = ($checked == true || empty($default)) ? true : false;
    }

    function toHTML($label,$name) {
        if (!empty($this->id)) {
            $divID  = ' id="'.$this->id.'Control"';
            $for = ' for="'.$this->id.'"';
        } else {
            $divID  = '';
            $for = '';
        }
        
        if (!$this->showdate && !$this->showtime) return "";
        $html = "<div".$divID." class=\"control datetime-control";
        $html .= (!empty($this->required)) ? ' required">' : '">';
        if(empty($this->flip)){
            $html .= "<label".$for." class=\"label\">".$label."</label>";
            $html .= $this->controlToHTML($name);
        } else {
            $html .= $this->controlToHTML($name);
            $html .= "<label".$for." class=\"label\">".$label."</label>";
        }
        $html .= "</div>";          
        return $html;
    }
    
    function controlToHTML($name) {
        $datectl = new yuicalendarcontrol($this->default,'',false);
        $timectl = new datetimecontrol($this->default,false);
        $datetime = date('l, F d, o g:i a', $this->default);
        
        $html  = '<span id="dtdisplay-'.$name.'">'.$datetime.'</span>';
        if (!$this->display_only) {
            $html .= '<input id="pub-'.$name.'" type="checkbox" name="'.$name.'"';
            $html .= $this->checked ? ' checked>'.$this->edit_text : '>'.$this->edit_text;
            $html .= '<div ';
            $html .= $this->checked ? 'style="display:none"': '';
            $html .= ' id="datetime-'.$name.'">';
            $html .= ($this->showdate) ? $datectl->controlToHTML($name."date") : "";
            $html .= '<div class="yuitime">';
            $html .= ($this->showtime) ? $timectl->controlToHTML($name."time") : "";
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $script = "
        YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
            Y.on('click',function(e){
                var cal = Y.one('#datetime-".$name."');
                if (cal.getStyle('display')=='none') {
                    cal.setStyle('display','block');
                } else {
                    cal.setStyle('display','none');
                }
            },'#pub-".$name."');
        });
        ";
        
        expJavascript::pushToFoot(array(
            "unique"=>"newsmod-".$name,
            "yui3mods"=>"1",
            "content"=>$script,
         ));
             
        return $html;
    }
    
    function onRegister(&$form) {
        //$form->addScript('datetime_disable',PATH_RELATIVE.'subsystems/forms/controls/datetimecontrol.js');
    }
    
    static function parseData($original_name,$formvalues) {
        if (!isset($formvalues[$original_name])) {
            $date = yuicalendarcontrol::parseData($original_name.'date',$formvalues);
            $time = datetimecontrol::parseData($original_name.'time',$formvalues);
            return $date + $time;
        } else return 0;
        //return $time;
    }
    
    function templateFormat($db_data, $ctl) {
        /*
        if ($ctl->showdate && $ctl->showtime) {
            return strftime(DISPLAY_DATETIME_FORMAT,$db_data);
        } 
        elseif ($ctl->showdate) {
            return strftime(DISPLAY_DATE_FORMAT, $db_data);
        }
        elseif ($ctl->showtime) {
            return strftime(DISPLAY_TIME_FORMAT, $db_data);
        }
        else {
            return "";
        }
        */
    }
    
    function form($object) {
        /*
        $form = new form();
        if (!isset($object->identifier)) {
            $object->identifier = "";
            $object->caption = "";
            $object->showdate = true;
            $object->showtime = true;
        } 
        $form->register("identifier",gt('Identifier'),new textcontrol($object->identifier));
        $form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("showdate",gt('Show Date'), new checkboxcontrol($object->showdate,false));
        $form->register("showtime",gt('Show tTme'), new checkboxcontrol($object->showtime,false));
        
        $form->register("submit","",new buttongroupcontrol(gt('Save'),"",gt('Cancel')));
        return $form;
        */
    }
    
    function update($values, $object) {
        /*
        if ($object == null) { 
            $object = new datetimecontrol();
            $object->default = 0; //This will force the control to always show the current time as default
        }
        if ($values['identifier'] == "") {
            $post = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->showdate = isset($values['showdate']);
        $object->showtime = isset($values['showtime']);
        return $object;
        */
    }
}

?>
