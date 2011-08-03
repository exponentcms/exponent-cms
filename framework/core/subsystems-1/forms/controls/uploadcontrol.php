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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Upload Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class uploadcontrol extends formcontrol {

	function name() { return "File Upload Field"; }
	function isSimpleControl() { return false; }
	function getFieldDefinition() {
		return array(
			DB_FIELD_TYPE=>DB_DEF_STRING,
			DB_FIELD_LEN=>250,);
	}
	
	function __construct($default = "", $disabled = false) {
		$this->disabled = $disabled;
	}
	
	function onRegister(&$form) {
		$form->enctype = "multipart/form-data";
	}

	function controlToHTML($name) {
		$html = "<input type=\"file\" name=\"$name\" ";
		if(isset($this->class)) $html .=  'class="' . $this->class . '"';
		$html .= ($this->disabled?"disabled ":"");
		$html .= ($this->tabindex>=0?"tabindex=\"".$this->tabindex."\" ":"");
		$html .= ($this->accesskey != ""?"accesskey=\"".$this->accesskey."\" ":"");
		$html .= "/>";
		return $html;
	}

	function form($object) {
//		if (!defined("SYS_FORMS")) require_once(BASE."framework/core/subsystems-1/forms.php");
		require_once(BASE."framework/core/subsystems-1/forms.php");
//		exponent_forms_initialize();

		$form = new form();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
			$object->default = "";
		}
		$i18n = exponent_lang_loadFile('subsystems/forms/controls/textcontrol.php');

		$form->register("identifier",$i18n['identifier'],new textcontrol($object->identifier));
		$form->register("caption",$i18n['caption'], new textcontrol($object->caption));
		$form->register("default",$i18n['default'], new textcontrol($object->default));
		$form->register("submit","",new buttongroupcontrol($i18n['save'],'',$i18n['cancel']));
		return $form;
	}

	function update($values, $object) {
        if ($object == null) $object = new uploadcontrol();
        if ($values['identifier'] == "") {
            $i18n = exponent_lang_loadFile('subsystems/forms/controls/textcontrol.php');
            $post = $_POST;
            $post['_formError'] = $i18n['id_req'];
            expSession::set("last_POST",$post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->default = $values['default'];
        return $object;
    }

	function moveFile($original_name,$formvalues) {
//		if (!defined('SYS_FILES')) include_once(BASE.'framework/core/subsystems-1/files.php');
		include_once(BASE.'framework/core/subsystems-1/files.php');
		$dir = 'files/uploads';
		$filename = exponent_files_fixName(time().'_'.$formvalues[$original_name]['name']);
		$dest = $dir.'/'.$filename;
        //Check to see if the directory exists.  If not, create the directory structure.
        if (!file_exists(BASE.$dir)) exponent_files_makeDirectory($dir);
        // Move the temporary uploaded file into the destination directory, and change the name.
        exponent_files_moveUploadedFile($formvalues[$original_name]['tmp_name'],BASE.$dest);
		return $dest;
	}

	static function parseData($original_name,$formvalues) {
		$file = $formvalues[$original_name];
		return '<a href="'.URL_FULL.$file.'">'.basename($file).'</a>';
	}
}

?>
