<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
//        global $head_config;

		include_once(SMARTY_PATH.'Smarty.class.php');

		// Set up the Smarty template variable we wrap around.
		$this->tpl = new Smarty();

        if (!SMARTY_DEVELOPMENT) $this->tpl->error_reporting = error_reporting() & ~E_NOTICE & ~E_WARNING;  //FIXME this disables bad template code reporting 3.x
        $this->tpl->debugging = SMARTY_DEVELOPMENT;  // Opens up the debug console
        $this->tpl->error_unassigned = true;  // display notice when accessing unassigned variable, if warnings turned on

		$this->tpl->php_handling = SMARTY::PHP_REMOVE;

        $this->tpl->setCaching(Smarty::CACHING_OFF);
//        $this->tpl->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
        $this->tpl->setCacheDir(BASE.'tmp/cache');
        $this->tpl->cache_id = md5($this->viewfile);

        // set up plugin search order based on framework
        if (bs3(true)) {
            $this->tpl->setPluginsDir(array(
                BASE . 'themes/' . DISPLAY_THEME . '/plugins',
                BASE . 'framework/plugins/bootstrap3',
                BASE . 'framework/plugins/bootstrap',
                BASE . 'framework/plugins/jquery',
                BASE . 'framework/plugins',
                SMARTY_PATH . 'plugins',
            ));
        } elseif (bs2()) {
            $this->tpl->setPluginsDir(array(
                BASE.'themes/'.DISPLAY_THEME.'/plugins',
                BASE.'framework/plugins/bootstrap',
                BASE.'framework/plugins/jquery',
                BASE.'framework/plugins',
                SMARTY_PATH.'plugins',
            ));
        } elseif (newui()) {
            $this->tpl->setPluginsDir(array(
                BASE.'themes/'.DISPLAY_THEME.'/plugins',
                BASE.'framework/plugins/newui',  // we leave out bootstrap3 & bootstrap chain on purpose
                BASE.'framework/plugins/jquery',
                BASE.'framework/plugins',
                SMARTY_PATH.'plugins',
            ));
        } elseif (framework() == 'jquery') {
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

		//autoload filters & compiler plugins
        $this->tpl->loadFilter('output', 'trim');  // trim whitespace from beginning and end of template output
        $this->tpl->loadPlugin('smarty_compiler_switch');  // adds {switch} function

		$this->viewfile = $viewfile;
		$this->viewdir = realpath(dirname($this->viewfile));

		$this->module = $controller->baseclassname;

        // strip file type
        if (substr($viewfile, -7) == '.config') {
            $this->file_is_a_config = true;
            $this->view = substr(basename($this->viewfile),0,-7);
        } else $this->view = substr(basename($this->viewfile),0,-4);

        $this->tpl->setTemplateDir($this->viewdir);

        $this->tpl->setCompileDir(BASE . 'tmp/views_c');
        $this->tpl->compile_id = framework() . '_' . md5($this->viewfile);

		$this->tpl->assign("__view", $this->view);
		$this->tpl->assign("__redirect", expHistory::getLastNotEditable());
		
		$this->tpl->assign("__loc",$controller->loc);
		$this->tpl->assign("__name", $controller->baseclassname);  //FIXME probably not used in 2.0?
        $this->tpl->assign("controller", $controller->baseclassname);
        if ($controller->baseclassname != 'common') {
            $this->tpl->assign("asset_path", $controller->asset_path);
            $this->tpl->assign("model_name", $controller->basemodel_name);
            $this->tpl->assign("model_table", $controller->model_table);
            $this->tpl->assign("config", $controller->config);
        }
	}

}

?>
