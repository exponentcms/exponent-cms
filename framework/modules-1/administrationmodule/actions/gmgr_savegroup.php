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

// Part of the User Management category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
	require_once(BASE.'framework/core/subsystems-1/users.php');
	if (isset($_POST['id'])) { // Existing user profile edit
		$g = exponent_users_getGroupById($_POST['id']);
		$g = exponent_users_groupUpdate($_POST,$g);
		exponent_users_saveGroup($g);
		
		expHistory::back();
	} else {
		if (exponent_users_getGroupByName($_POST['name']) != null) {
			$post = $_POST;
			$post['_formError'] = gt('The group name name is already taken.');
			expSession::set('last_POST',$post);
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		} else {
			$g = exponent_users_groupUpdate($_POST,null);
			exponent_users_saveGroup($g);
			expHistory::back();
		}
	}
} else {
	echo SITE_403_HTML;
}

?>