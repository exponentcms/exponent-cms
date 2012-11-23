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
        if (!SMARTY_DEVELOPMENT) $this->tpl->error_reporting = error_reporting() & ~E_NOTICE & ~E_WARNING;  //FIXME this disables bad template code reporting 3.x
        $this->tpl->debugging = SMARTY_DEVELOPMENT;  // Opens up the debug console
        $this->tpl->error_unassigned = true;  // display notice when accessing unassigned variable, if warnings turned on

		$this->tpl->php_handling = SMARTY::PHP_REMOVE;

//		$this->tpl->caching = false;
        $this->tpl->setCaching(Smarty::CACHING_OFF);
//        $this->tpl->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
//		$this->tpl->cache_dir = BASE.'tmp/cache';
        $this->tpl->setCacheDir(BASE.'tmp/cache');
        $this->tpl->cache_id = md5($this->viewfile);

        //FIXME we should add a check for new jQuery type plugins near the beginning of chain if used
        if (defined('JQUERY_THEME')) {
            $this->tpl->setPluginsDir(array(
               BASE.'themes/'.DISPLAY_THEME.'/plugins',
               BASE.'framework/plugins/jquery',
               BASE.'framework/plugins',
               SMARTY_PATH.'plugins',
            ));
        } else {
            $this->tpl->setPluginsDir(array(
               BASE.'themes/'.DISPLAY_THEME.'/plugins',
               BASE.'framework/plugins',
               SMARTY_PATH.'plugins',
            ));
        }

		//autoload filters
//		$this->tpl->autoload_filters = array('post' => array('includemiscfiles'));
        $this->tpl->loadPlugin('smarty_compiler_switch');

		$this->viewfile = $viewfile;
		$this->viewdir = realpath(dirname($this->viewfile));

		$this->module = $controller->baseclassname;
				
		$this->view = substr(basename($this->viewfile),0,-4);

//		$this->tpl->template_dir = $this->viewdir;
        $this->tpl->setTemplateDir($this->viewdir);

//		$this->tpl->compile_dir = BASE . 'tmp/views_c';
        $this->tpl->setCompileDir(BASE . 'tmp/views_c');
		$this->tpl->compile_id = md5($this->viewfile);
		
		$this->tpl->assign("__view", $this->view);
		$this->tpl->assign("__redirect", expHistory::getLastNotEditable());
		
		$this->tpl->assign("__loc",$controller->loc);
		$this->tpl->assign("__name", $controller->baseclassname);
	}

}

?>
