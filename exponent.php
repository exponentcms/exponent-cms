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

function __realpath($path) {
	$path = str_replace('\\','/',realpath($path));
	if ($path{1} == ':') {
		// We can't just check for C:/, because windows users may have the IIS webroot on X: or F:, etc.
		$path = substr($path,2);
	}
	return $path;
}

// Bootstrap, which will clean the _POST, _GET and _REQUEST arrays, and include 
// necessary setup files (exponent_setup.php, exponent_variables.php) as well as initialize
// the compatibility layer.
// This was moved into its own file from this file so that 'lighter' scripts could bootstrap.
include_once(dirname(__realpath(__FILE__)).'/exponent_bootstrap.php');

// Initialize the AutoLoader Subsystem
require_once(BASE.'subsystems/autoloader.php');

// Initialize the Sessions Subsystem
require_once(BASE.'subsystems/sessions.php');
// Initializes the session.	 
exponent_sessions_initialize();

/*
if (isset($_REQUEST['section'])) {
	exponent_sessions_set('last_section', intval($_REQUEST['section']));
} else {
	if (!isset($_REQUEST['action']) && !isset($_REQUEST['module'])) exponent_sessions_set('last_section', SITE_DEFAULT_SECTION);
}
*/

if (!defined('DISPLAY_THEME')) {
	/* exdoc
	 * The directory and class name of the current active theme.  This may be different
	 * than the configure theme (DISPLAY_THEME_REAL) due to previewing.
	 */
	define('DISPLAY_THEME',DISPLAY_THEME_REAL);
}
if (!defined('THEME_ABSOLUTE')) {
	/* exdoc
	 * The absolute path to the current active theme's files.  This is similar to the BASE constant
	 */
	define('THEME_ABSOLUTE',BASE.'themes/'.DISPLAY_THEME.'/'); // This is the recommended way
}
if (!defined('THEME_RELATIVE')) {
	/* exdoc
	 * The relative web path to the current active theme.  This is similar to the PATH_RELATIVE consant.
	 */
	define('THEME_RELATIVE',PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/');
}
if (!defined('JS_FULL')) {
	/* exdoc
	 * The absolute path to Exponent's core javascript.
	 */
	define('JS_FULL',URL_FULL.'framework/core/assets/js/');
}

// Initialize the theme subsystem
if (!defined('SYS_THEME')) require_once(BASE.'subsystems/theme.php');

// iconset base
if (!defined('ICON_RELATIVE')) {
	
	define('ICON_RELATIVE', PATH_RELATIVE . 'framework/core/assets/images/');

}
if (!defined('MIMEICON_RELATIVE')) {
	//DEPRECATED: old directory, inconsitent naming
    // if (is_readable(THEME_ABSOLUTE . 'mimetypes/')) {
		/* exdoc
		 * The relative web path to the current MIME icon set.	If a mimetypes/ directory
		 * exists directly underneath the theme's directory, then that is used.	 Otherwise, the
		 * system falls back to the iconset/mimetypes/ directory in the root of the Exponent directory.
		 */
    //  define('MIMEICON_RELATIVE', THEME_RELATIVE . 'mimetypes/');
    // } else if(is_readable(THEME_ABSOLUTE . "images/icons/mimetypes" )){
    //  define('MIMEICON_RELATIVE', THEME_RELATIVE . "images/icons/mimetypes/");
    // } else {
    //  define('MIMEICON_RELATIVE', PATH_RELATIVE . 'themes/common/images/icons/mimetypes/');
    // }
    define('MIMEICON_RELATIVE', PATH_RELATIVE . 'themes/common/skin/mimetypes/');
}

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
