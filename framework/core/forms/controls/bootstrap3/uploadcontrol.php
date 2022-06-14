<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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

	function controlToHTML($name,$label)
    {
        $html = ($this->horizontal && bs3()) ? '<div class="col-sm-10">' : '';

        if (!empty($this->default)) {
            $html .= '<div class="fileinput fileinput-exists input-group" data-provides="fileinput">';
            $html .= '<input type="hidden"  name="' . $name . '" value="' . $this->default . '" />';
            $fi_name = '';
            $fi_file = $this->default;
        } else {
            $html .= '<div class="fileinput fileinput-new input-group" data-provides="fileinput" style="width:82%">';
            $fi_name = $name;
            $fi_file = '';
        }
        $html .= '  <div class="form-control" data-trigger="fileinput"><i class="fa fa-file fileinput-exists"></i> ';
        $html .= '<span class="fileinput-filename with-icon">' . $fi_file . '</span></div>';
        $html .= '  <span class="input-group-btn"><span class="btn btn-default btn-file"><span class="fileinput-new">' . gt('Select file') . '</span><span class="fileinput-exists">' . gt('Change') . '</span><input type="file" name="' . $fi_name . '"';
        if (!empty($this->accept))
            $html .= ' accept="' . $this->accept . '"';
        $html .= '></span>';
        $html .= '  <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">' . gt('Remove') . '</a></span>';
        $html .= '</div>';

        if (!empty($this->description)) $html .= "<div class=\"" . (bs3() ? "help-block" : "control-desc") . "\">" . $this->description . "</div>";
        $html .= ($this->horizontal && bs3()) ? '</div>' : '';

        expCSS::pushToHead(array(
            "unique" => 'fileupload-' . $name,
            "css" => "
                .fileinput-filename {
                    display: inline-block;
                    overflow: hidden;
                    vertical-align: middle;
                    /* new lines */
                    width: 100%;
                    position: absolute;
                    left: 0;
                    padding-left: 30px;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                }
    	    "
        ));
        if (bs4() || bs5()) {
            expCSS::pushToHead(array(
                "unique" => 'fileupload-bs4-' . $name,
                "css" => "
                    .fileinput.input-group,
                    .file-input {
                        display: flex;
                    }
        	    "
            ));
        }

        expJavascript::pushToFoot(array(
            "unique" => 'fileupload-' . $name,
            "jquery" => 'fileinput',
        ));

		return $html;
	}

    /**
     * Format the control's data for user display
     *
     * @param $db_data
     * @param $ctl
     * @return string
     */
    static function templateFormat($db_data, $ctl) {
        if (strlen(PATH_RELATIVE) > 1) {
            $base = str_replace(PATH_RELATIVE, '', BASE);
        } else {
            $base = rtrim(BASE, "\\/");
        }
        if (empty($db_data)) {
            return $db_data;
        } elseif (file_exists($base . $db_data)) {
            // if the file exists return a link
            if (PATH_RELATIVE !== '/')
                $baseurl = str_replace(PATH_RELATIVE, '', URL_BASE);
            else
                $baseurl = URL_BASE;
            return isset($db_data) ? ('<a href="' . $baseurl . $db_data . '">' . basename($db_data) . '</a>') : "";
        } else
            // file missing return filename
            return basename($db_data);
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
		$form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier),true, array('required'=>true));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
        $form->register("description",gt('Control Description'), new textcontrol($object->description));
		$form->register("default",gt('Default'), new textcontrol($object->default));
        $form->register("accept",gt('Accept'), new textcontrol($object->accept));
        if (!expJavascript::inAjaxAction())
    		$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		return $form;
	}

    static function update($values, $object) {
        if ($object == null) $object = new uploadcontrol();
        if ($values['identifier'] == "") {
            $post = expString::sanitize($_POST);
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST",$post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
        if (!empty($values['default']))
            $object->default = $values['default'];
        if (!empty($values['accept']))
            $object->accept = $values['accept'];
        return $object;
    }

	static function moveFile($original_name, $formvalues) {
		$dir = UPLOAD_DIRECTORY_RELATIVE . 'uploads';
		$filename = expFile::fixName(time() . '_' . $formvalues[$original_name]['name']);
		$dest = $dir . '/' . $filename;
        //Check to see if the directory exists.  If not, create the directory structure.
        if (!file_exists(BASE.$dir)) expFile::makeDirectory($dir);
        // Move the temporary uploaded file into the destination directory, and change the name.
        return expFile::moveUploadedFile($formvalues[$original_name]['tmp_name'], BASE . $dest);
	}

    static function moveRegistrationFile($original_name, $formvalues, $index) {
   		$dir = UPLOAD_DIRECTORY_RELATIVE . 'uploads';
   		$filename = expFile::fixName(time() . '_' . $formvalues['name'][$original_name][$index-1]);
   		$dest = $dir . '/' . $filename;
        //Check to see if the directory exists.  If not, create the directory structure.
        if (!file_exists(BASE.$dir)) expFile::makeDirectory($dir);
        // Move the temporary uploaded file into the destination directory, and change the name.
        return expFile::moveUploadedFile($formvalues['tmp_name'][$original_name][$index-1], BASE . $dest);
   	}

//    static function buildDownloadLink($control_name,$file_name,$mode) {
//   		$file = $formvalues[$original_name];
//   		return '<a href="'.PATH_RELATIVE.$file.'">'.basename($file).'</a>';
//   	}

    /**
     * Moves the uploaded file into our file system, NOT the database
     *
     * @param $name
     * @param $values
     * @param bool $for_db
     *
     * @return string   The full directory and filename of the uploaded file
     */
    static function parseData($name, $values, $for_db = false) {
        if (!isset($values[$name]) && empty($_FILES[$name]['name'])) {
            return null;
        } elseif (is_array($values[$name])) {
            $file = $values[$name]['name'];
//            return '<a href="'.URL_FULL.$file.'">'.basename($file).'</a>';  //FIXME this shouldn't be a link
        } else {
            if (!empty($values['isedit']) && !empty($_FILES[$name]['name'])) {
//                $file = $_FILES[$name]['name'];
                $file = PATH_RELATIVE . self::moveFile($name, $_FILES);
            } elseif (!empty($values['registration']) && !empty($_FILES['registrant']['name'])) {
                 $file = PATH_RELATIVE . self::moveRegistrationFile($name, $_FILES['registrant'], $values['registration']);
            } else {
                $file = $values[$name];
   //            return '<a href="'.URL_BASE.$file.'">'.basename($file).'</a>';  //FIXME this shouldn't be a link
            }
        }
        return $file;
	}
}

?>
