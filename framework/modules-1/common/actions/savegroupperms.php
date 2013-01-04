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

 	//$groups = explode(';',$_POST['permdata']);

	$locarray = array();
//	if ($loc->mod == 'navigationController' && (isset($_POST['permdata'][1]['manage']) && $_POST['permdata'][1]['manage'] || isset($_POST['permdata'][1]['manage']) && $_POST['permdata'][1]['manage'])) {
//		$sections = navigationController::levelTemplate($loc->int);
//		$locarray[] = $loc;
//		foreach ($sections as $section) {
//			$locarray[] = expCore::makeLocation('navigationController', null, $section->id);
//		}
//	} else {
		$locarray[] = $loc;
//	}
	$groups = group::getAllGroups();
	foreach ($locarray as $location) {
		foreach ($groups as $g) {
			expPermissions::revokeAllGroup($g,$location);
		}
	}
	
	foreach ($_POST['permdata'] as $k => $group_str) {
		$perms = array_keys($group_str);
		$g = group::getGroupById($k);

		foreach ($locarray as $location) {
			for ($i = 0; $i < count($perms); $i++) {
				expPermissions::grantGroup($g,$perms[$i],$location);
			}
		}
	}
	expPermissions::triggerRefresh();
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>
