<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
 * Range Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class rangecontrol extends textcontrol {

    var $min = "";
    var $max = "";
    var $step = "";
    var $type = 'range';

    static function name() { return "Text Box - Range"; }

    function controlToHTML($name, $label) {
        $this->size = !empty($this->size) ? $this->size : 25;
        $idname  = (!empty($this->id)) ? ' id="'.$this->id.'"' : ' id="'.$name.'"';
        if ($this->type != 'text') {
            $extra_class = ' ' . $this->type;
        } else {
            $extra_class = '';
        }
        $html = ($this->horizontal && (bs3()||bs4() || bs5())) ? '<div class="col-sm-10">' : '';
        $html .= '<input' . $idname . ' class="text form-control' . $extra_class . '" type="' . $this->type . '" name="' . $name . '"';
        $html .= " value=\"" . str_replace('"',"&quot;",$this->default) . "\"";
        $html .= $this->size ? " size=\"".$this->size."\"" : "";
        $html .= $this->disabled ? " disabled " : "";
        $html .= $this->focus ? " autofocus" : "";
        $html .= $this->maxlength ? " maxlength=\"".$this->maxlength."\"" : "";
        $html .= ($this->min !== "")  ? " min=\"".$this->min."\"" : " min=\"0\"";
        $html .= ($this->max != "") ? " max=\"".$this->max."\"" : " max=\"100\"";
        $html .= ($this->step != "") ? " step=\"".$this->step."\"" : " step=\"1\"";
        $html .= ($this->tabindex >= 0) ? " tabindex=\"".$this->tabindex."\"" : "";
        $html .= ($this->accesskey != "") ? " accesskey=\"".$this->accesskey."\"" : "";
        $html .= $this->placeholder ? " placeholder=\"".$this->placeholder."\"" : "";
        if (!empty($this->pattern)) $html .= " pattern=\"".$this->pattern."\"";
        if ($this->filter != "") {
            $html .= " onkeypress=\"return ".$this->filter."_filter.on_key_press(this, event);\"";
            $html .= " onblur=\"".$this->filter."_filter.onblur(this);\"";
            $html .= " onfocus=\"".$this->filter."_filter.onfocus(this);\"";
            $html .= " onpaste=\"return ".$this->filter."_filter.onpaste(this, event);\"";
        }

        $caption = !empty($this->caption) ? $this->caption : str_replace(array(":","*"), "", ucwords($label));
        if (!empty($this->required)) $html .= ' required="'.rawurlencode($this->default).'" caption="'.$caption.'"';
        if (!empty($this->description))
            $html .= ' aria-describedby="'. $name . 'HelpBlock "';
        $html .= "/>";
        if (!empty($this->description))
            $html .= "<div id=\"" . $name . "HelpBlock\" class=\"form-text text-muted\">".$this->description."</div>";
        $html .= ($this->horizontal && (bs3()||bs4() || bs5())) ? '</div>' : '';
        return $html;
    }

    static function form($object) {
        $form = parent::form($object);
        $form->registerBefore("required",'min',gt('Minimum'), new textcontrol((empty($object->min)?"":$object->min)));
        $form->registerBefore("required",'max',gt('Maximum'), new textcontrol((empty($object->max)?"":$object->max)));
        $form->registerBefore("required",'step',gt('Step'), new textcontrol((empty($object->step)?"1":$object->step)));
        return $form;
    }

    static function update($values, $object) {
        $object = parent::update($values, $object);
        if (isset($values['min'])) $object->min = (int)($values['min']);
        if (isset($values['max'])) $object->max = (int)($values['max']);
        if (isset($values['step'])) $object->step = (int)($values['step']);
        return $object;
    }

}

?>
