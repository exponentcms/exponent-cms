<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

if (expPermissions::check('manage',$loc)) {

	$locarray = array();
//    if ($loc->mod == 'navigationController' && (isset($_POST['permdata'][2]['manage']) && $_POST['permdata'][2]['manage'] || isset($_POST['permdata'][2]['manage']) && $_POST['permdata'][2]['manage'])) {
//		$sections = navigationController::levelTemplate($loc->int);
//		$locarray[] = $loc;
//		foreach ($sections as $section) {
//			$locarray[] = expCore::makeLocation('navigationController', null, $section->id);
//		}
//	} else {
		$locarray[] = $loc;
//	}
	$users = user::getAllUsers();
	foreach ($locarray as $location) {
		foreach ($users as $u) {
			expPermissions::revokeAll($u,$location);
		}
	}	
	
	foreach ($_POST['permdata'] as $k => $user_str) {
		$perms = array_keys($user_str);
		$u = user::getUserById($k);

		foreach ($locarray as $location) {
			for ($i = 0; $i < count($perms); $i++) {
				expPermissions::grant($u,$perms[$i],$location);
			}
		}

		if ($k == $user->id) {
			expPermissions::load($user);
		}
	}
	expPermissions::triggerRefresh();
    expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>
