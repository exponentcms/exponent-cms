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

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_check('configure',$loc)) {
	$config = $db->selectObject($_POST['module'].'_config',"location_data='".serialize($loc)."'");
	$config = call_user_func(array($_POST['module'].'_config','update'),$_POST,$config);
	$config->location_data = serialize($loc);
	
	if (isset($config->id)) {
		$db->updateObject($config,$_POST['module'].'_config');
	} else {
		$db->insertObject($config,$_POST['module'].'_config');
	}
	
	$container = $db->selectObject('container',"internal='".serialize($loc)."'");
	$vconfig = array();
	if (isset($_POST['_viewconfig'])) {
		$opts = exponent_template_getViewConfigOptions($loc->mod,$container->view);
		foreach (array_keys($opts) as $o) {
			$vconfig[$o] = (isset($_POST['_viewconfig'][$o]) ? $_POST['_viewconfig'][$o] : 0);
		}
	}
	$container->view_data = serialize($vconfig);
	$db->updateObject($container,'container');
	exponent_flow_redirect();
} else {
	echo SITE_403_HTML;
}

?>