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

if (!defined('EXPONENT')) exit('');

/**
 * Text Editor Control
 *
 * @author James Hunt
 * @copyright 2004-2011 OIC Group, Inc.
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
		$toolbar = 'def';
		if (empty($this->toolbar)) $toolbar = $db->selectObject('htmleditor_ckeditor','active=1');
		if (empty($toolbar) || $this->toolbar=="default" || $this->toolbar->data=="default") {
			$tb = "
	           ['Source','-','Preview','-','Templates'],
               ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print','SpellChecker','Scayt'],
               ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
               '/',
               ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
               ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
               ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
               ['Link','Unlink','Anchor'],
               ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
               '/',
               ['Styles','Format','Font','FontSize'],
               ['TextColor','BGColor'],
               ['Maximize', 'ShowBlocks','-','About']
			";
			$skin = 'kama';
			$scayt_on = 'true';
			$paste_word = 'forcePasteAsPlainText : true,';
	    } else {
//			$tb = !empty($this->toolbar) ? $this->toolbar->data : $toolbar->data;
//			$skin = !empty($toolbar->skin) ? $toolbar->skin : 'kama';
			if (!empty($this->toolbar)) {
				$tb = $this->toolbar->data;
				$skin = $this->toolbar->skin;
				$scayt_on = $this->toolbar->scayt_on ? 'true' : 'false';
				$paste_word = $this->toolbar->paste_word ? 'pasteFromWordPromptCleanup : true,' : 'forcePasteAsPlainText : true,';
			} else {
				$tb = $toolbar->data;
				$skin = $toolbar->skin;
				$scayt_on = $toolbar->scayt_on ? 'true' : 'false';
				$paste_word = $toolbar->paste_word ? 'pasteFromWordPromptCleanup : true,' : 'forcePasteAsPlainText : true,';
			}
	    }
	    
	    $content = "
	    	EXPONENT.editor".createValidId($name)." = CKEDITOR.replace('".createValidId($name)."',
				{
					skin : '".$skin."',
					toolbar : [".stripSlashes($tb)."],
					".$paste_word."
                    scayt_autoStartup : ".$scayt_on.",
                    filebrowserBrowseUrl : '".makelink(array("controller"=>"file", "action"=>"picker", "ajax_action"=>1, "ck"=>1, "update"=>"fck"))."',
                    filebrowserWindowWidth : '640',
                    filebrowserWindowHeight : '480',
					filebrowserLinkBrowseUrl : '".PATH_RELATIVE."external/editors/connector/CKeditor_link.php',
                    filebrowserLinkWindowWidth : '320',
                    filebrowserLinkWindowHeight : '600',
					filebrowserImageBrowseLinkUrl : '".PATH_RELATIVE."external/editors/connector/CKeditor_link.php',
					entities_additional : ''
                });
				
				CKEDITOR.on( 'instanceReady', function( ev ) {
					var blockTags = ['div','h1','h2','h3','h4','h5','h6','p','pre','ol','ul','li'];
					var rules =  {
						indent : false,
						breakBeforeOpen : false,
						breakAfterOpen : false,
						breakBeforeClose : false,
						breakAfterClose : true
					};
					for (var i=0; i<blockTags.length; i++) {
						ev.editor.dataProcessor.writer.setRules( blockTags[i], rules );
					}
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
