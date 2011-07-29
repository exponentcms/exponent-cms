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

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('administrate',$loc)) {

 	//$groups = explode(';',$_POST['permdata']);
    
	if (!defined('SYS_USERS')) include_once(BASE.'subsystems/users.php');

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
	$groups = exponent_users_getAllGroups();
	foreach ($locarray as $location) {
		foreach ($groups as $g) {
			exponent_permissions_revokeAllGroup($g,$location);
		}
	}
	
	foreach ($_POST['permdata'] as $k => $group_str) {
		$perms = array_keys($group_str);
		$g = exponent_users_getGroupById($k);

		foreach ($locarray as $location) {
			for ($i = 0; $i < count($perms); $i++) {
				exponent_permissions_grantGroup($g,$perms[$i],$location);
			}
		}
	}
	exponent_permissions_triggerRefresh();
	exponent_flow_redirect();
} else {
	echo SITE_403_HTML;
}

?>
