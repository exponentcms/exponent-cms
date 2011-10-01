<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006-2007 Maxim Mueller
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
/** @define "BASE" "../../.." */

/**
 * This is the class expTheme
 *
 * @subpackage Core-Subsystems
 * @package Framework
 */

class expTemplate {

	/*
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
			return BASE . 'framework/core/views/viewnotfound.tpl';
		}
		//return first match
		return array_shift($viewfilepath);
	}

	//DEPRECATED: backward compatibility wrapper
	public static function getModuleViewFile($name, $view, $recurse=true) {
		return self::getViewFile("modules", $name, $view);
	}

	// I think these still need to be i18n-ized
	public static function getViewConfigForm($module,$view,$form,$values) {
		$form_file = "";
		$resolved_path = null;
		$resolved_path = expCore::resolveFilePaths("modules", $module , "form" , $view);
		if (isset($resolved_path) && $resolved_path != '') {
			$filepath = array_shift(expCore::resolveFilePaths("modules", $module , "form" , $view));
		} else {
			$filepath = false;
		}

		if ($filepath != false) {
			$form_file = $filepath;
		}

	//	require_once(BASE."framework/core/subsystems-1/forms.php");
		if ($form == null) $form = new form();
		if ($form_file == "") return $form;

		$form->register(null,"",new htmlcontrol("<hr size='1' /><b>Layout Configuration</b>"));

		$fh = fopen($form_file,"r");
		while (($control_data = fgetcsv($fh,65536,"\t")) !== false) {
			$data = array();
			foreach ($control_data as $d) {
				if ($d != "") $data[] = $d;
			}
			if (!isset($values[$data[0]])) $values[$data[0]] = 0;
			if ($data[2] == "checkbox") {
				$form->register("_viewconfig[".$data[0]."]",$data[1],new checkboxcontrol($values[$data[0]],true));
			} else if ($data[2] == 'text') {
				$form->register("_viewconfig[".$data[0]."]",$data[1],new textcontrol($values[$data[0]]));
			} else {
				$options = array_slice($data,3);
				$form->register("_viewconfig[".$data[0]."]",$data[1],new dropdowncontrol($values[$data[0]],$options));
			}
		}

		$form->register("submit","",new buttongroupcontrol("Save","","Cancel"));

		return $form;
	}

	public static function getViewConfigOptions($module,$view) {
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

	    //Get the forms from the base form diretory
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

	/**
	 * Generates a list of email templates/forms
	 * @param $type
	 * @return array
	 */
	public static function listFormTemplates($type) {  //FIXME only used by calendarmodule edit action
		return expCore::buildNameList("forms", $type, "tpl", "[!_]*");
	}

	/* exdoc
	 *
	 * Looks through the module's views directory and returns
	 * all non-internal views that are found there.
	 * Returns an array of all standard view names.
	 * This array is unsorted.
	 *
	 * @param string $module The classname of the module to get views for.
	 * @param string $lang deprecated, was used to list language specific templates
	 * @node Subsystems:Template
	 */
	public static function listModuleViews($module, $lang = LANG) {  //FIXME only used by containermodule edit action and administrationmodule examplecontent action
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
}

?>
