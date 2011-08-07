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

// Part of the Configuration category

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('configuration',exponent_core_makeLocation('administrationmodule'))) {
	//ob_end_clean();
	
	header('Content-type: application/octet-stream');
	header('Content-Disposition: inline; filename="exponent.phpinfo.html"');
	
	$template = new template('administrationmodule','_sysinfo',$loc);
	
	ob_start();
	phpinfo(INFO_GENERAL+INFO_CONFIGURATION+INFO_MODULES);
	$str = ob_get_contents();
	$str = str_replace(array('<html>','<body>','</body>','</html>'),'',$str);
	ob_end_clean();
	
	$template->assign('phpinfo',$str);
	
//	if (!defined('SYS_MODULES')) require_once(BASE.'framework/core/subsystems-1/modules.php');
//	if (!defined('SYS_INFO')) require_once(BASE.'framework/core/subsystems-1/info.php');
	require_once(BASE.'framework/core/subsystems-1/modules.php');
	require_once(BASE.'framework/core/subsystems-1/info.php');

	$mods = array();
	
	foreach (expModules::exponent_modules_list() as $m) {
		if (class_exists($m)) {
			$mobj = new $m();
			$mods[$m] = array(
				'name'=>$mobj->name(),
				'author'=>$mobj->author(),
				'description'=>$mobj->description(),
			);
		}
	}
	
	$template->assign('modules',$mods);
	$template->assign('subsystems',exponent_info_subsystems());
	
	$template->assign('override_style',0);
	
	$template->output();
	
	exit(''); // Exit, since we are exporting.
} else {
	echo SITE_403_HTML;
}

?>