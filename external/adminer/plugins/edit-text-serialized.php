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
//        $out1 = @preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
        $out = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            create_function ('$m',
                '$m_new = str_replace(\'"\',\'\"\',$m[2]);
                return "s:".strlen($m_new).\':"\'.$m_new.\'";\';'
            ),
            $serial_str );
//        if ($out1 !== $out) {
//            eDebug('problem:<br>'.$out.'<br>'.$out1);
//        }
        $out2 = unserialize($out);
        if (is_array($out2) && !empty($out2['moduledescription'])) {  // work-around for links in module descriptions
            $out2['moduledescription'] = stripslashes($out2['moduledescription']);
        } elseif (is_object($out2) && get_class($out2) == 'htmlcontrol') {
            $out2->html = stripslashes($out2->html);
        }
        return $out2;
	}

}
