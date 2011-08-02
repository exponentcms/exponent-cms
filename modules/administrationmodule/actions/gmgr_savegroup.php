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
/** @define "BASE" "../../.." */

// Part of the User Management category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
//	if (!defined('SYS_USERS')) require_once(BASE.'subsystems/users.php');
	require_once(BASE.'subsystems/users.php');
	if (isset($_POST['id'])) { // Existing user profile edit
		$g = exponent_users_getGroupById($_POST['id']);
		$g = exponent_users_groupUpdate($_POST,$g);
		exponent_users_saveGroup($g);
		
		exponent_flow_redirect();
	} else {
		if (exponent_users_getGroupByName($_POST['name']) != null) {
			$i18n = exponent_lang_loadFile('modules/administrationmodule/actions/gmgr_savegroup.php');
			$post = $_POST;
			$post['_formError'] = $i18n['name_taken'];
			exponent_sessions_set('last_POST',$post);
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		} else {
			$g = exponent_users_groupUpdate($_POST,null);
			exponent_users_saveGroup($g);
			exponent_flow_redirect();
		}
	}
} else {
	echo SITE_403_HTML;
}

?>