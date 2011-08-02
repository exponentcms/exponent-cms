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

// Part of the User Management categry

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
	exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);

	$template = new template('administrationmodule','_groupmanager',$loc);
//	if (!defined('SYS_USERS')) include_once(BASE.'framework/core/subsystems-1/users.php');
	include_once(BASE.'framework/core/subsystems-1/users.php');
	$groups = exponent_users_getAllGroups();
	$template->assign('groups',$groups);
	$template->assign('perm_level',2); // So we get the edit/delete links
	$template->output();
} else {
	echo SITE_403_HTML;
}


?>