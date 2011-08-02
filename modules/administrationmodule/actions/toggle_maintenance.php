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

// Part of Extensions category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('development',exponent_core_makeLocation('administrationmodule'))) {
//	if (!defined('SYS_CONFIG')) include_once(BASE.'framework/core/subsystems-1/config.php');
	include_once(BASE.'framework/core/subsystems-1/config.php');
	$value = (MAINTENANCE_MODE == 1) ? 0 : 1;
	exponent_config_change('MAINTENANCE_MODE', $value);
	redirect_to(array('module'=>'administrationmodule', 'action'=>'index'));
} else {
	echo SITE_403_HTML;
}

?>
