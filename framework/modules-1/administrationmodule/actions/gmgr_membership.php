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

// Sanitize required _GET parameters
$_GET['id'] = intval($_GET['id']);

$memb = $db->selectObject('groupmembership','member_id='.$user->id.' AND group_id='.$_GET['id'].' AND is_admin=1');

$perm_level = 0;
if ($memb) $perm_level = 1;
if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) $perm_level = 2;

if ($perm_level) {
	$group = $db->selectObject('group','id='.$_GET['id']);
	if ($group != null) {
//		if (!defined('SYS_USERS')) require_once(BASE.'framework/core/subsystems-1/users.php');
		require_once(BASE.'framework/core/subsystems-1/users.php');
		$users = exponent_users_getAllUsers(0);
		
		$members = array();
		$admins = array();
		foreach ($db->selectObjects('groupmembership','group_id='.$group->id) as $m) {
			$members[] = $m->member_id;
			if ($m->is_admin == 1) {
				$admins[] = $m->member_id;
			}
		}
		
		for ($i = 0; $i < count($users); $i++) {
			if (in_array($users[$i]->id,$members)) {
				$users[$i]->is_member = 1;
			} else {
				$users[$i]->is_member = 0;
			}
			
			if (in_array($users[$i]->id,$admins)) {
				$users[$i]->is_admin = 1;
			} else {
				$users[$i]->is_admin = 0;
			}
		}
		
		$template = new template('administrationmodule','_groupmembership',$loc);
		$template->assign('group',$group);
		$template->assign('users',$users);
		$template->assign('canAdd',(count($members) < count($users) ? 1 : 0));
		$template->assign('hasMember',(count($members) > 0 ? 1 : 0));
		$template->assign('perm_level',$perm_level);
		$template->output();
	} else echo SITE_404_HTML;
} else {
	echo SITE_403_HTML;
}

?>