<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
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

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','index.php');

/**
 * @param $buffer
 * @param $mode
 * @return string
 */
function epb($buffer, $mode) {
    //@ob_gzhandler($buffer, $mode);
    @ob_gzhandler($buffer);
    //return $buffer; // uncomment if you're messing with output buffering so errors show. ~pb
    return expProcessBuffer($buffer);
}

ob_start('epb');
$microtime_str = explode(' ',microtime());
$i_start = $microtime_str[0] + $microtime_str[1];

// Initialize the Exponent Framework
require_once('exponent.php');

// if the user has turned on sef_urls then we need to route the request, otherwise we can just 
// skip it and default back to the old way of doing things.
$router->routeRequest();

// initialize this users cart if they have ecomm installed.
// define whether or not ecom is enabled
if ($db->selectValue('modstate', 'active', 'module="storeController"')) {
    define("ECOM",1);
    $order = order::getUserCart();   
} else {
    define("ECOM",0);
}

if (isset($_GET['id']) && !is_numeric($_GET['id'])) $_GET['id'] = intval($_GET['id']);
$section = $router->getSection();
$sectionObj = $router->getSectionObj($section);
if (ENABLE_TRACKING) $router->updateHistory($section);

// set the output header
header("Content-Type: text/html; charset=".LANG_CHARSET);

// Check to see if we are in maintenance mode.
if (MAINTENANCE_MODE && !exponent_users_isAdmin() && ( !isset($_REQUEST['module']) || $_REQUEST['module'] != 'loginmodule')) {
	//only admins/acting_admins are allowed to get to the site, all others get the maintenance view
	$template = new standalonetemplate('_maintenance');
	$template->output();
} else {
	if (MAINTENANCE_MODE > 0) flash('error', "Maintenance Mode is Enabled");
	//the default user is anonymous
	if (!exponent_sessions_loggedIn()) {
		// Initialize the users subsystem
//		require_once(BASE.'framework/core/subsystems-1/users.php');  // FIXME users.php is already loaded from within exponent.php above
		//TODO: Maxims initial anonymous user implementation
		//exponent_users_login("anonymous", "anonymous");
	}

	// check to see if we need to install or upgrade the system
	expVersion::checkVersion();

	// Handle sub themes
	$page = exponent_theme_getTheme();

	// If we are in a printer friendly request then we need to change to our printer friendly subtheme
	if (PRINTER_FRIENDLY == 1) {
		exponent_sessions_set("uilevel",0);
		$pftheme = exponent_theme_getPrinterFriendlyTheme();  	// get the printer friendly theme 
		$page = $pftheme == null ? $page : $pftheme;		// if there was no theme found then just use the current subtheme
	}
 
	$base_i18n = exponent_lang_loadFile('index.php');

	if (is_readable($page)) {
		if (!exponent_javascript_inAjaxAction()) {
			include_once($page);
			exponent_theme_satisfyThemeRequirements();
		} else {
			exponent_theme_runAction();
		}
	} else {
		echo sprintf($base_i18n['not_readable'], $page);
	}

	if (PRINTER_FRIENDLY == 1) {
		//$levels = exponent_sessions_get('uilevels');
		//if (!empty($levels)) exponent_sessions_set('uilevel',max(array_keys($levels)));
		exponent_sessions_unset('uilevel');
	}
}

//$microtime_str = explode(' ',microtime());
//$i_end = $microtime_str[0] + $microtime_str[1];
//echo "\r\n<!--".sprintf($base_i18n['exec_time'],round($i_end - $i_start,4)).'-->';

ob_end_flush();

?>