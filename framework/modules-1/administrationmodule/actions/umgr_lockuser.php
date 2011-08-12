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

if (isset($_GET['id']) && exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
//	if (!defined('SYS_USERS')) require_once(BASE.'framework/core/subsystems-1/users.php');
	require_once(BASE.'framework/core/subsystems-1/users.php');
	$u = exponent_users_getUserById(intval($_GET['id']));
	if ($u && $u->is_admin == 0 && ($u->is_acting_admin == 0 || $user->is_admin == 1)) {
		$u->is_locked = $_GET['value'];
		exponent_users_saveUser($u);
	}
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>