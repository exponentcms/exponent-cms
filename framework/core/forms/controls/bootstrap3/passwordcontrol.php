<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * Generic HTML Input Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class passwordcontrol extends genericcontrol {

    var $meter = false;

    static function name() { return "password"; }

    function __construct($type="password", $default = false, $class="", $filter="", $checked=false, $required = false, $validate="", $onclick="", $label="", $maxlength="", $placeholder="", $pattern="") {
        parent::__construct('password', $default, $class, $filter, $checked, $required, $validate, $onclick, $label, $maxlength, $placeholder, $pattern);
    }

    function toHTML($label,$name) {
        if (!empty($this->id)) {
            $divID  = ' id="'.$this->id.'Control"';
            $for = ' for="'.$this->id.'"';
        } else {
            $divID  = ' id="'.$name.'Control"';
            $for = '';
        }
//        if ($this->required) $label = "*" . $label;
        $disabled = $this->disabled == true ? "disabled" : "";
        if ($this->type != 'hidden') {
            $html = '';
             if ($this->meter) {
                 $html .= "<div class=\"row " . $this->id . "-meter\">";
             }
            $class = empty($this->class) ? '' : ' '.$this->class;
            $html .= '<div' . $divID . ' class="' . $this->type . '-control control form-group ' . $class . '" ' . $disabled;
            $html .= (!empty($this->required)) ? ' required="required">' : '>';
      		//$html .= "<label>";
            if($this->required) {
                $labeltag = '<span class="required" title="'.gt('This entry is required').'">*&#160;</span>' . $label;
            } else {
                $labeltag = $label;
            }
            if(empty($this->flip)){
                    $html .= empty($label) ? "" : "<label".$for." ".(bs3()?"class=\"control-label\"":"").(($this->horizontal)?"col-sm-2 control-label":"" ).">". $labeltag."</label>";
                    $html .= $this->controlToHTML($name, $label);
            } else {
                    $html .= $this->controlToHTML($name, $label);
                    $html .= empty($label) ? "" : "<label".$for." ".(bs3()?"class=\"control-label\"":"").">". $labeltag."</label>";
            }
            $html .= "</div>";
            if ($this->meter) {
                expCSS::pushToHead(array(
            	    "unique"=>"password-meter",
            	    "css"=>".kv-scorebar-border {
            	            margin: 0;
            	            margin-top: 3px;
            	            margin-left: 15px;
            	            margin-right: 15px;
            	        }"
            	    )
            	);
                expJavascript::pushToFoot(array(
                    "unique"=>"password-meter" . $name,
                    "jquery"=>"strength-meter",
                    "content"=>"$('#".$this->id."').strength({
            toggleMask: false,
//            mainTemplate: '<div class=\"kv-strength-container\">{input}<div class=\"kv-meter-container\">{meter}</div></div>',
            rules: {
                minLength: " . MIN_PWD_LEN . ",
            },
        });",
                ));

//                expJavascript::pushToFoot(array(
//                    "unique"=>"password-meter".$name,
//                    "jquery"=>"pwstrength-bootstrap",
//                    "content"=>"$(document).ready(function () {
//            \"use strict\";
//            var options = {};
//            options.common = {
//                minChar: " . MIN_PWD_LEN . ",
//            };
//            options.ui = {
//                container: \"." . $this->id . "-meter\",
//                showVerdictsInsideProgressBar: true,
//                showErrors: true,
//                viewports: {
//                    progress: \".pwstrength_viewport_progress\",
//                    errors: \".pwstrength_viewport_progress\",
//                }
//            };
//            $('#" . $this->id . "').pwstrength(options);
//        });",
//                 ));
//                $html .= "<div class=\"" . $this->class . "\" style=\"padding-top: 8px;\">
//                    <div class=\"pwstrength_viewport_progress\"></div>
//                </div>";
                $html .= "</div>";
            }
        } else {
            $html = $this->controlToHTML($name, $label);
        }
        return $html;
    }

    static function form($object) {
		$form = parent::form($object);
		$form->registerBefore("required",'meter',gt('Meter'), new checkboxcontrol($object->meter,false));
		return $form;
    }

    static function update($values, $object) {
		$object = parent::update($values, $object);
		$object->meter = !empty($values['meter']);
		return $object;
    }

}

?>
