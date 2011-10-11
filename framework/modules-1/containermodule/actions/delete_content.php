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

$iloc = expCore::makeLocation($_GET['m'],@$_GET['s'],@$_GET['i']);

// Make sure that locref refcount is indeed 0.
//$locref = $db->selectObject("locationref","module='".$iloc->mod."' AND source='".$iloc->src."' AND internal='".$iloc->int."'");
//if ($locref && $locref->refcount == 0 && expPermissions::check("administrate",$iloc)) {
$secref = $db->selectObject("sectionref","module='".$iloc->mod."' AND source='".$iloc->src."' AND internal='".$iloc->int."'");
if ($secref && $secref->refcount == 0 && expPermissions::check("administrate",$iloc)) {
	// delete in location.
	$modclass = $iloc->mod;
    expSession::clearAllUsersSessionCache('containermodule');
    expSession::clearAllUsersSessionCache($iloc);
	
	//FIXME: more module/controller glue code
	if (expModules::controllerExists($modclass)) {
	    $mod = new $modclass($iloc->src);
	    $mod->delete_instance();
	} else {
	    $mod = new $modclass();
	    $mod->deleteIn($iloc);	    
	}
	
//	$db->delete("locationref","module='".$iloc->mod."' AND source='".$iloc->src."' AND internal='".$iloc->int."'");
	$db->delete("sectionref","module='".$iloc->mod."' AND source='".$iloc->src."' AND internal='".$iloc->int."'");
	expPermissions::revokeComplete($iloc);
}

expHistory::back();

?>
