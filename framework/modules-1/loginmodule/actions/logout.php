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

if (!defined('EXPONENT')) exit('');

//if (!defined('SYS_USERS')) require_once(BASE.'framework/core/subsystems-1/users.php');
require_once(BASE.'framework/core/subsystems-1/users.php');
exponent_users_logout();
exponent_permissions_clear();
exponent_sessions_unset('uilevel');
exponent_sessions_clearCurrentUserSessionCache();
flash('message', 'You have been logged out');
redirect_to(array("section"=>SITE_DEFAULT_SECTION));
//exponent_flow_redirect();

?>
