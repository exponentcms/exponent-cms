<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

require_once('exponent_common.php');

// Initialize the AutoLoader Subsystem
require_once(BASE.'subsystems/autoloader.php');

// Initialize the Sessions Subsystem
require_once(BASE.'subsystems/sessions.php');
// Initializes the session.	 
exponent_sessions_initialize();

// initialize useful/needed constants throughout the system
require_once(BASE.'exponent_constants.php');

// Initialize the language subsystem
require_once(BASE.'subsystems/lang.php');
exponent_lang_initialize();
// Load 2.0 lang
expLang::loadLang();

// Initialize the Core Subsystem
require_once(BASE.'subsystems/core.php');

// Initialize the Database Subsystem
require_once(BASE.'subsystems/database.php');
$db = exponent_database_connect(DB_USER,DB_PASS,DB_HOST.':'.DB_PORT,DB_NAME);

// Initialize the Modules Subsystem.
require_once(BASE.'subsystems/modules.php');
exponent_modules_initialize();

// Initialize the Template Subsystem.
require_once(BASE.'subsystems/template.php');

// Initialize the Permissions Subsystem.
require_once(BASE.'subsystems/permissions.php');

// Initialize the Flow Subsystem.
if (!defined('SYS_FLOW')) require_once(BASE.'subsystems/flow.php');
$history = new expHistory(); //<--This is the new flow subsystem and will be replacing the above.

// Initialize the User Subsystem.
require_once(BASE.'subsystems/users.php');
// Initialize the javascript subsystem
if (!defined('SYS_JAVASCRIPT')) require_once(BASE.'subsystems/javascript.php');

// Initialize the new MVC framework
require_once(BASE.'framework/core/expFramework.php');
$available_controllers = intializeControllers();
if (exponent_javascript_inAjaxAction()) set_error_handler('handleErrors');

// Validate the session.  This will populate the $user variable
$user = new user();
exponent_sessions_validate();

// Initialize permissions variables
exponent_permissions_initialize();
// initialize the expRouter
$router = new expRouter();

// initialize this users cart if they have ecomm installed.
if (controllerExists('cart')) $order = order::getUserCart();

//Initialize the navigation heirarchy
$sections = exponent_core_initializeNavigation();

function eDebug($var, $halt=false){
	if (DEVELOPMENT) {
		echo "<xmp>";
		print_r($var);
		echo "</xmp>";
		
		if ($halt) die();
	}
}

function eLog($var, $type='', $path='', $minlevel='0') {
	if($type == '') { $type = "INFO"; }
	if($path == '') { $path = BASE . 'tmp/exponent.log'; }
	if (DEVELOPMENT >= $minlevel) {
		if (is_writable ($path) || !file_exists($path)) {
			if (!$log = fopen ($path, "ab")) {
				eDebug("Error opening log file for writing.");
			} else {
				if (fwrite ($log, $type . ": " . $var . "\r\n") === FALSE) {
					eDebug("Error writing to log file ($log).");
				}
				fclose ($log);
			}
		} else {
			eDebug ("Log file ($log) not writable.");
		}
	}
}
?>
