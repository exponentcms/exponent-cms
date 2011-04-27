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
	global $router;
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

	foreach (exponent_users_getAllGroups() as $g) {
		foreach ($perms as $perm=>$name) {
			$var = 'perms_'.$perm;
			if (exponent_permissions_checkGroup($g,$perm,$loc,true)) {
				$g->$perm = 1;
			} else if (exponent_permissions_checkGroup($g,$perm,$loc)) {
				$g->$perm = 2;
			} else {
				$g->$perm = 0;
			}
		}
		$users[] = $g;
	}
	
	$p["Group"] = 'username';
	foreach ($mod->permissions() as $key => $value) {
        $p[$value]=$key;
	}
	
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
        
	
	$template->assign('is_group',1);
	$template->assign('have_users',count($users) > 0); // users = groups
	$template->assign('users',$users);
	$template->assign('page',$page);
	$template->assign('perms',$perms);

	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
