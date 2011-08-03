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

if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
	exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);

//	if (!defined('SYS_USERS')) require_once(BASE.'framework/core/subsystems-1/users.php');
	require_once(BASE.'framework/core/subsystems-1/users.php');

	$template = new template('administrationmodule','_usermanager',$loc);
	
	$template = exponent_users_userManagerFormTemplate($template);
	$template->output();
} else {
	echo SITE_403_HTML;
}


?>