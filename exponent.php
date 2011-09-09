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
/** @define "BASE" "." */

/**
 * The file that initializes everything
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */

// Initialize the exponent environment
require_once('exponent_bootstrap.php');

// Initialize the MVC framework - for objects we need loaded now
require_once(BASE.'framework/core/expFramework.php');

// Initialize the Sessions subsystem
expSession::initialize();

// Initialize the Theme subsystem
expTheme::initialize();

// Create the list of available/active controllers
$available_controllers = initializeControllers();  //original position
//$available_controllers = array();

// Initialize the language subsystem
expLang::loadLang();

// Initialize the Database subsystem
require_once(BASE.'framework/core/subsystems-1/database.php');
$db = exponent_database_connect(DB_USER,DB_PASS,DB_HOST.':'.DB_PORT,DB_NAME);
//$available_controllers = initializeControllers();
//foreach ($db->selectObjects('modstate',1) as $mod) {
//	if (!empty($mod->path)) $available_controllers[$mod->module] = $mod->path;  //FIXME test location
//}

// Initialize the old school Modules subsystem.
require_once(BASE.'framework/core/subsystems-1/modules.php');
exponent_modules_initialize(); // now in the autoloader, if used

// Initialize the Template subsystem.
require_once(BASE.'framework/core/subsystems-1/template.php');

// Initialize the History (Flow) subsystem.
$history = new expHistory(); //<--This is the new flow subsystem and will be replacing the above.
$SYS_FLOW_REDIRECTIONPATH = 'exponent_default';

// Initialize the javascript subsystem
if (expJavascript::inAjaxAction()) set_error_handler('handleErrors');

// Validate the session and populate the $user variable
$user = new user();
expSession::validate();

// Initialize permissions variables
$exponent_permissions_r = expSession::get("permissions");

// initialize the expRouter
$router = new expRouter();

// Initialize the navigation hierarchy
$sections = expCore::initializeNavigation();

/**
 * dumps the passed variable to screen, but only if in development mode
 * @param  $var the variable to dump
 * @param bool $halt if set to true will halt execution
 * @return void
 */
function eDebug($var, $halt=false){
	if (DEVELOPMENT) {
		echo "<xmp>";
		print_r($var);
		echo "</xmp>";
		
		if ($halt) die();
	}
}

/**
 * dumps the passed variable to a log, but only if in development mode
 * @param  $var the variable to log
 * @param string $type the type of entry to record
 * @param string $path the pathname for the log file
 * @param string $minlevel
 * @return void
 */
function eLog($var, $type='', $path='', $minlevel='0') {
	if($type == '') { $type = "INFO"; }
	if($path == '') { $path = BASE . 'tmp/exponent.log'; }
	if (DEVELOPMENT >= $minlevel) {
		if (is_writable ($path) || !file_exists($path)) {
			if (!$log = fopen ($path, "ab")) {
				eDebug("Error opening log file for writing.");
			} else {
				if (fwrite ($log, $type . ": " . $var . "\r\n") === FALSE) {
					eDebug("Error writing to log file ($path).");
				}
				fclose ($log);
			}
		} else {
			eDebug ("Log file ($path) not writable.");
		}
	}
}

?>
