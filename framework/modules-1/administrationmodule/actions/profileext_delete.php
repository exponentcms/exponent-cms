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

// Part of the User Management System

if (!defined('EXPONENT')) exit('');

if (isset($_GET['id']) && exponent_permissions_check('user_management',exponent_core_makeLocation('administrationmodule'))) {
	$db->delete('profileextension','id='.intval($_GET['id']));
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>