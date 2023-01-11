<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * Dropdown Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class dropdowncontrol extends formcontrol {

    var $type = 'select';
    var $items = array();
    var $size = 1;
    var $include_blank = false;
    var $style = '';
    var $select2 = false;

    static function name() { return "Drop Down List"; }
    static function isSimpleControl() { return true; }
    static function getFieldDefinition() {
        return array(
            DB_FIELD_TYPE=>DB_DEF_STRING,
            DB_FIELD_LEN=>255);
    }

    function __construct($default = "",$items = array(), $include_blank = false, $multiple=false) {
        $this->default = $default;
        $this->items = $items;
        $this->include_blank = $include_blank;
        $this->required = false;
        $this->multiple = $multiple;
    }

    function controlToHTML($name,$label=null) {
        $idname  = (!empty($this->id)) ? ' id="'.$this->id.'"' : (!empty($name)?' id="'.$name.'"':"");
        $disabled = $this->disabled != false ? "disabled" : "";

        $html = ($this->horizontal) ? '<div class="col-sm-10">' : '<div style="display:inline">';

        $html .= '<select'.$idname.' name="' . $name;
        if ($this->multiple) $html.= '[]';
        $html .= ($this->size > 1) ? '" size="' . $this->size . '"' : '"';
        $html .= ' class="select form-control form-select '.$this->class.' '.$disabled.'"';
        if ($this->disabled) $html .= ' disabled="1"';
        if ($this->tabindex >= 0) $html .= ' tabindex="' . $this->tabindex . '"';
        foreach ($this->jsHooks as $hook=>$action) {
            $html .= " $hook=\"$action\"";
        }
        if (@$this->required) {
            $html .= 'required="required" ';
        }
        if (!empty($this->multiple)) $html .= ' multiple';
        if (!empty($this->onchange)) $html .= ' onchange="'.$this->onchange.'" ';
        if (!empty($this->style)) $html .= ' style="' . $this->style . '"';
        if (!empty($this->description))
            $html .= ' aria-describedby="'. $name . 'HelpBlock "';
        $html .= '>';

        if (is_bool($this->include_blank) && $this->include_blank == true) {
            $html .= '<option value=""';
            if (empty($this->default))
                $html .= ' selected';
            $html .= '></option>';
        } elseif (is_string($this->include_blank) && !empty($this->include_blank)) {
            $html .= '<option value=""';
            if (empty($this->default))
                $html .= ' selected';
            $html .= '>'.$this->include_blank.'</option>';
        }

        if (!empty($this->items)) foreach ($this->items as $value=>$caption) {
            $html .= '<option value="' . $value . '"';
            if (is_array($this->default)) {
                if (in_array($value, $this->default)) $html .= " selected";
            } else {
                if ($value == $this->default && !empty($this->default)) $html .= " selected";
            }
            if ($this->select2) {
                $html .= ' data-icon="' . $value . '"';
            }
            $html .= '>' . $caption . '</option>';
        }
        $html .= '</select>';
        if (!empty($this->description))
            $html .= "<div id=\"" . $name . "HelpBlock\" class=\"form-text text-muted\">".$this->description."</div>";
        $html .= '</div>';

        if ($this->select2) {
//            $content = "
//        function format" . $name . "(icon, container) {
//            if (!icon.id) { return icon.text; }
//            var originalOption = icon.element;
//            return $('<span><i class=\"fa-fw ' + $(originalOption).data('icon') + '\"></i> ' + icon.text + '</span>');
//        }
//        $('#" . $name . "').select2({
////            width: \"100%\",
//            templateResult: format" . $name . ",
//            templateSelection: format" . $name . "
//        });
//        ";

            expJavascript::pushToFoot(
                array(
                    "unique" => 'select2-' . $name,
                    "jquery" => "select2",
                    "content" => $this->select2,
                )
            );
            expCSS::pushToHead(array(
    //	    "unique"=>"select2-bootstrap",
    	    "scsscss"=>JQUERY_RELATIVE . "addons/scss/select2-bootstrap-5.scss",
    	    )
    	);
        }

        return $html;
    }

    static function form($object) {
        $form = new form();
        if (empty($object)) $object = new stdClass();
        if (!isset($object->identifier)) {
            $object->identifier = "";
            $object->caption = "";
            $object->description = "";
            $object->default = "";
            $object->size = 1;
            $object->items = array();
            $object->include_blank = false;
            $object->required = false;
        }
        if (empty($object->description)) $object->description = "";
        $form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier),true, array('required'=>true));
        $form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
        $form->register("items",gt('Items'), new listbuildercontrol($object->items,null));
        $form->register("include_blank", gt('Include a Blank Item.'), new checkboxcontrol($object->include_blank,true));
        $form->register("default",gt('Default'), new textcontrol($object->default));
        $form->register("size",gt('Size'), new textcontrol($object->size,3,false,2,"integer"));
        $form->register("required", gt('Make this a required field.'), new checkboxcontrol($object->required,true));
        if (!expJavascript::inAjaxAction())
            $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
        return $form;
    }

    static function update($values, $object) {
        if ($values['identifier'] == "") {
            $post = expString::sanitize($_POST);
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        if ($object == null) $object = new dropdowncontrol();
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
        $object->default = $values['default'];
        $object->items = listbuildercontrol::parseData('items', $values, true);
        $object->include_blank = !empty($values['include_blank']);
        if (isset($values['size'])) $object->size = ((int)($values['size']) <= 0)?1:(int)($values['size']);
        $object->required = !empty($values['required']);
        return $object;
    }
}

?>
