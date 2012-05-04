<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

/**
 * Base Template class
 *
 * @package Subsystems-Forms
 * @subpackage Template
 */
abstract class basetemplate {
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
//	var $langdir = "";
	//	
	
	function __construct($item_type, $item_dir, $view = "Default") {
		include_once(SMARTY_PATH.'Smarty.class.php');

		// Set up the Smarty template variable we wrap around.
		$this->tpl = new Smarty();
		$this->tpl->error_reporting = error_reporting() & ~E_NOTICE & ~E_WARNING;  //FIXME this disables bad template code reporting 3.x
        $this->tpl->error_unassigned = true;  // display notice when accessing unassigned variable, if warnings turned on
//		$this->tpl->debugging = DEVELOPMENT;  // Opens up the debug console

		//Some (crappy) wysiwyg editors use php as their default initializer
		//FJD - this might break some editors...we'll see.
		$this->tpl->php_handling = SMARTY::PHP_REMOVE;

		$this->tpl->caching = false;
		$this->tpl->cache_dir = BASE.'tmp/cache';

		$this->tpl->setPluginsDir(array(SMARTY_PATH.'plugins',BASE.'framework/plugins'));

		//autoload filters
//		$this->tpl->autoload_filters = array('post' => array('includemiscfiles'));
		
		$this->viewfile = expTemplate::getViewFile($item_type, $item_dir, $view);
		$this->viewdir = realpath(dirname($this->viewfile));

		$this->module = $item_dir;

		$this->view = substr(basename($this->viewfile),0,-4);
		
		$this->tpl->template_dir = $this->viewdir;
		
		$this->tpl->compile_dir = BASE . 'tmp/views_c';
		$this->tpl->compile_id = md5($this->viewfile);
		
		$this->tpl->assign("__view", $this->view);
		$this->tpl->assign("__redirect", expHistory::getLastNotEditable());
	}
	
	/**
	 * Assign a variable to the template.
	 *
	 * @param string $var The name of the variable - how it will be referenced inside the Smarty code
	 * @param mixed $val The value of the variable.
	 */
	function assign($var, $val) {
		$this->tpl->assign($var, $val);
	}
	
	/**
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
	
	/**
	 * Render the template and return the result to the caller.
	 * @return bool|mixed|string
	 */
	function render() { // Caching support?
		return $this->tpl->fetch($this->view.'.tpl');
	}

}

?>
