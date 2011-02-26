<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Copyright 2006-2007 Maxim Mueller
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

/**
 * HTML Editor Control
 *
 * @author James Hunt
 * @copyright 2004-2006 OIC Group, Inc.
 * @version 0.95
 *
 * @package Subsystems
 * @subpackage Forms
 */

/**
 * Manually include the class file for formcontrol, for PHP4
 * (This does not adversely affect PHP5)
 */
require_once(BASE."subsystems/forms/controls/formcontrol.php");

/**
 * HTML Editor Control
 *
 * @package Subsystems
 * @subpackage Forms
 */
if (SITE_WYSIWYG_EDITOR == "ckeditor") {

class htmleditorcontrol extends ckeditorcontrol {
}

} else {
    
class htmleditorcontrol extends formcontrol {
	var $module = "";
	var $toolbar = "";
	
	
	function name() {
		return "WYSIWYG Editor";
	}
	
	
	function htmleditorcontrol($default="",$module = "",$rows = 20,$cols = 60, $toolbar = "", $height=300) {
		$this->default = $default;
		$this->module = $module; // For looking up templates.
		$this->toolbar = $toolbar;
		$this->height = $height;
	}
	
	
	function controlToHTML($name) {

			global $db;
			
			if($this->toolbar == "") {
				$config = $db->selectObject("toolbar_" . SITE_WYSIWYG_EDITOR, "active=1");
			}else{
				$config = $db->selectObject("toolbar_" . SITE_WYSIWYG_EDITOR, "name='" . $this->toolbar . "'");
			}
			
			//as long as we don't have proper datamodels, emulate them
			//there are at least two sets of data: view data and content data
			$view = new StdClass();
			$content = new StdClass();
			
			if (isset($config->data)) {
				$view->toolbar = $config->data;
			} else {
				$view->toolbar = null;
			}
			$view->path_to_editor = PATH_RELATIVE . "external/editors/" . SITE_WYSIWYG_EDITOR . "/";
			
			$content->name = $name;

			if (SITE_WYSIWYG_EDITOR == "FCKeditor") {
				//this belongs into the view layer, but as long we have PHP_REMOVE enabled on our templates...

				//$content->value = addslashes(str_replace(array("\n","\r"), "", $this->default));
				$content->value = $this->default;
			} else {
				$content->value = $this->default;
			}
			
			//create new view object
			//WARNING: automatic fallback to Default.tpl will not work
			//until exponent_core_resolveFilePaths() gets an update
			//waiting for switch to PHP5: strrpos() will take strings as needle
			$viewObj = new ControlTemplate("EditorControl", SITE_WYSIWYG_EDITOR);
	
			//assign the data models to the view object
			$viewObj->assign("view", $view);
			$viewObj->assign("content", $content);
			$viewObj->assign('height', $this->height);
			
			//return the processed template to the caller for display
			$html = $viewObj->render();
			
			//spares us to send the js editor init code more than once
			//TODO: Convert to OO API and use eXp->EditorControl->doneInit instead
			if(!defined("SITE_WYSIWYG_INIT")) {
				define("SITE_WYSIWYG_INIT", 1);
			}
			
			return $html;

	}
	
	
	function parseData($name, $values, $for_db = false) {
		$html = $values[$name];
		if (trim($html) == "<br />") $html = "";
		return $html;
	}
}
}

?>
