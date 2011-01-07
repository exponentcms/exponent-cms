<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

// Part of Extensions category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('development',exponent_core_makeLocation('administrationmodule'))) {
	if (!defined('SYS_CONFIG')) include_once(BASE.'subsystems/config.php');
	$value = (DEVELOPMENT == 1) ? 0 : 1;
	exponent_config_change('DEVELOPMENT', $value);
	exponent_theme_remove_css();
	redirect_to(array('module'=>'administrationmodule', 'action'=>'index'));
} else {
	echo SITE_403_HTML;
}

?>
