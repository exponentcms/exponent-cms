<?php

##################################################
#
# Copyright (c) 2004-2017 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');

/**
 * HTML Editor Control - displays wysiwyg editor widget
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
if (SITE_WYSIWYG_EDITOR == "ckeditor") {

class htmleditorcontrol extends ckeditorcontrol {
}

} elseif (SITE_WYSIWYG_EDITOR == "tinymce") {
class htmleditorcontrol extends tinymcecontrol {
}

} else {

class htmleditorcontrol extends formcontrol {

	var $module = "";
	var $toolbar = "";

	static function name() {return "WYSIWYG Editor";}

	function __construct($default="",$module = "",$rows = 20,$cols = 60, $toolbar = "", $height=300) {
		$this->default = $default;
		$this->module = $module; // For looking up templates.
		$this->toolbar = $toolbar;
		$this->height = $height;
	}

	function controlToHTML($name,$label) {
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

			$content->value = $this->default;

			//create new view object
			//WARNING: automatic fallback to Default.tpl will not work
			//until expCore::resolveFilePaths() gets an update
			//waiting for switch to PHP5: strrpos() will take strings as needle
//			$viewObj = new controltemplate("EditorControl", SITE_WYSIWYG_EDITOR);
//
//			//assign the data models to the view object
//			$viewObj->assign("view", $view);
//			$viewObj->assign("content", $content);
//			$viewObj->assign('height', $this->height);
//
//			//return the processed template to the caller for display
//			$html = $viewObj->render();

			//spares us to send the js editor init code more than once
			//TODO: Convert to OO API and use eXp->EditorControl->doneInit instead
			if(!defined('SITE_WYSIWYG_INIT')) {
				define('SITE_WYSIWYG_INIT', 1);
			}

//			return $html;

	}

	static function parseData($name, $values, $for_db = false) {
		$html = $values[$name];
		if (trim($html) == "<br />") $html = "";
		return $html;
	}
}
}

?>
