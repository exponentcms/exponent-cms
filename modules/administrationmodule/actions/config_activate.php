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

// Part of the Configuration category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('configuration',exponent_core_makeLocation('administrationmodule'))) {
	if (!defined('SYS_CONFIG')) require_once(BASE.'subsystems/config.php');
	exponent_config_activateProfile($_GET['configname']);
	exponent_flow_redirect();
} else {
	echo SITE_403_HTML;
}

?>