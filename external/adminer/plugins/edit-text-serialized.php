<?php
##################################################
#
# Copyright (c) 2004-2019 OIC Group, Inc.
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
      if ($serial_str === 'Array') return null;  // empty array string??
      if (is_array($serial_str) || is_object($serial_str)) return $serial_str;  // already unserialized
//        $out1 = preg_replace_callback(
//            '!s:(\d+):"(.*?)";!s',
//            create_function ('$m',
//                '$m_new = str_replace(\'"\',\'\"\',$m[2]);
//                return "s:".strlen($m_new).\':"\'.$m_new.\'";\';'
//            ),
//            $serial_str );
        $out = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($m) {
                $m_new = str_replace('"','\"',$m[2]);
                return "s:".strlen($m_new).':"'.$m_new.'";';
            }, $serial_str );
//        if ($out1 !== $out) {
//            eDebug('problem:<br>'.$out.'<br>'.$out1);
//        }
      $out2 = unserialize($out);
      if (is_array($out2)) {
          if (!empty($out2['moduledescription'])) {  // work-around for links in module descriptions
              $out2['moduledescription'] = stripslashes($out2['moduledescription']);
          }
          if (!empty($out2['description'])) {  // work-around for links in forms descriptions
              $out2['description'] = stripslashes($out2['description']);
          }
          if (!empty($out2['report_desc'])) {  // work-around for links in forms report descriptions
              $out2['report_desc'] = stripslashes($out2['report_desc']);
          }
          if (!empty($out2['response'])) {  // work-around for links in forms response
              $out2['response'] = stripslashes($out2['response']);
          }
          if (!empty($out2['auto_respond_body'])) {  // work-around for links in forms auto respond
              $out2['auto_respond_body'] = stripslashes($out2['auto_respond_body']);
          }
      } elseif (is_object($out2) && get_class($out2) == 'htmlcontrol') {
          $out2->html = stripslashes($out2->html);
      }
      return $out2;
  }

}
