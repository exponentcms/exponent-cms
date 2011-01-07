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

if (!defined("EXPONENT")) exit("");

$container = null;
$iloc = null;
$cloc = null;
if (isset($_POST['id'])) $container = $db->selectObject("container","id=" . intval($_POST['id']));
if ($container != null) {
	$iloc = unserialize($container->internal);
	$loc = unserialize($container->external);
	$cloc = unserialize($container->external);
	$cloc->int = $container->id;
}



if (exponent_permissions_check("add_module",$loc) || 
	($iloc != null && exponent_permissions_check("administrate",$iloc)) ||
	($cloc != null && exponent_permissions_check("edit_module",$cloc))
	) {
	
	//eDebug($_POST,true);
	$container = container::update($_POST,$container,$loc);
	
	if (isset($container->id)) {
		$db->updateObject($container,"container");
	} else {
		$db->insertObject($container,"container");
	}
	
	if ($container->is_existing == 0) {
		$iloc = unserialize($container->internal);
		$locref = $db->selectObject("locationref","module='".$iloc->mod."' AND source='".$iloc->src."'");
		$locref->description = (isset($_POST['description'])?$_POST['description']:'');
		$db->updateObject($locref,"locationref","module='".$iloc->mod."' AND source='".$iloc->src."'");
	}
    define('SOURCE_SELECTOR',0);
    define('PREVIEW_READONLY',0); // for mods
	
    exponent_sessions_clearAllUsersSessionCache('containermodule');
	exponent_flow_redirect();
} else {
	echo SITE_403_HTML;
}

?>
