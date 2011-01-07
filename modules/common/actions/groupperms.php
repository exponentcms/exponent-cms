<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('administrate',$loc)) {
	$i18n = exponent_lang_loadFile('subsystems/users.php');
	if (exponent_template_getModuleViewFile($loc->mod,'_grouppermissions',false) == TEMPLATE_FALLBACK_VIEW) {
		$template = new template('common','_grouppermissions',$loc);
	} else {
		$template = new template('common','_grouppermissions',$loc);
		//$template = new template($loc->mod,'_grouppermissions',$loc);
	}
	$template->assign('user_form',0);

	if (!defined('SYS_GROUPS')) include_once(BASE.'subsystems/users.php');

	$users = array(); // users = groups
    $modulename = controllerExists($loc->mod) ? getControllerClassName($loc->mod) : $loc->mod;    
    //$modclass = $loc->mod;
	$modclass = $modulename;
	$mod = new $modclass();
	$perms = $mod->permissions($loc->int);
	// Create the anonymous group
	/*
	$g = null;
	$g->id = 0;
	$g->name = $i18n['anonymous_group_name'];
	$g->description = $i18n['anonymous_group_description'];
	$g->inclusive = 1;
	//$g->code = "";
	foreach ($perms as $perm=>$name) {
		$var = "perms_$perm";
		if (exponent_permissions_checkGroup($g,$perm,$loc,true)) $g->$var = 1;
		else if (exponent_permissions_checkGroup($g,$perm,$loc)) $g->$var = 2;
		else $g->$var = 0;
	}
	$users[] = $g;
	*/
	foreach (exponent_users_getAllGroups() as $g) {
		foreach ($perms as $perm=>$name) {
			$var = 'perms_'.$perm;
			if (exponent_permissions_checkGroup($g,$perm,$loc,true)) {
				$g->$var = 1;
			} else if (exponent_permissions_checkGroup($g,$perm,$loc)) {
				$g->$var = 2;
			} else {
				$g->$var = 0;
			}
		}
		$users[] = $g;
	}
	$template->assign('have_users',count($users) > 0); // users = groups
	$template->assign('users',$users); // users = groups
	$template->assign('perms',$perms);

	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
