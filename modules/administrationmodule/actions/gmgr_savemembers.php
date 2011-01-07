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

// Part of the User Management category

if (!defined('EXPONENT')) exit('');

// Sanitize required _GET parameters
$_GET['id'] = intval($_GET['id']);

$memb = $db->selectObject('groupmembership','member_id='.$user->id.' AND group_id='.$_GET['id'].' AND is_admin=1');

if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule')) || $memb) {
	$group = $db->selectObject('group','id='.intval($_POST['id']));
	if ($group) {
		$db->delete('groupmembership','group_id='.$group->id);
		$memb = null;
		$memb->group_id = $group->id;
		if ($_POST['membdata'] != "") {
			foreach (explode(',',$_POST['membdata']) as $str) {
				$str = explode(':',$str);
				$memb->member_id = $str[0];
				$memb->is_admin = $str[1];
				$db->insertObject($memb,'groupmembership');
			}
		}
		exponent_permissions_triggerRefresh();
		exponent_flow_redirect();
	} else echo SITE_404_HTML;
} else {
	echo SITE_403_HTML;
}

?>