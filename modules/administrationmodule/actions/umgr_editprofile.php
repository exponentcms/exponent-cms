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

// Part of the User Management category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
	if (!defined('SYS_USERS')) require_once(BASE.'subsystems/users.php');
	if (!defined('SYS_FORMS')) require_once(BASE.'subsystems/forms.php');
	exponent_forms_initialize();

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
		// Super user editting a 'lesser' user.
		$i18n = exponent_lang_loadFile('modules/administrationmodule/actions/umgr_editprofile.php');
		$form->registerBefore('submit','is_acting_admin',$i18n['is_admin'],new checkboxcontrol($u->is_acting_admin,true));
	}
	
	$template = new template('administrationmodule','_umgr_editprofile',$loc);
	$template->assign('form_html',$form->toHTML());
	$template->assign('is_edit',isset($u->id)?1:0);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>