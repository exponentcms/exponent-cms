<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * Upload Control - display file upload widget
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class uploadcontrol extends formcontrol {

    var $accept = "";

	static function name() { return "File Upload Field"; }
	static function isSimpleControl() { return true; }
	static function getFieldDefinition() {
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

	function controlToHTML($name,$label) {
        $html = '';
        if (!empty($this->default)) $html .= '<input type="hidden"  name="'.$name.'" value="'.$this->default.'" />';
		$html .= "<input type=\"file\" name=\"$name\" ";
		if(isset($this->class)) $html .=  'class="' . $this->class . '"';
        if (!empty($this->accept)) $html .= ' accept="'.$this->accept.'"';
		$html .= ($this->disabled?" disabled ":"");
		$html .= ($this->tabindex>=0?" tabindex=\"".$this->tabindex."\" ":"");
		$html .= ($this->accesskey != ""?" accesskey=\"".$this->accesskey."\" ":"");
		$html .= "/>";
        if (!empty($this->default)) $html .= ' ('.basename($this->default).')';
        if (!empty($this->description)) $html .= "<div class=\"control-desc\">".$this->description."</div>";
		return $html;
	}

	static function form($object) {
		$form = new form();
        if (empty($object)) $object = new stdClass();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
            $object->description = "";
			$object->default = "";
            $object->accept = "";
		}
        if (empty($object->description)) $object->description = "";
		$form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
		$form->register("default",gt('Default'), new textcontrol($object->default));
        $form->register("accept",gt('Accept'), new textcontrol($object->accept));
		$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		return $form;
	}

    static function update($values, $object) {
        if ($object == null) $object = new uploadcontrol();
        if ($values['identifier'] == "") {
            $post = $_POST;
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
        $object->default = $values['default'];
        $object->accept = $values['accept'];
        return $object;
    }

	static function moveFile($original_name,$formvalues) {
		$dir = UPLOAD_DIRECTORY_RELATIVE . 'uploads';
		$filename = expFile::fixName(time().'_'.$formvalues[$original_name]['name']);
		$dest = $dir.'/'.$filename;
        //Check to see if the directory exists.  If not, create the directory structure.
        if (!file_exists(BASE.$dir)) expFile::makeDirectory($dir);
        // Move the temporary uploaded file into the destination directory, and change the name.
        expFile::moveUploadedFile($formvalues[$original_name]['tmp_name'],BASE.$dest);
		return $dest;
	}

//    static function buildDownloadLink($control_name,$file_name,$mode) {
//   		$file = $formvalues[$original_name];
//   		return '<a href="'.PATH_RELATIVE.$file.'">'.basename($file).'</a>';
//   	}

	static function parseData($original_name,$formvalues) {
        if (is_array($formvalues[$original_name])) {
            $file = $formvalues[$original_name]['name'];
//            return '<a href="'.URL_FULL.$file.'">'.basename($file).'</a>';  //FIXME this shouldn't be a link
        } else {
            if (!empty($formvalues['isedit']) && !empty($_FILES[$original_name]['name'])) {
//                $file = $_FILES[$original_name]['name'];
                $file = PATH_RELATIVE . self::moveFile($original_name, $_FILES, true);
            } else {
                $file = $formvalues[$original_name];
   //            return '<a href="'.URL_BASE.$file.'">'.basename($file).'</a>';  //FIXME this shouldn't be a link
            }
        }
        return $file;
	}
}

?>
