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
 * Controller Template Wrapper
 *
 * @package Subsystems-Forms
 * @subpackage Template
 */
class controllertemplate extends basetemplate {

	function __construct($controller, $viewfile) {
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
		
		$this->viewfile = $viewfile;
		$this->viewdir = realpath(dirname($this->viewfile));

		$this->module = $controller->baseclassname;
				
		$this->view = substr(basename($this->viewfile),0,-4);
		
		//fix for the wamp/lamp issue
		//checks necessary in case a file from /views/ is used
		//should go away, the stuff should be put into a CoreModule
		//then this can be simplified
		//TODO: generate this through $this->viewfile using find BASE/THEME_ABSOLUTE and replace with ""
		
//		$this->langdir .= 'framework/'.$controller->relative_viewpath . "/";
		
		$this->tpl->template_dir = $this->viewdir;
		
		$this->tpl->compile_dir = BASE . 'tmp/views_c';
		$this->tpl->compile_id = md5($this->viewfile);
		
		$this->tpl->assign("__view", $this->view);
		$this->tpl->assign("__redirect", expHistory::getLastNotEditable());
		
		$this->tpl->assign("__loc",$controller->loc);
		$this->tpl->assign("__name", $controller->baseclassname);
	}

}

?>
