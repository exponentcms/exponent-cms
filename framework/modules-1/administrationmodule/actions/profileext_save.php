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
	$ext = null;
	if (isset($_GET['id'])) {
		$ext = $db->selectObject('profileextension','id='.intval($_GET['id']));
	}
	
	$ext->extension = $_GET['ext'];
	if (!isset($ext->id)) {
		// Get rank, append to end.
		$ext->rank = $db->max('profileextension','rank');
		if ($ext->rank === null) {
			$ext->rank = 0;
		} else {
			$ext->rank++;
		}
		$db->insertObject($ext,'profileextension');
	} else {
		$db->updateObject($ext,'profileextension');
	}
	
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>