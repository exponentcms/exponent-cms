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

if (isset($_GET['id']) && exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
	if (!defined('SYS_USERS')) require_once(BASE.'subsystems/users.php');
	
	$u = exponent_users_getUserById(intval($_GET['id']));
	
	$template = new template('administrationmodule','_umgr_edit_which',$loc);
	$template->assign('user',$u);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>