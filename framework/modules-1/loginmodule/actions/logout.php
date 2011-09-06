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

//require_once(BASE.'framework/core/subsystems-1/users.php');
expSession::logout();
exponent_permissions_clear();
expSession::un_set('uilevel');
expSession::clearCurrentUserSessionCache();
flash('message', 'You have been logged out');
redirect_to(array("section"=>SITE_DEFAULT_SECTION));
//expHistory::back();

?>
