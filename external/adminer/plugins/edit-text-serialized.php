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

/** Use <textarea> for char and varchar
* @link http://www.adminer.org/plugins/#use
* @author Jakub Vrana, http://www.vrana.cz/
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerEditTextSerializedarea {

    public function selectVal(&$val, $link, $field, $original) {
		if (preg_match("~location_data|internal|external|config|billing_options|extra_fields|user_input_fields|options|meta_fb|data~", $field["field"])) {
			$val = '<div title="'.htmlentities(print_r(self::expUnserialize(html_entity_decode($val)),true)).'">'.$val.'</div>';
		}
	}

	function editInput($table, $field, $attrs, $value) {
		if (preg_match("~location_data|internal|external|config|billing_options|extra_fields|user_input_fields|options|meta_fb|data~", $field["field"])) {
//			return '<input value="' . h($value) . '" title="' . htmlentities(print_r(self::expUnserialize($value),true)) . '" maxlength=250 size=40 $attrs>';
            return "<textarea title=\"" . htmlentities(print_r(self::expUnserialize($value),true)) . "\" cols='38' rows='2'$attrs>" . h($value) . '</textarea>';
		}
	}

    function expUnserialize($serial_str) {
        if ($serial_str === 'Array' || is_null($serial_str))
            return null;  // empty array string??
        if (is_array($serial_str) || is_object($serial_str))
            return $serial_str;  // already unserialized
        $out = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($m) {
                $m_new = str_replace('"','\"',$m[2]);
                return "s:".strlen($m_new).':"'.$m_new.'";';
            }, $serial_str );
        $out2 = @unserialize($out);
        // list of fields with rich text
        $stripList = array(
            'moduledescription',
            'description',
            'report_desc',
            'report_def',
            'report_def_showall',
            'response',
            'auto_respond_body',
            'ecomheader',
            'ecomfooter',
            'cart_description_text',
            'policy',
            'checkout_message_top',
            'checkout_message_bottom',
            'message'
        );
        if (is_array($out2)) {
            foreach ($stripList as $strip) {
                if (!empty($out2[$strip])) {  // work-around for links in rich text
                    $out2[$strip] = stripslashes($out2[$strip]);
                }
            }
        } elseif (is_object($out2) && $out2 instanceof \htmlcontrol) {
            $out2->html = stripslashes($out2->html);
        }
        if ($out2 === false && !empty($out)) {
            $out2 = $out;
        }
        return $out2;
    }

}
