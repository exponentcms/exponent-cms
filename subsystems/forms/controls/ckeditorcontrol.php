<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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
 * Text Editor Control
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
 * Text Editor Control
 *
 * @package Subsystems
 * @subpackage Forms
 */
class ckeditorcontrol extends formcontrol {
	function name() { return "CKEditor"; }
	
	function __construct ($default="",$rows = 5,$cols = 45) {
		$this->default = $default;
		$this->rows = $rows;
		$this->cols = $cols;
		$this->required = false;
		$this->maxchars = 0;
	}

	function controlToHTML($name) {
	    global $db;
	    
	    $toolbar = $db->selectObject('htmleditor_ckeditor','active=1');
	    if (empty($toolbar)||$this->toolbar=="default") {
	       $tb = "
	           ['Source','-','Preview','-','Templates'],
               ['Cut','Copy','Paste','PasteText','PasteFromWord'],
               ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
               '/',
               ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
               ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
               ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
               ['Link','Unlink','Anchor'],
               ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
               '/',
               ['Styles','Format','Font','FontSize'],
               ['TextColor','BGColor'],
               ['Maximize', 'ShowBlocks','-','About']
	       ";
	    } else {
     	       $tb = !empty($this->toolbar) ? $this->toolbar : $toolbar->data;
	    }
	    
	    $content = "
	    	EXPONENT.editor".createValidId($name)." = CKEDITOR.replace('".createValidId($name)."',
				{
                    filebrowserBrowseUrl : '".makelink(array("controller"=>"file", "action"=>"picker", "ajax_action"=>1, "ck"=>1, "update"=>"fck"))."',
					filebrowserLinkBrowseUrl : '".PATH_RELATIVE."external/editors/ckconnector/CKeditor_link.php',
					toolbar : [".stripSlashes($tb)."],
                    forcePasteAsPlainText:true,
                    filebrowserWindowWidth : '640',
                    filebrowserWindowHeight : '480'
                });
                
	    ";
	    
	    expJavascript::pushToFoot(array(
            "unique"=>"cke".$name,
            "content"=>$content,
            //"src"=>PATH_RELATIVE."external/ckeditor/ckeditor.js"
         ));
		$html = "<script src=\"".PATH_RELATIVE."external/ckeditor/ckeditor.js\"></script>";
		$html .= "<textarea class=\"textarea\" id=\"".createValidId($name)."\" name=\"$name\"";
		$html .= " rows=\"" . $this->rows . "\" cols=\"" . $this->cols . "\"";
		if ($this->accesskey != "") $html .= " accesskey=\"" . $this->accesskey . "\"";
		if (!empty($this->class)) $html .= " class=\"" . $this->class . "\"";
		if ($this->tabindex >= 0) $html .= " tabindex=\"" . $this->tabindex . "\"";

		$html .= ">";
		$html .= htmlentities($this->default,ENT_COMPAT,LANG_CHARSET);
		$html .= "</textarea>";
		return $html;
	}
		
}

?>
