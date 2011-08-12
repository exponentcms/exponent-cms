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

if (!defined('EXPONENT')) exit('');

// Part of the Database category

if (exponent_permissions_check('database',exponent_core_makeLocation('administrationmodule'))) {
	$src = urldecode($_GET['delsrc']);
	
	$mod = new $_GET['mod']();
	if ($mod->hasContent()) { // may not need the check, but it doesn't hurt
		$mod->deleteIn(exponent_core_makeLocation($_GET['mod'],$_GET['delsrc']));
	}
	
//	$db->delete('locationref',"module='" . $_GET['mod'] . "' AND source='$src' AND refcount=0");
	$db->delete('sectionref',"module='" . $_GET['mod'] . "' AND source='$src' AND refcount=0");
	
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>