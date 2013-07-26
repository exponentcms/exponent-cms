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

/**
* This is the class expTemplate
*
 * @package    Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expTemplate {

	/**
	 * Retrieve Module-Independent View File
	 *
	 * Looks in the theme and the /views directory for a .tpl file
	 * corresponding to the passed view.
	 *
	 * @param string $type One of "modules"", "controls"", "forms" or ""
	 * @param string $name The name the object we are requesting a view from
	 * @param string $view The name of the requested view
	 *
	 * @return string The full filepath of the view template
	 */
	public static function getViewFile($type="", $name="", $view="Default") {
		$viewfilepath = expCore::resolveFilePaths($type, $name, "tpl", $view);
		// Something is really screwed up.
		if ($viewfilepath == false) {
			// Fall back to something that won't error.
//			return BASE . 'framework/core/views/viewnotfound.tpl';
            return TEMPLATE_FALLBACK_VIEW;
		}
		//return first match
		return array_shift($viewfilepath);
	}

	//DEPRECATED: backward compatibility wrapper
	public static function getModuleViewFile($name, $view, $recurse=true) {  //FIXME Not Used 2.2???
		return self::getViewFile("modules", $name, $view);
	}

	public static function getViewConfigForm($module,$view,$form,$values) {  //FIXME Not Used 2.2???
		$form_file = "";
		$resolved_path = null;
		$resolved_path = expCore::resolveFilePaths("modules", $module , "form" , $view);
		if (isset($resolved_path) && $resolved_path != '') {
            $tmppath = expCore::resolveFilePaths("modules", $module , "form" , $view);
			$filepath = array_shift($tmppath);
		} else {
			$filepath = false;
		}

		if ($filepath != false) {
			$form_file = $filepath;
		}

		if ($form == null) $form = new form();
		if ($form_file == "") return $form;

//		$form->register(null,"",new htmlcontrol("<hr size='1' /><b>".gt('Layout Configuration')."</b>"));
        $form->register(null,"",new htmlcontrol("<h2>".gt('Layout Configuration')."</h2>"),true,ucwords($view).' '.gt('View Configuration'));

		$fh = fopen($form_file,"r");
		while (($control_data = fgetcsv($fh,65536,"\t")) !== false) {
			$data = array();
			foreach ($control_data as $d) {
				if ($d != "") $data[] = $d;
			}
			if (!isset($values[$data[0]])) $values[$data[0]] = 0;
			if ($data[2] == "checkbox") {
				$form->register("_viewconfig[".$data[0]."]",$data[1],new checkboxcontrol($values[$data[0]]),true,ucwords($view).' '.gt('View Configuration'));
			} else if ($data[2] == 'text') {
				$form->register("_viewconfig[".$data[0]."]",$data[1],new textcontrol($values[$data[0]]),true,ucwords($view).' '.gt('View Configuration'));
			} else {
				$options = array_slice($data,3);
				$form->register("_viewconfig[".$data[0]."]",$data[1],new dropdowncontrol($values[$data[0]],$options),true,ucwords($view).' '.gt('View Configuration'));
			}
		}

		$form->register("submit","",new buttongroupcontrol("Save","","Cancel"),true,'base');

		return $form;
	}

	public static function getViewConfigOptions($module,$view) {  //FIXME Not Used 2.2???
		$form_file = "";
		$filepath = array_shift(expCore::resolveFilePaths("modules", $module, "form", $view));
		if ($filepath != false) {
			$form_file = $filepath;
		}
		if ($form_file == "") return array(); // no form file, no options

		$fh = fopen($form_file,"r");
		$options = array();
		while (($control_data = fgetcsv($fh,65536,"\t")) !== false) {
			$data = array();
			foreach ($control_data as $d) {
				if ($d != "") $data[] = $d;
			}
			$options[$data[0]] = $data[1];
		}
		return $options;
	}

	public static function getFormTemplates($type) {  //FIXME Not Used???
	    $forms = array();

	    //Get the forms from the base form directory
	    if (is_dir(BASE.'forms/'.$type)) {
	        if ($dh = opendir(BASE.'forms/'.$type)) {
	             while (false !== ($file = readdir($dh))) {
	                if ( (substr($file,-4,4) == ".tpl") && ($file{0} != '_')) {
	                    $forms[substr($file,0,-4)] = substr($file,0,-4);
	                }
	            }
	        }
	    }
	    //Get the forms from the themes form directory.  If the theme has forms of the same
	    //name as the base form dir, then they will overwrite the ones already  in the array $forms.
	    if (is_dir(THEME_ABSOLUTE.'forms/'.$type)) {
	        if ($dh = opendir(THEME_ABSOLUTE.'forms/'.$type)) {
	             while (false !== ($file = readdir($dh))) {
	                if ( (substr($file,-4,4) == ".tpl") && ($file{0} != '_')) {
	                    $forms[substr($file,0,-4)] = substr($file,0,-4);
	                }
	            }
	        }
	    }

	    return $forms;
	}

	/** exdoc
	 *
	 * Looks through the module's views directory and returns
	 * all non-internal views that are found there.
	 * Returns an array of all standard view names.
	 * This array is unsorted.
	 *
	 * @param string $module The classname of the module to get views for.
	 * @return array
	 * @node Subsystems:Template
	 */
	public static function listModuleViews($module) {  //FIXME only used by container 2.0 edit action for OS modules
		return expCore::buildNameList("modules", $module, "tpl", "[!_]*");
	}

	public static function getViewParams($viewfile) {
		$base = substr($viewfile,0,-4);
		$vparam = null;
		if (is_readable($base.'.config')) {
			$vparam = include($base.'.config');
		}
		return $vparam;
	}

	/** exdoc
	 * @state <b>UNDOCUMENTED</b>
	 * @node Undocumented
	 * @return array
	 */
    //FIXME we need to also look for custom & jquery controls
	public static function listControlTypes($include_static = true) {
		$cdh = opendir(BASE."framework/core/forms/controls");
		$list = array();
		while (($ctl = readdir($cdh)) !== false) {
			if (substr($ctl,-4,4) == ".php" && is_readable(BASE."framework/core/forms/controls/$ctl")) {
				if (call_user_func(array(substr($ctl,0,-4),"isSimpleControl"))) {
                    if ($include_static || !call_user_func(array(substr($ctl,0,-4),"isStatic"))) {
                        $list[substr($ctl,0,-4)] = call_user_func(array(substr($ctl,0,-4),"name"));
                    }
				}
			}
		}
		return $list;
	}

    //FIXME we need to also look for custom & jquery controls
	public static function listSimilarControlTypes($type) {
        $oldctl = new $type();
		$cdh = opendir(BASE."framework/core/forms/controls");
		$list = array();
		while (($ctl = readdir($cdh)) !== false) {
			if (substr($ctl,-4,4) == ".php" && is_readable(BASE."framework/core/forms/controls/$ctl")) {
				if (call_user_func(array(substr($ctl,0,-4),"getFieldDefinition")) === $oldctl->getFieldDefinition() && call_user_func(array(substr($ctl,0,-4),"isSimpleControl"))) {
					$list[substr($ctl,0,-4)] = call_user_func(array(substr($ctl,0,-4),"name"));
				}
			}
		}
		return $list;
	}

	public static function guessControlType($ddcol, $default_value=null, $colname=null) {
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
                //$control = new calendarcontrol($default_value);
                return 'calendar';
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

}

?>