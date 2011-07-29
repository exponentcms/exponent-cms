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

//if (exponent_permissions_check('administrate',$loc)) {
if ($user->isAdmin()) {
	global $router;
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
				$u->$perm = 1;
			} else if (exponent_permissions_checkUser($u,$perm,$loc)) {
				$u->$perm = 2;
			} else {
				$u->$perm = 0;
			}
		}
		$users[] = $u;
	}
	
	$p["User Name"] = 'username';
	$p["First Name"] = 'firstname';
	$p["Last Name"] = 'lastname';
	foreach ($mod->permissions() as $key => $value) {
        $p[$value]=$key;
	}

	if (SEF_URLS == 1) {
		$page = new expPaginator(array(
		//'model'=>'user',
		'limit'=>(isset($_REQUEST['limit'])?$_REQUEST['limit']:20),
		'controller'=>$router->params['controller'],
		'action'=>$router->params['action'],
		'records'=>$users,
		//'sql'=>$sql,
		'order'=>'username',
		'dir'=>'DESC',
		'columns'=>$p,
		));
	} else {
		$page = new expPaginator(array(
		//'model'=>'user',
		'limit'=>(isset($_REQUEST['limit'])?$_REQUEST['limit']:20),
		'controller'=>$_GET['module'],
		'action'=>$_GET['action'],
		'records'=>$users,
		//'sql'=>$sql,
		'order'=>'username',
		'dir'=>'DESC',
		'columns'=>$p,
		));
	}
        
	
	$template->assign('have_users',$have_users);
	$template->assign('users',$users);
	$template->assign('page',$page);
	$template->assign('perms',$perms);
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
