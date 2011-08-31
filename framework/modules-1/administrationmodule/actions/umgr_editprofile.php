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
	require_once(BASE.'framework/core/subsystems-1/users.php');
	require_once(BASE.'framework/core/subsystems-1/forms.php');

	$u = exponent_users_getUserById(intval($_GET['id']));
	if ($u == null) {
		$u->is_admin = 0;
		$u->is_acting_admin = 0;
	}
	$u = exponent_users_getFullProfile($u);
	$form = exponent_users_form($u);
	$form->meta('module','administrationmodule');
	$form->meta('action','umgr_saveuser');
	
	if ($user->is_admin == 1 && $u->is_admin == 0) {
		// Super user editing a 'lesser' user.
		$form->registerBefore('submit','is_acting_admin',gt('Administrator?'),new checkboxcontrol($u->is_acting_admin,true));
	}
	
	$template = new template('administrationmodule','_umgr_editprofile',$loc);
	$template->assign('form_html',$form->toHTML());
	$template->assign('is_edit',isset($u->id)?1:0);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>