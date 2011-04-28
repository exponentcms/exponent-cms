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

$realbase = realpath(BASE) .'/modules/';
$file = realpath($realbase . $_REQUEST['m'] . '/actions/' . $_REQUEST['a'] . '.php');

if (exponent_permissions_check('administration',exponent_core_makeLocation('administrationmodule'))) {
	if( substr($file, 0, strlen($realbase) ) == $realbase ){
		if (is_readable($file)) {
			include($file);
		}
		else {
			echo SITE_404_HTML;
		}
	}
	else {
		echo SITE_403_HTML;
	}
}else echo SITE_403_HTML;

?>