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
 * States Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class statescontrol extends dropdowncontrol {

    static function name() { return "Drop Down List - States"; }
    static function isSimpleControl() {
        global $db;

        if ($db->tableExists('geo_region')) {
            return true;
        } else {
            return false;
        }
    }

    function __construct($default = "",$items = array(), $include_blank = false, $multiple=false, $abbv=false, $add_other=false) {
        $this->default = $default;
        $this->items = $items;
        $this->include_blank = $include_blank;
        $this->required = false;
        $this->multiple = $multiple;
        $this->abbv = $abbv;
        $this->add_other = $add_other;
    }

    function controlToHTML($name,$label=null) {
        global $db;

        if ($db->tableExists('geo_region')) {
            $c = $db->selectObject('geo_country', 'is_default=1');
            if (empty($c->id)) $country = 223;
            else $country = $c->id;

            if ($this->multiple) {
//                $this->multiple  = true;
                $this->items[-1] = 'ALL United States';
            }
            /*if (isset($this->add_other)) {
                $this->items[-2] = '-- Specify State Below --';
            }*/
                  //if(!count($states)) $this->items[-2] = '-- Specify State Below --';
            if (!empty($this->add_other)) {
                $this->items[-2] = gt('-- Specify State Below --');
                $this->include_blank = false;
            } else $this->include_blank = !empty($this->include_blank) ? $this->include_blank : false;

            $states = $db->selectObjects('geo_region', 'country_id=' . $country . ' AND active=1 ORDER BY rank, name ASC');
            foreach ($states as $state) {
                // only show the US states unless the theme says to show all us territories
                //if (!in_array($state->id, $not_states)) {
                $this->items[$state->id] = !empty($this->abbv) ? $state->code : $state->name;
                //}
            }

            // sanitize the default value. can accept as id, code abbrv or full name,
            if (!empty($this->default) && !is_numeric($this->default) && !is_array($this->default)) {
                $this->default = $db->selectValue('geo_region', 'id', 'name="' . $this->default . '" OR code="' . $this->default . '"');
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
            $object->add_other = false;
            $object->include_blank = false;
            $object->required = false;
        } 
        if (empty($object->description)) $object->description = "";
        $form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier));
        $form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
        $form->register("default",gt('Default'), new textcontrol($object->default));
        $form->register("size",gt('Size'), new textcontrol($object->size,3,false,2,"integer"));
        $form->register("abbv", gt('Use abbreviations'), new checkboxcontrol($object->abbv,true));
        $form->register("add_other", gt('\'Select State\' entry?'), new checkboxcontrol($object->add_other,true));
        $form->register("include_blank", gt('Include a Blank Entry?'), new checkboxcontrol($object->include_blank,true));
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
        if ($object == null) $object = new statescontrol();
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
        $object->default = $values['default'];
        if (isset($values['size'])) $object->size = (intval($values['size']) <= 0)?1:intval($values['size']);
        $object->abbv = isset($values['abbv']);
        $object->add_other = isset($values['add_other']);
        $object->include_blank = isset($values['include_blank']);
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
            return $db->selectValue('geo_region', 'name', 'id="' . $db_data . '"');
        } else {
            return "";
        }
	}

}

?>
