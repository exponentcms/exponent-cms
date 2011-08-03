<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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
/** @define "BASE" "../.." */

function smarty_function_userlistcontrol($params,&$smarty) {
	echo '<script src="'.PATH_RELATIVE.'subsystems/forms/controls/listbuildercontrol.js" language="javascript"></script>';
//	if (!defined('SYS_FORMS')) require_once(BASE.'framework/core/subsystems-1/forms.php');
//	if (!defined('SYS_USERS')) require_once(BASE.'framework/core/subsystems-1/users.php');
	require_once(BASE.'framework/core/subsystems-1/forms.php');
	require_once(BASE.'framework/core/subsystems-1/users.php');
//    exponent_forms_initialize();

	global $db;
	$users = $db->selectObjects("user",null,"username");
	
	foreach ($users as $user) {
		$allusers[$user->id] = "$user->lastname, $user->firstname($user->username)";
	}
	
	$control = new listbuildercontrol(null, $allusers, 5);
	$name = isset($params['name']) ? $params['name'] : "userlist";
	echo $control->controlToHTML($name);
}

?>
