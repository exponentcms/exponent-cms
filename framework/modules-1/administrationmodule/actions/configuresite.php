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
	expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);
	
	$configname = (isset($_GET['configname']) ? $_GET['configname'] : "");
	
//	require_once(BASE.'framework/core/subsystems-1/config.php');
	require_once(BASE.'framework/core/subsystems-1/forms.php');

	$profiles = expSettings::profiles();
	if (count($profiles) == 0) $profiles = array(''=>'[No Profiles]');
	if (!array_key_exists($configname,$profiles) || $configname == '') {
		if (defined('CURRENTCONFIGNAME')) $configname = CURRENTCONFIGNAME;
		else {
			$keys = array_keys($profiles);
			$configname = $keys[1];
		}
	}
	if (!array_key_exists($configname,$profiles)) $configname = "";
	uasort($profiles,'strnatcmp');
	
	$template = new template('administrationmodule','_configuresiteview',$loc);
	
	$form = new form();
	
	$dd = new dropdowncontrol($configname,$profiles);
	$href = preg_replace("/&configname.*/",'',$_SERVER['REQUEST_URI']);
	$dd->jsHooks['onchange'] = "document.location.href = eXp.makeLink('module', 'administrationmodule', 'action', 'configuresite', 'configname', this.options[this.selectedIndex].value);";
	$form->register('configname',gt('Profile'),$dd);
	$template->assign('form_html',$form->toHTML());
	
	$template = expSettings::outputConfigurationTemplate($template,$configname);
	$template->assign('configname',$configname);
	
	$canactivate = ($configname != '' && is_readable(BASE."conf/profiles/$configname.php"));
	$candelete = ($configname != '' && expUtil::isReallyWritable(BASE.'conf/profiles'));
	$canedit = (($configname == '' && (expUtil::isReallyWritable(BASE.'conf/config.php'))) || expUtil::isReallyWritable(BASE.'conf/profiles/'));
	
	$template->assign('canactivate',$canactivate);
	$template->assign('canedit',$canedit);
	$template->assign('candelete',$candelete);
	
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
