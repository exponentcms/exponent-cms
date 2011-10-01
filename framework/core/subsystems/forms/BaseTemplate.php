<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006-2007 Maxim Mueller
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
/** @define "BASE" "../../.." */

class BaseTemplate {
	// Smarty template object.
	var $tpl;
	
	// This is the directory of the particular module, used to identify the moduel
	var $module = "";
	
	// The full server-side filename of the .tpl file being used.
	// This will be used by modules on the outside, for retrieving view configs.
	var $viewfile = "";
	
	// Name of the view (for instance, 'Default' for 'Default.tpl')
	var $view = "";
	
	// Full server-side directory path of the .tpl file being used.
	var $viewdir = "";
	
	//fix for the wamp/lamp issue
	var $langdir = "";
	//	
	
	function __construct($item_type, $item_dir, $view = "Default") {
		
//		include_once(BASE.'external/Smarty-2/libs/Smarty.class.php');
		include_once(BASE.'external/Smarty-3/libs/Smarty.class.php');

		// Set up the Smarty template variable we wrap around.
		$this->tpl = new Smarty();
		$this->tpl->error_reporting = error_reporting() & ~E_NOTICE & ~E_WARNING;  //FIXME to disable bad template code reporting 3.x
		//Some (crappy) wysiwyg editors use php as their default initializer
		//FJD - this might break some editors...we'll see.
//		$this->tpl->php_handling = SMARTY_PHP_REMOVE;
		$this->tpl->php_handling = SMARTY::PHP_REMOVE;

		$this->tpl->caching = false;
		$this->tpl->cache_dir = BASE . 'tmp/cache';

		//$this->tpl->plugins_dir[] = BASE . 'framework/core/subsystems-1/template/Smarty/plugins';
//		$this->tpl->plugins_dir[] = BASE . 'framework/plugins';
		// now reverse the array so we can bypass looking in our root folder for old plugins
//		$this->tpl->plugins_dir = array_reverse($this->tpl->plugins_dir);
		$this->tpl->setPluginsDir(array(BASE.'external/Smarty-3/libs/plugins',BASE . 'framework/plugins'));

		//autoload filters
		$this->tpl->autoload_filters = array('post' => array('includemiscfiles'));
		
		$this->viewfile = expTemplate::getViewFile($item_type, $item_dir, $view);
		$this->viewdir = realpath(dirname($this->viewfile));

		$this->module = $item_dir;

		$this->view = substr(basename($this->viewfile),0,-4);
		
		//fix for the wamp/lamp issue
		//checks necessary in case a file from /views/ is used
		//should go away, the stuff should be put into a CoreModule
		//then this can be simplified
		//TODO: generate this through $this->viewfile using find BASE/THEME_ABSOLUTE and replace with ""
		if($item_type != "") {
			$this->langdir .= $item_type . "/";
		}
		if($item_dir != "") {
			$this->langdir .= $item_dir . "/";
		}
		$this->langdir .= "views/";
		
		
		$this->tpl->template_dir = $this->viewdir;
		
		$this->tpl->compile_dir = BASE . 'tmp/views_c';
		$this->tpl->compile_id = md5($this->viewfile);
		
		$this->tpl->assign("__view", $this->view);
		$this->tpl->assign("__redirect", expHistory::getLastNotEditable());
	}
	
	/*
	 * Assign a variable to the template.
	 *
	 * @param string $var The name of the variable - how it will be referenced inside the Smarty code
	 * @param mixed $val The value of the variable.
	 */
	function assign($var, $val) {
		$this->tpl->assign($var, $val);
	}
	
	/*
	 * Render the template and echo it to the screen.
	 */
	function output() {
		// javascript registration
		
		$this->tpl->display($this->view.'.tpl');
	}
	
	function register_permissions($perms, $locs) {
		$permissions_register = array();
		if (!is_array($perms)) $perms = array($perms);
		if (!is_array($locs)) $locs = array($locs);
		foreach ($perms as $perm) {
			foreach ($locs as $loc) {
				$permissions_register[$perm] = (expPermissions::check($perm, $loc) ? 1 : 0);
			}
		}
		$this->tpl->assign('permissions', $permissions_register);
	}
	
	/*
	 * Render the template and return the result to the caller.
	 */
	function render() { // Caching support?
		return $this->tpl->fetch($this->view.'.tpl');
	}
}

?>
