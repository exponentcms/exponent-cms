<?php

/** Use <textarea> for char and varchar
* @link http://www.adminer.org/plugins/#use
* @author Jakub Vrana, http://www.vrana.cz/
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerEditTextSerializedarea {

	function selectVal(&$val, $link, $field) {
//		if (ereg("_html", $field["field"]) && $val != '&nbsp;') {
		if (ereg("location_data|internal|external|config|data", $field["field"])) {
			$val = '<div title="'.htmlentities(print_r(self::expUnserialize(html_entity_decode($val)),true)).'">'.$val.'</div>';
		}
	}
	
	function editInput($table, $field, $attrs, $value) {
//		if (ereg('text', $field["type"])) {
		if (ereg("location_data|internal|external|config|data", $field["field"])) {
//			return '<input value="' . h($value) . '" title="' . htmlentities(print_r(self::expUnserialize($value),true)) . '" maxlength=250 size=40 $attrs>';
            return "<textarea title=\"" . htmlentities(print_r(self::expUnserialize($value),true)) . "\" cols='38' rows='2'$attrs>" . h($value) . '</textarea>';
		}
	}
	
	function expUnserialize($serial_str) {
        if ($serial_str === 'Array') return null;  // empty array string??
        $out = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            create_function ('$m',
                '$m_new = str_replace(\'"\',\'\"\',$m[2]);
                return "s:".strlen($m_new).\':"\'.$m_new.\'";\';'
            ),
            $serial_str );
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
