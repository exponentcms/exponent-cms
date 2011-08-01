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

// Part of the User Management category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
//	if (!defined('SYS_USERS')) require_once(BASE.'subsystems/users.php');
//	if (!defined('SYS_FORMS')) require_once(BASE.'subsystems/forms.php');
	require_once(BASE.'subsystems/users.php');
	require_once(BASE.'subsystems/forms.php');
//	exponent_forms_initialize();

	$g = exponent_users_getGroupById(intval($_GET['id']));
	$form = exponent_users_groupForm($g);
	$form->meta('module','administrationmodule');
	$form->meta('action','gmgr_savegroup');
	
	$template = new template('administrationmodule','_gmgr_editprofile',$loc);
	$template->assign('form_html',$form->toHTML());
	$template->assign('is_edit',($g != null));
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>