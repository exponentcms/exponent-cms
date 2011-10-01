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

class controllerTemplate extends baseTemplate {

	function __construct($controller, $viewfile) {
//		include_once(BASE.'external/Smarty-2/libs/Smarty.class.php');
		include_once(BASE.'external/Smarty-3/libs/Smarty.class.php');

		// Set up the Smarty template variable we wrap around.
		$this->tpl = new Smarty();
		$this->tpl->error_reporting = error_reporting() & ~E_NOTICE & ~E_WARNING;  //FIXME this disables bad template code reporting 3.x
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
