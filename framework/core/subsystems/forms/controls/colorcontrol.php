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
 * Color Picker Control
 * text entry hex color w/ pop-up color selector
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class colorcontrol extends formcontrol {

    var $disable_text = "";
    var $default = '';
    var $hide = false;

    static function name() {
        return "YAHOO! UI Color Picker";
    }

    static function isSimpleControl() {
        return false;
    }

    static function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=>DB_DEF_STRING,
            DB_FIELD_LEN=>100
        );
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

    function toHTML($label, $name) {
        if (!empty($this->id)) {
            if (substr($this->id,-2,2) == '[]') $this->id = substr($this->id,0,strlen($this->id)-2);
            $divID = ' id="' . $this->id . 'Control"';
            $for   = ' for="' . $this->id . '"';
        } else {
            $divID = '';
            $for   = '';
        }

        $disabled = $this->disabled != 0 ? "disabled" : "";
        $class    = empty($this->class) ? '' : $this->class;

        $html = "<div" . $divID . " style=\"display:inline\" class=\"" . $this->type . "-control control " . $class . $disabled . "\"";
        $html .= (!empty($this->required)) ? ' required">' : '>';
        //$html .= "<label>";
        if (empty($this->flip)) {
            $html .= "<label for=\"" . $this->id . "\" style=\"display:inline\" class=\"label\">" . $label . "</label>" . $this->controlToHTML($name, $label);
        } else {
            $html .= $this->controlToHTML($name, null) . "<label" . $for . " style=\"display:inline\" class=\"label\">" . $label . "</label>";
        }
        //$html .= "</label>";
        if (!empty($this->description)) $html .= "<div class=\"control-desc\" style=\"display:block;\">" . $this->description . "</div>";
        $html .= "</div>";
        return $html;
    }

    function controlToHTML($name, $label = null) {
        $assets_path = SCRIPT_RELATIVE . 'framework/core/subsystems/forms/controls/assets/';
        $html = "
        <span><input ".(empty($this->hide)?"size=10 type=\"text\"":"type=\"hidden\"")." id=\"" . $this->id . "\" name=\"" . $name . "\" value=\"" . $this->default . "\" class=\"text colorbox\" />
        <div id='divpreview-" . $this->id . "' style='background-color:" . $this->default . "'> </div></span>
        <div id='container-" . $this->id . "' style='display:none'>
            <div id='picker-" . $this->id . "'></div>
            <div style=\"clear:both\"></div>
            <a id='updateColors-" . $this->id . "'>".gt('Select Current Color')."</a>
            <a id='cancelColors-" . $this->id . "'>".gt('Cancel')."</a>
        </div>
        ";

        $script = "
            YUI(EXPONENT.YUI3_CONFIG).use('gallery-colorpicker', function (Y) {
                // create a picker and render it
                var picker = new Y.ColorPicker();
                picker.render('#picker-" . $this->id . "');
                var	hex = picker.get('hex');
                var colorbox = Y.one('#" . $this->id . "');
                var colorpicker = Y.one('#container-" . $this->id . "');
                var preview = Y.one('#divpreview-" . $this->id . "');
                // This flag used to track mouse position
                var isMouseOverColorpicker = false;

                function getColor() {
                    if (!isMouseOverColorpicker) {
                        var colortext = colorbox.get('value');
                        if (colortext != null) {
                            var color = parseInt(colortext.substr(1,6),16);
                            color = color.toString(16);
                            color = '000000'.substr(0, 6 - color.length) + color;
                        } else {
                            var color = 000000;
                        }
                        picker.set('hex',color);
                        updateColors();
                    }
                }

                function updateColors() {
                    hex = picker.get('hex');
                    colorbox.set('value','#'+hex.toUpperCase());
                    document.getElementById('divpreview-" . $this->id . "').style.backgroundColor='#'+hex.toUpperCase();
                    colorpicker.setStyle('display', 'none');
                }

                // retrieve the selected value from the picker
                Y.one('#updateColors-" . $this->id . "').on('click',function(ev) {
                    ev.halt();
                    updateColors();
                });
                Y.one('#cancelColors-" . $this->id . "').on('click',function(ev) {
                    ev.halt();
                    colorpicker.setStyle('display', 'none');
                });

                // retrieve the selected value from the picker
                preview.on('click',function(ev) {
                    ev.halt();
                    showColorpicker(ev);
                });

                // Tracking mouse position
                colorpicker.on('mouseover', function () {
                    isMouseOverColorpicker = true;
                });
                colorpicker.on('mouseout', function () {
                    isMouseOverColorpicker = false;
                });

                // show/hide colorpicker when text field gains/loses focus
                colorbox.on('focus', function(ev) {
                    showColorpicker(ev);
                });
                colorbox.on('blur', function(ev) {
                    if (!isMouseOverColorpicker) {
                        ev.halt();
                        getColor();
                    }
                });

                var showColorpicker = function () {
                    colorpicker.setStyle('display', '');
                };

                getColor();
            });
        ";

        $css = "
            #container-" . $this->id ." {
                width: 290px;
                background-color: #ccc;
                border-radius: 15px;
                -webkit-border-radius: 15px;
                -moz-border-radius: 15px;
                margin: 0 auto;
                padding: 10px;
                display: block;
                position: absolute;
                z-index:1000;
            }
            #updateColors-" . $this->id .", #cancelColors-" . $this->id ." {
                display: inline-block;
                margin: 10px 0px;
                background-color: #666;
                color: #fff;
                padding: 5px;
                padding-left: 10px;
                padding-right: 10px;
                border-radius: 5px;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                text-decoration: none;
                margin-bottom: 0;
            }
            #divpreview-" . $this->id ." {
                border-radius: 5px;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                display:inline-block;
                height:20px;
                width:20px;
                border:1px solid #d4d4d4;
                margin-bottom: 5px;
                vertical-align: middle;
            }
            #updateColors-" . $this->id .":hover, #cancelColors-" . $this->id .":hover {
                background-color: #999;
                cursor:pointer;
            }
        ";
        expCSS::pushToHead(array(
    	    "unique"=>"colorpicker" . $this->id,
    	    "link"=>"http://yui.yahooapis.com/combo?gallery-2011.09.14-20-40/build/gallery-colorpicker/assets/gallery-colorpicker-core.css",
            "css"=>$css
        ));

        expJavascript::pushToFoot(array(
            "unique"  => 'zzcolor' . $this->id,
            "yui3mods"=> 1,
            "content" => $script,
//            "src"=>""
        ));
        return $html;
    }

    static function parseData($original_name, $formvalues) {
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
    // 
    //  $form->register("identifier",gt('Identifier'),new textcontrol($object->identifier));
    //  $form->register("caption",gt('Caption'), new textcontrol($object->caption));
    //  $form->register("showtime",gt('Show Time'), new checkboxcontrol($object->showtime,false));
    // 
    //  $form->register("submit","",new buttongroupcontrol(gt('Save'),"",bt'Cancel'),"",'editable'));
    //  return $form;
    // }

    static function update($values, $object) {
        if ($object == null) {
            $object          = new popupdatetimecontrol();
            $object->default = 0;
        }
        if ($values['identifier'] == "") {
            $post               = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST", $post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption    = $values['caption'];
        $object->showtime   = isset($values['showtime']);
        return $object;
    }

}

?>
