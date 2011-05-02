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

if (exponent_permissions_check('database',exponent_core_makeLocation('administrationmodule'))) {
	$exporters = array();
	$idh = opendir(BASE.'modules/exporter/exporters');
	while (($imp = readdir($idh)) !== false) {
		if (substr($imp,0,1) != '.' && is_readable(BASE.'modules/exporter/exporters/'.$imp.'/start.php') && is_readable(BASE.'modules/exporter/exporters/'.$imp.'/info.php')) {
			$exporters[$imp] = include(BASE.'modules/exporter/exporters/'.$imp.'/info.php');
		}
	}
	
	$template = new template('exporter','_exporters');
	$template->assign('exporters',$exporters);
	$template->output();
	
} else {
	echo SITE_403_HTML;
}

?>