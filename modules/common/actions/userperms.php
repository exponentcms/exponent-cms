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
	if (exponent_template_getModuleViewFile($loc->mod,'_userpermissions',false) == TEMPLATE_FALLBACK_VIEW) {
		$template = new template('common','_userpermissions',$loc);
	} else {
		//TODO
		//ADK - I hard coded the common module name into the new template declaration since the path resolver 
		// can't seem to figure out that we are in the common module and not the module that call this action.

		//$template = new template($loc->mod,'_userpermissions',$loc);
		$template = new template('common','_userpermissions',$loc);
	}
	$template->assign('user_form',1);
	
	if (!defined('SYS_USERS')) include_once(BASE.'subsystems/users.php');
	$users = array();
	$modulename = controllerExists($loc->mod) ? getControllerClassName($loc->mod) : $loc->mod;
	$modclass = $modulename;
	$mod = new $modclass();
	$perms = $mod->permissions($loc->int);
	$have_users = 0;
	foreach (exponent_users_getAllUsers(0) as $u) {
		$have_users = 1;
		foreach ($perms as $perm=>$name) {
			$var = 'perms_'.$perm;
			if (exponent_permissions_checkUser($u,$perm,$loc,true)) {
				$u->$var = 1;
			} else if (exponent_permissions_checkUser($u,$perm,$loc)) {
				$u->$var = 2;
			} else {
				$u->$var = 0;
			}
		}
		$users[] = $u;
	}
	$template->assign('have_users',$have_users);
	$template->assign('users',$users);
	$template->assign('perms',$perms);
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
