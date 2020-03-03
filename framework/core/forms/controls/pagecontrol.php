<?php

##################################################
#
# Copyright (c) 2004-2020 OIC Group, Inc.
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
 * Page Control - Form Wizard Page marker
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class pagecontrol extends formcontrol {

    var $caption = "";
    var $design_time = false;
//    var $placeholder = "";
//    var $pattern = "";
//    var $size = 40;
//    var $maxlength = "";

    static function name() { return "Form Page Break - Wizard"; }
    static function isStatic() { return true; }
    static function isSimpleControl() { return true; }
    static function getFieldDefinition() {
        return null;
    }

    function __construct($default = "", $size=40 , $disabled = false, $maxlength = 0, $filter = "", $required = false, $placeholder = "", $pattern="") {
//        $this->disabled = $disabled;
//        $this->default = $default;
//        $this->placeholder = $placeholder;
//        $this->pattern = $pattern;
//        $this->size = $size;
//        $this->maxlength = $maxlength;
//        $this->filter = $filter;
//        $this->required = $required;
    }

    /**
     * Place the control in the form
     *
     * @param $label
     * @param $name
     * @return string
     */
    function toHTML($label,$name) {
        $caption = !empty($this->caption) ? $this->caption : str_replace(array(":","*"), "", ucwords($label));
        $description = !empty($this->description) ? $this->description : $caption;
        $html  = '<fieldset '.($this->horizontal&&bs3()&&$this->design_time?'class="col-sm-10" ':'').'title="'.$caption.'">
                  <legend>'.$description.'</legend>';
        return $html;
	}

    function controlToHTML($name, $label) {
        $html = "<label class=\"".(bs3()?"control-label":"label").($this->horizontal&&bs3()&&$this->design_time?' col-sm-2':'')."\">".$label."</label>";
        $html .= $this->toHTML($name, $label);
        return $html . '</fieldset>';
    }

    /**
     * Event hook for when control is un-registered (removed) on a form
     *
     * @param $form
     */
    function onUnRegister(&$form) { // Do we need the explicit ref op??
        if ($form->is_paged) $form->is_paged--;
	}

    /**
     * Event hook for when control is registered on a form
     *
     * @param $form
     */
    function onRegister(&$form) { // Do we need the explicit ref op??
        $form->is_paged++;
	}

    static function form($object) {
        $form = new form();
        if (empty($object)) $object = new stdClass();
        if (!isset($object->identifier)) {
            $object->identifier = "";
            $object->caption = "";
            $object->description = "";
//            $object->default = "";
//            $object->placeholder = "";
//            $object->pattern = "";
//            $object->size = 0;
//            $object->maxlength = 0;
//            $object->required = false;
        }
        if (empty($object->description)) $object->description = "";
        $form->register("identifier",gt('Identifier'),new textcontrol($object->identifier));
        $form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
//        $form->register("default",gt('Default'), new textcontrol($object->default));
//        $form->register("placeholder",gt('Placeholder'), new textcontrol($object->placeholder));
//        $form->register("pattern",gt('Pattern'), new textcontrol($object->pattern));
//        $form->register("size",gt('Size'), new textcontrol((($object->size==0)?"":$object->size),4,false,3,"integer"));
//        $form->register("maxlength",gt('Maximum Length'), new textcontrol((($object->maxlength==0)?"":$object->maxlength),4,false,3,"integer"));
//        $form->register("required", gt('Make this a required field.'), new checkboxcontrol($object->required,false));
        if (!expJavascript::inAjaxAction())
            $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
        return $form;
    }

    static function update($values, $object) {
        $this_control = $values['control_type'];
//        if ($object == null) $object = new textcontrol();
        if ($object == null) $object = new $this_control();
        if ($values['identifier'] == "") {
			$post = expString::sanitize($_POST);
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
//        if (isset($values['default'])) $object->default = $values['default'];
//        if (isset($values['placeholder'])) $object->placeholder = $values['placeholder'];
//        if (isset($values['pattern'])) $object->pattern = $values['pattern'];
//        if (isset($values['size'])) $object->size = (int)($values['size']);
//        if (isset($values['maxlength'])) $object->maxlength = (int)($values['maxlength']);
//        $object->required = isset($values['required']);
        $object->is_static = 1;
        return $object;
    }

}

?>
