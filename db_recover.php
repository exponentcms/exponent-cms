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

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','db_recover.php');

// Initialize the Exponent Framework
include_once(dirname(realpath(__FILE__)).'/exponent.php');

$i18n = exponent_lang_loadFile('db_recover.php');

exit($i18n['disabled']);

// If we made it here, the user has enabled the Database Recovery Script manually.

// Save the old user data, in case current user is actually logged in.
$oldu = $user;
// Temproarily elevate the current user to admin status, to 
// allow them to install tables.
$user->is_admin = 1;
$user->is_acting_admin = 1;

// The $loc variable would normally be created by the Exponent framework
// when running the action we are about to include.  Here, we synthetically
// create the location, so that the action doesn't freak out.
$loc = exponent_core_makeLocation('administrationmodule');

// Simulate running the Install Tables action.
include_once(dirname(__realpath(__FILE__)).'/modules/administrationmodule/actions/installtables.php');

// In case something is screwed up in the database, we need to 
// create some records.

// Create the default administrative account (username:admin, password:admin)
// if there are no users in the user table.
if ($db->tableIsEmpty('user')) {
	echo $i18n['create_admin'].'<br />';
	$user = null;
	$user->username = 'admin';
	$user->password = md5('admin');
	$user->is_admin = 1;
	// This wont work for other users subsystems
	$db->insertObject($user,'user');
}

// If no modules have been activated, we will not be able to activate any modules
// through the Administration Control Panel, usually because we won't
// be able to add the inactive Admin Control Panel to get access to the module
// manager.  Activate at least the Administrative Module if there are no modules.
if ($db->tableIsEmpty('modstate')) {
	echo $i18n['activate_panel'].'<br />';
	$modstate = null;
	$modstate->module = 'administrationmodule';
	$modstate->active = 1;
	$db->insertObject($modstate,'modstate');
}

// If therer are no sections in the database, we should create a default Home section
// so that the user at least has a starting page.
if ($db->tableIsEmpty('section')) {
	echo $i18n['create_section'].'<br />';
	$section = null;
	$section->name = $i18n['home'];
	$section->public = 1;
	$section->active = 1;
	$section->rank = 0;
	$section->parent = 0;
	$sid = $db->insertObject($section,'section');
}
//GREP:VIEWIFY
?>
