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

class administrationmodule {
	function name() { return 'Administration Control Panel'; }
	function author() { return 'OIC Group, Inc'; }
	function description() { return 'A control panel that gives administrators easy access to administrative tasks.'; }
	
	function hasContent() { return false; }
	function hasSources() { return false; }
	function hasViews()   { return true;  }
	
	function supportsWorkflow() { return false; }
	function dontShowInModManager() { return true; }
	
	function permissions($internal = "") {
		$permissions = array('administrate'=>gt('Administrate'));
		
		$menu = array();
		$dir = BASE.'framework/modules-1/administrationmodule/tasks';
		if (is_readable($dir)) {
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				if (substr($file,-4,4) == '.php' && is_readable($dir.'/'.$file) && is_file($dir.'/'.$file)) {
					$menu = array_merge($menu,include($dir.'/'.$file));
				}
			}
		}
		
		foreach (array_keys($menu) as $header) {
			$permissions[strtolower(str_replace(' ','_',$header))] = $header;
		}
		return $permissions;
	}
	
	function deleteIn($loc) {
		// Do nothing, no content
	}
	
	function copyContent($from_loc,$to_loc) {
		// Do nothing, no content
	}
	
	
	function spiderContent($item = null) {
		// Do nothing, no content
		return false;
	}
	
	function show($view,$loc = null,$title = "") {
		global $user;
		$menu = array();
		$dir = BASE.'framework/modules-1/administrationmodule/tasks';
		if (is_readable($dir)) {
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
				if (substr($file,-4,4) == '.php' && is_readable($dir.'/'.$file) && is_file($dir.'/'.$file)) {
					$menu = array_merge($menu,include($dir.'/'.$file));
				}
			}
		}
		$template = new template('administrationmodule',$view,$loc);
		
		$level = 99;
		if (expSession::is_set('uilevel')) {
			$level = expSession::get('uilevel');
		}
		$template->assign('can_manage_nav', exponent_permissions_checkOnModule("manage","navigationmodule"));
		$template->assign('editMode',expSession::loggedIn() && $level != UILEVEL_PREVIEW);
		$template->assign('title',$title);
		$template->assign('previewMode',($level == UILEVEL_PREVIEW));
		
		$template->assign('menu',$menu);
		$template->assign('moduletitle',$title);
		$template->assign('user',$user);
		
		$perms = administrationmodule::permissions();
		$template->assign('check_permissions',array_flip($perms));
		$template->register_permissions(array_keys($perms),exponent_core_makeLocation('administrationmodule'));
		
		$template->output($view);
	}
}

?>
