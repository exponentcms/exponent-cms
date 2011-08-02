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
/** @define "BASE" ".." */

/* exdoc
 * The definition of this constant lets other parts of the system know 
 * that the subsystem has been included for use.
 * @node Subsystems:Modules
 */
//define('SYS_MODULES',1);

/* exdoc
 * This includes all modules available to the system, for use later.
 * @node Subsystems:Modules
 */
function exponent_modules_initialize() {
	if (is_readable(BASE.'modules')) {
		$dh = opendir(BASE.'modules');
		while (($file = readdir($dh)) !== false) {
			if (is_dir(BASE.'modules/'.$file) && is_readable(BASE.'modules/'.$file.'/class.php')) {
				include_once(BASE.'modules/'.$file.'/class.php');
			}
		}
	}
}

/* exdoc
 * Looks through the modules directory and returns a list of
 * all module class names that exist in the system.  No activity
 * state check is made, so inactive modules will also be listed.
 * Returns the list of module class names.
 * @node Subsystems:Modules
 */
function exponent_modules_list() {
	$mods = array();
	if (is_readable(BASE."modules")) {
		$dh = opendir(BASE."modules");
		while (($file = readdir($dh)) !== false) {
			if (substr($file,-6,6) == "module") $mods[] = $file;
		}
	}
	return $mods;
}

/* exdoc
 * Looks through the database returns a list of all module class
 * names that exist in the system and have been turned on by
 * the administrator.  Inactive modules will not be included.
 * Returns the list of active module class names.
 * @node Subsystems:Modules
 */
function exponent_modules_listActive() {
	global $db;
	$modulestates = $db->selectObjects("modstate","active='1'");
	$modules = array();
	foreach ($modulestates as $state) {
		if (class_exists($state->module)) $modules[] = $state->module;
	}
	return $modules;
}

/* exdoc
 * Looks through the current theme and standard js directories to find
 * the javascript form validation file for a given form in a module. Returns
 * he filename of the Javascript Validation script, or "" if one was not found.
 *
 * @param string $module The classname of the module.
 * @param string $formname The name of the form
 * @node Subsystems:Modules
 */
function exponent_modules_getJSValidationFile($module,$formname) {
	if (is_readable(BASE."themes/".DISPLAY_THEME."/modules/$module/js/$formname.validate.js")) return PATH_RELATIVE . "themes/".DISPLAY_THEME."/modules/$module/js/$formname.validate.js";
	else if (is_readable(BASE."modules/$module/js/$formname.validate.js")) return PATH_RELATIVE."modules/$module/js/$formname.validate.js";
	return "";
}

/* exdoc
 * Populate Template for module manager -- THIS NEEDS CHANGED
 * @node Subsystems:Modules
 */
function exponent_modules_moduleManagerFormTemplate($template) {
    //global $available_controllers;
    
    $controllers = listUserRunnableControllers();
	$modules = array_merge(exponent_modules_list(), $controllers);
	natsort($modules);
	
	global $db;
	$moduleInfo = array();
	foreach ($modules as $module) {
		if (class_exists($module)) {
			$mod = new $module();
			$modstate = $db->selectObject("modstate","module='$module'");
		
			$moduleInfo[$module] = null;
			$moduleInfo[$module]->class = $module;
			$moduleInfo[$module]->name = $mod->name();
			$moduleInfo[$module]->author = $mod->author();
			$moduleInfo[$module]->description = $mod->description();
			$moduleInfo[$module]->active = ($modstate != null ? $modstate->active : 0);
		}
	}
//	if (!defined('SYS_SORTING')) include_once(BASE.'framework/core/subsystems-1/sorting.php');
//	include_once(BASE.'framework/core/subsystems-1/sorting.php');
//	uasort($moduleInfo,"exponent_sorting_byNameAscending");
	$moduleInfo = expSorter::sort(array('array'=>$moduleInfo,'sortby'=>'name', 'order'=>'ASC', 'ignore_case'=>true, 'type'=>'a'));

	$template->assign("modules",$moduleInfo);
	return $template;
}

/* exdoc
 * This is used to verify that a module directory has all the required
 * directories and files.  Used mainly by the module upload feature to
 * ensure that the uploaded archive does in fact contain a module. Returns
 * true if the directory has valid module structure and false if it does not.
 *
 * @param string $basedir The absolute path to the module directory
 * @node Subsystems:Modules
 */
function exponent_modules_verifyModule($basedir) {
	// class.php
	if (!file_exists("$basedir/class.php") ||
		!is_file("$basedir/class.php") ||
		!is_readable("$basedir/class.php")	) return false;
	
	// actions
	if (file_exists("$basedir/actions") && (
		!is_dir("$basedir/actions") ||
		!is_readable("$basedir/actions"))) return false;
	
	// views
	if (file_exists("$basedir/views") && (
		!is_dir("$basedir/views") ||
		!is_readable("$basedir/views"))) return false;
	
	// views_c
	if (file_exists("$basedir/tmp/views_c") && (
		!is_dir("$basedir/tmp/views_c") ||
		!is_readable("$basedir/tmp/views_c"))) return false;
		
	return true;
}

/* exdoc
 * Checks to see if a module exists in the system.  No activity
 * check is made, so inactive modules still exist, according to this
 * method (no this is not a bug) Returns  true of the module exists, false if it was not found.
 * @node Subsystems:Modules
 */
function exponent_modules_moduleExists($name) {
	return (file_exists(BASE."modules/$name") && is_dir(BASE."modules/$name") && is_readable(BASE."modules/$name/class.php"));
}

function exponent_modules_getModuleInstancesByType($type=null) {
	if (empty($type)) return array();
        global $db;
        $refs = $db->selectObjects('sectionref', 'module="'.$type.'"');
        $modules = array();
        foreach ($refs as $ref) {
		    if ($ref->refcount > 0) {
                    	$instance = $db->selectObject('container', 'internal like "%'.$ref->source.'%"');
	                    $mod = null;
            	        $mod->title = !empty($instance->title) ? $instance->title : "Untitled";
                    	$mod->section = $db->selectvalue('section', 'name', 'id='.$ref->section);
	                    $modules[$ref->source][] = $mod;
		    }
        }

        return $modules;
}

?>
