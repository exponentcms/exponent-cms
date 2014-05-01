<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 */

// Initialize the exponent environment
require_once('exponent_bootstrap.php');

// Initialize the MVC framework - for objects we need loaded now
require_once(BASE.'framework/core/expFramework.php');

// Initialize the Sessions subsystem
expSession::initialize();

// Initialize the Theme subsystem
expTheme::initialize();

// Initialize the language subsystem
expLang::initialize();

// Initialize the Database subsystem
$db = expDatabase::connect(DB_USER,DB_PASS,DB_HOST.':'.DB_PORT,DB_NAME);

// Initialize the Modules subsystem & Create the list of available/active controllers
$available_controllers = expModules::initializeControllers();
//foreach ($db->selectObjects('modstate',1) as $mod) {
//	if (!empty($mod->path)) $available_controllers[$mod->module] = $mod->path;  //FIXME test
//}

// Initialize the History (Flow) subsystem.
$history = new expHistory(); //<--This is the new flow subsystem

// Initialize the javascript subsystem
if (expJavascript::inAjaxAction()) set_error_handler('handleErrors');

// Validate the session and populate the $user variable
if ($db->havedb) {
	$user = new user();
	expSession::validate();
}

// The flag to use a mobile theme variation.
if (!defined('MOBILE')) {
	if (defined('FORCE_MOBILE') && FORCE_MOBILE && $user->isAdmin()) {
		define('MOBILE',true);
	} else {
		define('MOBILE',expTheme::is_mobile());
	}
}

// Initialize permissions variables
$exponent_permissions_r = expSession::get("permissions");

// initialize the expRouter
$router = new expRouter();

// Initialize the navigation hierarchy
if ($db->havedb)
	$sections = navigationController::initializeNavigation();

?>