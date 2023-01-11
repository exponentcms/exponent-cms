<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
#[AllowDynamicProperties]
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

	function __construct($item_type, $item_dir, $view = "Default")
    {
//        global $head_config;

        include_once(SMARTY_PATH . 'Smarty.class.php');

        // Set up the Smarty template variable we wrap around.
        $this->tpl = new Smarty();

        if (!SMARTY_DEVELOPMENT)
            $this->tpl->error_reporting = error_reporting() & ~E_NOTICE & ~E_WARNING; //FIXME this disables bad template code reporting 3.x
        $this->tpl->debugging = SMARTY_DEVELOPMENT; // Opens up the debug console
        $this->tpl->error_unassigned = true; // display notice when accessing unassigned variable, if warnings turned on

        if (version_compare(SMARTY_VERSION, '4.0.0', 'lt')) {
            $this->tpl->php_handling = SMARTY::PHP_REMOVE;  //fixme remove for smarty v4
        }

        if (SMARTY_CACHING)
            $this->tpl->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
        else
            $this->tpl->setCaching(Smarty::CACHING_OFF);
        $this->tpl->setCacheDir(BASE . 'tmp/cache');
        $this->tpl->cache_id = md5($this->viewfile);

        // set up plugin search order based on framework
        if (bs5()) {
            $this->tpl->setPluginsDir(array(
                BASE . 'themes/' . DISPLAY_THEME . '/plugins',
                BASE . 'framework/plugins/bootstrap5',
                BASE . 'framework/plugins/bootstrap4',
                BASE . 'framework/plugins/bootstrap3',
                BASE . 'framework/plugins/bootstrap',
                BASE . 'framework/plugins/jquery',
                BASE . 'framework/plugins',
                SMARTY_PATH . 'plugins',
            ));
        } elseif (bs4()) {
            $this->tpl->setPluginsDir(array(
                BASE . 'themes/' . DISPLAY_THEME . '/plugins',
                BASE . 'framework/plugins/bootstrap4',
                BASE . 'framework/plugins/bootstrap3',
                BASE . 'framework/plugins/bootstrap',
                BASE . 'framework/plugins/jquery',
                BASE . 'framework/plugins',
                SMARTY_PATH . 'plugins',
            ));
        } elseif (bs3(true)) {
            $this->tpl->setPluginsDir(array(
                BASE.'themes/'.DISPLAY_THEME.'/plugins',
                BASE.'framework/plugins/bootstrap3',
                BASE.'framework/plugins/bootstrap',
                BASE.'framework/plugins/jquery',
                BASE.'framework/plugins',
                SMARTY_PATH.'plugins',
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
                BASE.'framework/plugins/newui',  // we leave out bootstrap5, bootstrap4, bootstrap3 & bootstrap on purpose
                BASE.'framework/plugins/jquery',
                BASE.'framework/plugins',
                SMARTY_PATH.'plugins',
            ));
        } elseif (framework() === 'jquery') {
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

		$this->viewfile = expTemplate::getViewFile($item_type, $item_dir, $view);  //FIXME only place we call this method
        if ($this->viewfile == TEMPLATE_FALLBACK_VIEW) {
            $this->tpl->assign("badview", $view);
        }
		$this->viewdir = realpath(dirname($this->viewfile));

		$this->module = $item_dir;

        // strip file type
		$this->view = substr(basename($this->viewfile),0,-4);

        $this->tpl->setTemplateDir($this->viewdir);

        $this->tpl->setCompileDir(BASE . 'tmp/views_c');
        $this->tpl->compile_id = framework() . '_' . md5($this->viewfile);

		$this->tpl->assign("__view", $this->view);
		$this->tpl->assign("__redirect", expHistory::getLastNotEditable());
	}

    /**
     * Generic magic method
     *
     * @param $property
     * @return null
     */
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return null;
    }

    /**
     *  Generic magic method
     *  We MUST create/set non-existing properties for Exponent code to work
     *
     * @param $property
     * @param $value
     */
    public function __set($property, $value) {
//        if (property_exists($this, $property)) {
            $this->$property = $value;
//        }
    }

    /**
     * Generic magic method
     *
     * @param $property
     * @return bool
     */
    public function  __isset($property) {
        return isset($this->$property);
    }

    /**
     * Generic magic method
     *
     * @param $property
     */
    public function __unset($property) {
        unset($this->$property);
    }

	/**
	 * Assign a variable to the template.
	 *
	 * @param string|array $var The name of the variable - how it will be referenced inside the Smarty code
	 * @param mixed $val The value of the variable.
	 */
	function assign($var, $val=null) {
		$this->tpl->assign($var, $val);
	}

	/**
	 * Render the template and echo it to the screen.
	 */
	function output() {
		// javascript registration
        if (empty($this->file_is_a_config)) {
            try {
                $this->tpl->display($this->view.'.tpl');
            } catch(SmartyException $e) {
                echo "Smarty reported: ". $e->getMessage();
//              exit;
            }
        }
	}

	function register_permissions($perms, $locs) {
		$permissions_register = array();
		if (!is_array($perms)) $perms = array($perms);
		if (!is_array($locs)) $locs = array($locs);
		foreach ($perms as $perm) {
			foreach ($locs as $loc) {
                $ploc = expCore::makeLocation(expModules::getModuleName($loc->mod),$loc->src,$loc->int);
				$permissions_register[$perm] = (expPermissions::check($perm, $ploc) ? 1 : 0);
			}
		}
		$this->tpl->assign('permissions', $permissions_register);
	}

    /**
     * Render the template and return the result to the caller.
     * @return bool|mixed|string
     * @throws Exception
     */
	function render() { // Caching support?
        try {
            if (empty($this->file_is_a_config)) {
                return $this->tpl->fetch($this->view.'.tpl');
            } else {
                return $this->tpl->fetch($this->view.'.config');
            }
        } catch(SmartyException $e) {
            return "Smarty reported: " . $e->getMessage();
        }
	}

}

?>
