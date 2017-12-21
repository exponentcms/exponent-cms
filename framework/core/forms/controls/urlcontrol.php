<?php

##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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
 * URL Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class urlcontrol extends textcontrol {

    var $type = 'url';

    static function name() { return "Text Box - URL"; }

    /**
     * Format the control's data for user display
     *
     * @param $db_data
     * @param $ctl
     * @return string
     */
    static function templateFormat($db_data, $ctl) {
        if (isset($db_data)) {
            if (!empty($ctl->link) && !empty($db_data)) {
                return '<a href="' . $db_data . '">' . $db_data . '</a>';
            } else {
                return $db_data;
            }
        } else {
            return "";
        }
//		return isset($db_data)?$db_data:"";
	}

    static function form($object) {
		$form = parent::form($object);
		$form->registerBefore("required",'link',gt('Output as a link'), new checkboxcontrol($object->link,false));
		return $form;
    }

    static function update($values, $object) {
		$object = parent::update($values, $object);
		$object->link = !empty($values['link']);
		return $object;
    }

}

?>
