<?php

/** Use <textarea> for char and varchar
* @link http://www.adminer.org/plugins/#use
* @author Jakub Vrana, http://www.vrana.cz/
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerEditTextSerializedarea {

//	function selectVal(&$val, $link, $field) {
//		// copied from tinymce.php
////		if (ereg("_html", $field["field"]) && $val != '&nbsp;') {
//		if (ereg("location_data", $field["field"]) || ereg("internal", $field["field"]) || ereg("external", $field["field"]) || ereg("config", $field["field"])) {
//			$val = '<div title="'.htmlentities(print_r(self::expUnserialize(html_entity_decode($val)),true)).'">'.$val.'</div>';
//		}
//	}
	
	function editInput($table, $field, $attrs, $value) {
//		if (ereg('text', $field["type"])) {
		if (ereg("location_data|internal|external|config|data", $field["field"])) {
//			return '<input value="' . h($value) . '" title="' . htmlentities(print_r(self::expUnserialize($value),true)) . '" maxlength=250 size=40 $attrs>';
            return "<textarea title=\"" . htmlentities(print_r(self::expUnserialize($value),true)) . "\" cols='38' rows='2'$attrs>" . h($value) . '</textarea>';
		}
	}
	
	function expUnserialize($serial_str) {
		if ($serial_str === 'Array') return null;  // empty array string??
		$out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
		$out2 = unserialize($out);
		if (is_array($out2) && !empty($out2['moduledescription'])) {  // work-around for links in module descriptions
			$out2['moduledescription'] = stripslashes($out2['moduledescription']);
		}
		return $out2;
	}

}
