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

require_once('exponent_bootstrap.php');

// Initialize the AutoLoader subsystem - for objects we want loaded on the fly
require_once(BASE.'framework/core/subsystems-1/autoloader.php');

// Initialize the MVC framework - for objects we need loaded now
require_once(BASE.'framework/core/expFramework.php');
/**
 * the list of available/active controllers
 * @global array $available_controllers
 * @name $available_controllers
 */
$available_controllers = intializeControllers();

// Initialize the Sessions Subsystem
expSession::initialize();

// Initialize the theme subsystem 1.0 compatibility layer
require_once(BASE.'framework/core/subsystems-1/theme.php');
$validateTheme = array("headerinfo"=>false,"footerinfo"=>false);

// Initialize the language subsystem
require_once(BASE.'framework/core/subsystems-1/lang.php');
exponent_lang_initialize();
// Load 2.0 lang
expLang::loadLang();

// Initialize the Core Subsystem
require_once(BASE.'framework/core/subsystems-1/core.php');

// Initialize the Database Subsystem
require_once(BASE.'framework/core/subsystems-1/database.php');
/**
 * The exponent database object
 *
 * @global mysqli_database $db the exponent database object
 * @name $db
 */
$db = exponent_database_connect(DB_USER,DB_PASS,DB_HOST.':'.DB_PORT,DB_NAME);

// Initialize the old school Modules Subsystem.
require_once(BASE.'framework/core/subsystems-1/modules.php');
exponent_modules_initialize();

// Initialize the Template Subsystem.
require_once(BASE.'framework/core/subsystems-1/template.php');

// Initialize the Permissions Subsystem.
require_once(BASE.'framework/core/subsystems-1/permissions.php');

// Initialize the Flow/History Subsystem.
//if (!defined('SYS_FLOW')) require_once(BASE.'framework/core/subsystems-1/flow.php');
require_once(BASE.'framework/core/subsystems-1/flow.php');
/**
 * the browsing history object
 * @global expHistory $history
 * @name $history
 */
$history = new expHistory(); //<--This is the new flow subsystem and will be replacing the above.

// Initialize the User Subsystem.
require_once(BASE.'framework/core/subsystems-1/users.php');

// Initialize the javascript subsystem
//if (!defined('SYS_JAVASCRIPT')) require_once(BASE.'framework/core/subsystems-1/javascript.php');
require_once(BASE.'framework/core/subsystems-1/javascript.php');

// Initialize the MVC framework
//require_once(BASE.'framework/core/expFramework.php');
//$available_controllers = intializeControllers();

if (expJavascript::inAjaxAction()) set_error_handler('handleErrors');

// Validate the session.  This will populate the $user variable
/**
 * the current user object
 * @global user $user
 * @name $user
 */
$user = new user();
expSession::validate();

// Initialize permissions variables
exponent_permissions_initialize();

/**
 * initialize the expRouter
 * the routing/link/url object
 * @global expRouter $router
 * @name $router
 */
$router = new expRouter();

/**
 * Initialize the navigation hierarchy
 * the list of sections/pages for the site
 * @global array $sections
 * @name $sections
 */
$sections = exponent_core_initializeNavigation();

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