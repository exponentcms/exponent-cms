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
            $class = empty($this->class) ? '' : ' '.$this->class;
            $html = '<div'.$divID.' class="'.$this->type.'-control control'." ".$class." ".$disabled;
            $html .= (!empty($this->required)) ? ' required">' : '">';
      		//$html .= "<label>";
            if($this->required) {
                $labeltag = '<span class="required" title="'.gt('This entry is required').'">*&#160;</span>' . $label;
            } else {
                $labeltag = $label;
            }
            if(empty($this->flip)){
                    $html .= empty($label) ? "" : "<label".$for." class=\"label\">". $labeltag."</label>";
                    $html .= $this->controlToHTML($name, $label);
            } else {
                    $html .= $this->controlToHTML($name, $label);
                    $html .= empty($label) ? "" : "<label".$for." class=\"label\">". $labeltag."</label>";
            }
            $html .= "</div>";
            if ($this->meter) {
                expCSS::pushToHead(array(
            	    "unique"=>"password-meter",
            	    "css"=>".kv-scorebar-border {
            	            margin: 0;
            	            margin-top: 3px;
            	        }"
            	    )
            	);
                expJavascript::pushToFoot(array(
                    "unique"=>"password-meter" . $name,
                    "jquery"=>"strength-meter",
                    "content"=>"$('#".$this->id."').strength({
            toggleMask: false,
            mainTemplate: '<div class=\"kv-strength-container\">{input}<div class=\"kv-meter-container\">{meter}</div></div>',
            rules: {
                minLength: " . MIN_PWD_LEN . ",
            },
        });",
                 ));
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
