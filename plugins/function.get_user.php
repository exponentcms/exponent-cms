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

function smarty_function_get_user($params,&$smarty) {
	if (!defined("SYS_SESSIONS")) include_once(BASE."subsystems/sessions.php");
	if (exponent_sessions_loggedIn()) {	
		global $user;
		if (isset($params['assign'])) $smarty->assign($params['assign'],$user);
		else echo $user->username;
	}
}

?>
