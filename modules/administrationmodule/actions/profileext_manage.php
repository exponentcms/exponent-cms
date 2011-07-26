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
	if (!defined('SYS_USERS')) require_once(BASE.'subsystems/users.php');
	exponent_users_includeProfileExtensions();
	
	exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);
	
	$template = new template('administrationmodule','_profileextManager',$loc);
	
	exponent_users_clearDeletedExtensions(); // This will clear db of deleted exts.
	
	$exts = $db->selectObjects('profileextension');
	
	if (!defined('SYS_SORTING')) require_once(BASE.'subsystems/sorting.php');
	usort($exts,'exponent_sorting_byRankAscending');
	
	for ($i = 0; $i < count($exts); $i++) {
		$exts[$i]->name = call_user_func(array($exts[$i]->extension,'name'));
		$exts[$i]->author = call_user_func(array($exts[$i]->extension,'author'));
		$exts[$i]->description = call_user_func(array($exts[$i]->extension,'description'));
	}
	
	$unused = exponent_users_listUnusedExtensions();
	foreach ($unused as $i) {
		$unused[$i] = null;
		$unused[$i]->name = call_user_func(array($i,'name'));
		$unused[$i]->author = call_user_func(array($i,'author'));
		$unused[$i]->description = call_user_func(array($i,'description'));
		$unused[$i]->hasData = call_user_func(array($i,'hasData'));
	}
	
	$template->assign('extensions',$exts);
	$template->assign('unused',$unused);
	$template->assign('haveMore',count($unused) == 0 ? 0 : 1);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
