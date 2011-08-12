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
#if ($user && $user->is_acting_admin == 1) {
	require_once(BASE.'framework/core/subsystems-1/users.php');
	if (isset($_POST['id'])) { // Existing user profile edit
		$_POST['id'] = intval($_POST['id']);
		$u = exponent_users_getUserById(intval($_POST['id']));
		$u = exponent_users_update($_POST,$u);
		//save extensions
		exponent_users_saveProfileExtensions($_POST,$u,false);
		exponent_users_saveUser($u);
		expHistory::back();
	} else {
		$_POST['username'] = trim($_POST['username']);
		if (user::getUserByName($_POST['username']) != null) {
			unset($_POST['username']);
	                expValidator::failAndReturnToForm(gt('That username is already taken.'), $_POST);
		} else if ($_POST['pass1'] != $_POST['pass2']) {
			unset($_POST['pass1']);
	                unset($_POST['pass2']);
	                expValidator::failAndReturnToForm(gt('Passwords do not match.'), $_POST);
		} else {
			$username_error = expValidator::checkUsername($_POST['username']);
			$strength_error = expValidator::checkPasswordStrength($_POST['username'],$_POST['pass1']);
			
			if ($username_error != ''){
				unset($_POST['username']);
		                expValidator::failAndReturnToForm(sprintf(gt('Your username has errors : %s'),$username_error), $_POST);
			}else if ($strength_error != '') {
				unset($_POST['pass1']);
	                        unset($_POST['pass2']);
        	                expValidator::failAndReturnToForm(sprintf(gt('Your password is not strong enough : %s'),$strength_error), $_POST);
			} else {
				$u = exponent_users_create($_POST,null);
				$u = exponent_users_saveProfileExtensions($_POST,$u,true);
				expHistory::back();
			}
		}
	}
} else {
	echo SITE_403_HTML;
}

?>
