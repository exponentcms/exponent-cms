<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * Country Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class countrycontrol extends dropdowncontrol {

    static function name() { return "Drop Down List - Countries"; }
    static function isSimpleControl() {
        global $db;

        if ($db->tableExists('geo_country')) {
            return true;
        } else {
            return false;
        }
    }

    function __construct($default = "",$items = array(), $include_blank = false, $multiple=false, $abbv=false, $show_all=false) {
        $this->default = $default;
        $this->items = $items;
        $this->include_blank = $include_blank;
        $this->required = false;
        $this->multiple = $multiple;
        $this->abbv = $abbv;
        $this->show_all = $show_all;
    }

    function controlToHTML($name,$label=null) {
        global $db;

        if ($db->tableExists('geo_country')) {
//            $this->include_blank = isset($this->include_blank) ? $this->include_blank : false;
//            if (isset($params['multiple'])) {
//                $this->multiple = true;
//                //$this->items[-1] = 'ALL United States';
//            }

            if ($this->show_all) $countries = $db->selectObjects('geo_country', null, 'name ASC');
            else $countries = $db->selectObjects('geo_country', 'active=1', 'name ASC');

            foreach ($countries as $country) {
                //if (!in_array($country->id, $not_countries)) {
                $this->items[$country->id] = !empty($this->abbv) ? $country->iso_code_3letter : $country->name;
                //}
            }

            // sanitize the default value. can accept as id, code abbrv or full name,
            if (!empty($this->default) && !is_numeric($this->default) && !is_array($this->default)) {
                $this->default = $db->selectValue('geo_country', 'id', 'name="' . $this->default . '" OR code="' . $this->default . '"');
            }
        } else {
            echo "NO TABLE";
            exit();
        }

        return parent::controlToHTML($name,$label);
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
            $object->abbv = false;
            $object->show_all = false;
            $object->required = false;
        } 
        if (empty($object->description)) $object->description = "";
        $form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier));
        $form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
        $form->register("default",gt('Default'), new textcontrol($object->default));
        $form->register("size",gt('Size'), new textcontrol($object->size,3,false,2,"integer"));
        $form->register("abbv", gt('Use abbreviations?'), new checkboxcontrol($object->abbv,true));
        $form->register("show_all", gt('Show all countries?'), new checkboxcontrol($object->show_all,true));
        $form->register("required", gt('Make this a required field.'), new checkboxcontrol($object->required,true));
        $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
        return $form;
    }

    static function update($values, $object) {
        if ($values['identifier'] == "") {
            $post = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        if ($object == null) $object = new countrycontrol();
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
        $object->default = $values['default'];
        if (isset($values['size'])) $object->size = (intval($values['size']) <= 0)?1:intval($values['size']);
        $object->abbv = isset($values['abbv']);
        $object->show_all = isset($values['show_all']);
        $object->required = isset($values['required']);
        return $object;
    }

    /**
     * Format the control's data for user display
     *
     * @param $db_data
     * @param $ctl
     * @return string
     */
    static function templateFormat($db_data, $ctl) {
        global $db;

        if (isset($db_data)) {
            return $db->selectValue('geo_country', 'name', 'id="' . $db_data . '"');
        } else {
            return "";
        }
	}

}

?>
