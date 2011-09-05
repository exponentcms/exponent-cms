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

expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);

if ($user) {
	$template = new template('administrationmodule','_groupmanager',$loc);
//	require_once(BASE.'framework/core/subsystems-1/users.php');
	$groups = array();
	foreach ($db->selectObjects('groupmembership','member_id='.$user->id.' AND is_admin=1') as $memb) {
		$groups[] = $db->selectObject('group','id='.$memb->group_id);
	}
	$template->assign('groups',$groups);
	$template->assign('perm_level',1); // So we don't get the edit/delete links.
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>