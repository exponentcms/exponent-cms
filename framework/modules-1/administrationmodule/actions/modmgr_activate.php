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

// Part of the Extensions category

if (!defined('EXPONENT')) exit('');

$_GET['activate'] = intval($_GET['activate']);

if (exponent_permissions_check('extensions',exponent_core_makeLocation('administrationmodule'))) {
	if (isset($_GET['all'])) {
		$db->delete('modstate');
		$modstate->active = $_GET['activate'];
		require_once(BASE.'framework/core/subsystems-1/modules.php');
		foreach (expModules::exponent_modules_list() as $mod) {
			$modstate->module = $mod;
			$db->insertObject($modstate,'modstate');
		}
	} else {
		$modstate = $db->selectObject('modstate',"module='".preg_replace('/[^A-Za-z0-9_ ]/','',$_GET['mod'])."'");
		if ($modstate == null) {
			$modstate->active = $_GET['activate'];
			$modstate->module = $_GET['mod'];
			$db->insertObject($modstate,'modstate');
		} else {
			$modstate->active = $_GET['activate'];
			$db->updateObject($modstate,'modstate',"module='".$_GET['mod']."'");
		}
	}
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>