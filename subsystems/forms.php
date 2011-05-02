<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

/* exdoc
 * The definition of this constant lets other parts
 * of the system know that the Forms Subsystem
 * has been included for use.
 *
 * @node Subsystems:Forms
 */
define("SYS_FORMS",1);

/* exdoc
 * Initialize the Subsystem
 *
 * This function includes files that would not otherwise be needed,
 * but are required for the Forms Subsystem to function properly.
 *
 * For servers running PHP5, this function registers the form class
 * file directories with the AutoLoader subsystem.
 *
 * For servers running PHP4, initializing the Forms Subsystem
 * can be a bit time-consuming, although testing and profiling done
 * justifies the existence of the subsystem.  The overhead is barely
 * noticeable, but it is there.
 *
 * @node Subsystems:Forms
 */
function exponent_forms_initialize() {
	$i18n = exponent_lang_loadFile('subsystems/forms.php');
	
	$forms_dir = BASE."subsystems/forms";
	$controls_dir = BASE."subsystems/forms/controls";
	if (phpversion() < 5) {
		if (is_readable($forms_dir)) {
			$dh = opendir($forms_dir);
			while (($file = readdir($dh)) !== false) {
				if (is_readable("$forms_dir/$file") && substr($file,-4,4) == ".php") {
					include_once("$forms_dir/$file");
				}
			}
		} else {
			echo $i18n['forms_dir_unreadable'];
		}
		if (is_readable($controls_dir)) {
			$dh = opendir($controls_dir);
			while (($file = readdir($dh)) !== false) {
				if (is_readable("$controls_dir/$file") && substr($file,-4,4) == ".php") {
					include_once("$controls_dir/$file");
				}
			}
		} else {
			echo $i18n['controls_dir_unreadable'];
		}
	} else {
		if (is_readable($controls_dir)) {
			global $auto_dirs;
			$auto_dirs["forms_forms"] = $forms_dir;
			$auto_dirs["forms_controls"] = $controls_dir;
		} else {
			echo $i18n['controls_dir_unreadable'];
		}
	}
}

/* exdoc
 * This function complements exponent_forms_cleanup, by properly
 * cleaning up AutoLoader modifications made by the initialization.
 *
 * While this only benefits servers running PHP5, it does not adversely
 * affect PHP4 servers.  For best practices, always call exponent_forms_cleanup
 * if you have called exponent_forms_initialize.
 *
 * @node Subsystems:Forms
 */
function exponent_forms_cleanup() {
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_forms_listControlTypes() {
	$cdh = opendir(BASE."subsystems/forms/controls");
	$list = array();
	while (($ctl = readdir($cdh)) !== false) {
		if (substr($ctl,-4,4) == ".php" && is_readable(BASE."subsystems/forms/controls/$ctl")) {
			if (call_user_func(array(substr($ctl,0,-4),"isSimpleControl"))) {
				$list[substr($ctl,0,-4)] = call_user_func(array(substr($ctl,0,-4),"name"));
			}
		}
	}
	return $list;
}

function exponent_forms_guessControlType($ddcol, $default_value=null, $colname=null) {
	$control = null;

	if (array_key_exists('FORM_FIELD_TYPE', $ddcol)) {
		new $ddcol['FORM_FIELD_TYPE']($default_value);
	} else {
		if ($ddcol[DB_FIELD_TYPE] == DB_DEF_ID && $colname != 'id') {
		        //If the id field is a foreign key reference than we need to try to scaffold
	        	/*$field_str = array();
		        if (stristr($col->Field, '_')) $field_str = split("_id", $col->Field);
		        if ( (count($field_str) > 0) && ($db->tableExists($field_str[0])) ) {
	        	        $foreign_table = $db->describeTable($field_str[0]);
	                	$fcolname = "";
		                foreach ($foreign_table as $forcol) {
		                        if ($forcol->Field == "title" || $forcol->Field == "name") {
	        	                        $fcolname = $forcol->Field;
	                	                break;
	                        	}
	                	}

		                if ($fcolname != "") {
		                        $foreign_key = $db->selectDropdown($field_str[0],$col->Field, null,$fcolname);
	        	                eDebug($foreign_key);
	                	        $control = new dropdowncontrol("", $foreign_key, true);
	                	}
	        	}*/
		} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_ID && $colname == 'id' && $default_value != null) {
		        //$control = new htmlcontrol('<input type="hidden" name="id" value="'.$default_value.'" />');
			return 'editor';
		} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_INTEGER) {
		        //$control = new genericcontrol($default_value);
			return 'text';
		} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_BOOLEAN) {
		        //$control = new genericcontrol($default_value);
			return 'radio';
		} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_TIMESTAMP) {

		} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_DECIMAL) {
	        	//$control = new genericcontrol($default_value);
			return 'text';
		} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_STRING) {
		        if ($ddcol[DB_FIELD_LEN] > 255) {
	        	        //$control = new texteditorcontrol($default_value);
				return 'html';
		        } else {
		                //$control = new genericcontrol($default_value);
				return 'text';
	        	}
		}
	}
	return 'text';
}

?>
