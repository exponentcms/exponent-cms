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
/** @define "BASE" "../../../.." */

// Part of the Configuration category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('configuration',exponent_core_makeLocation('administrationmodule'))) {
//	require_once(BASE.'framework/core/subsystems-1/config.php');

	$continue = true;
	if ($user->is_admin == 1) { // Only do the database stuff if we are a super admin
		$errors = '';
		
		// Test the prefix
		if (preg_match("/[^A-Za-z0-9]/",$_POST['c']['DB_TABLE_PREFIX'])) {
			$continue = false;
			$errors .= gt('Invalid table prefix.  The table prefix can only contain alphanumeric characters.');
		}
		
		// Test the database connection
		$newdb = exponent_database_connect($_POST['c']['DB_USER'],$_POST['c']['DB_PASS'],$_POST['c']['DB_HOST'].":".$_POST['c']['DB_PORT'],$_POST['c']['DB_NAME'],$_POST['c']['DB_ENGINE']);
		$newdb->prefix = $_POST['c']['DB_TABLE_PREFIX'] . '_';
		
		if (!$newdb->isValid()) {
			$continue = false;
			$errors .= gt('Unable to connect to database server.  Make sure that the database specified exists, and the user account specified has access to the server.');
		}
		
		if ($continue) {
			$status = $newdb->testPrivileges();
			foreach ($status as $type=>$flag) {
				if (!$flag) {
					$continue = false;
					$errors .= sprintf(gt('Unable to run %s commands.'),$type);
				}
			}
		}
	}
	
	$template = new template('administrationmodule','_config_results');
	
	if ($continue) {
		expSettings::saveConfiguration($_POST);
		$ob = "";
		if ($user->is_admin == 1) {
			$db = $newdb;
			ob_start();
			include_once(BASE . 'framework/modules-1/administrationmodule/actions/installtables.php');
			$ob = ob_get_contents();
			ob_end_clean();
			if ($db->tableIsEmpty('user')) {
				$user = null;
				$user->username = 'admin';
				$user->password = md5('admin');
				$user->is_admin = 1;
				$user->is_acting_admin = 1;
				$db->insertObject($user,'user');
			}
			
			if ($db->tableIsEmpty('modstate')) {
				$modstate = null;
				$modstate->module = 'administrationmodule';
				$modstate->active = 1;
				$db->insertObject($modstate,'modstate');
			}
			
			if ($db->tableIsEmpty('section')) {
				$section = null;
				$section->name = gt('Home');
				$section->public = 1;
				$section->active = 1;
				$section->rank = 0;
				$section->parent = 0;
				$sid = $db->insertObject($section,'section');
			}
		}
		$template->assign('success',1);
	} else {
		$template->assign('success',0);
		$template->assign('errors',$errors);
	}

	//blow away the css files incase the theme was changed.
	expTheme::removeCss();
	//delete the slingbar session
	expSession::deleteVar("slingbar_top");
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
