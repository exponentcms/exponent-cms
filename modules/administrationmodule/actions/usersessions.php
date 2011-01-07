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

if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
	exponent_flow_set(SYS_FLOW_PROTECTED, SYS_FLOW_ACTION);

	//cleans up any old sections
	$db->delete('sessionticket','last_active < ' . (time() - SESSION_TIMEOUT));
	
	if (!defined('SYS_USERS')) require_once(BASE.'subsystems/users.php');
	if (!defined('SYS_DATETIME')) require_once(BASE.'subsystems/datetime.php');
	
	$sessions = $db->selectObjects('sessionticket');
	for ($i = 0; $i < count($sessions); $i++) {
		$sessions[$i]->user = exponent_users_getUserById($sessions[$i]->uid);
		$sessions[$i]->duration = exponent_datetime_duration($sessions[$i]->last_active,$sessions[$i]->start_time);
	}
	
	$template = new template('administrationmodule','_sessionmanager',$loc);
	$template->assign('sessions',$sessions);
	$template->assign('user',$user);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>