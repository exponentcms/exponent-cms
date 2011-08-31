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

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('administrate',$loc)) {
	include_once(BASE.'framework/core/subsystems-1/users.php');

	$locarray = array();
	if ($loc->mod == 'navigationmodule' && !empty($perms[1]) && $perms[1] == 'manage') {
		$sections = navigationmodule::levelTemplate($loc->int);
		$locarray[] = $loc;
		foreach ($sections as $section) {
			$locarray[] = exponent_core_makeLocation('navigationmodule', null, $section->id);
		}
	} else {
		$locarray[] = $loc;
	}
	$users = user::getAllUsers();
	foreach ($locarray as $location) {
		foreach ($users as $u) {
			exponent_permissions_revokeAll($u,$location);
		}
	}	
	
	foreach ($_POST['permdata'] as $k => $user_str) {
		$perms = array_keys($user_str);
		$u = exponent_users_getUserById($k);

		foreach ($locarray as $location) {
			for ($i = 0; $i < count($perms); $i++) {
				exponent_permissions_grant($u,$perms[$i],$location);
			}
		}

		if ($k == $user->id) {
			exponent_permissions_load($user);
		}
	}
	exponent_permissions_triggerRefresh();
    expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>
