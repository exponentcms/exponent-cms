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

/** Edit all fields containing "_html" by HTML editor CKeditor and display the HTML in select
* @link http://www.adminer.org/plugins/#use
* @uses CKeditor, http://www.ckeditor.com/
* @author Dave Leffler, http://www.harrisonhills.org/tech
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerCKeditor {
	/** @access protected */
	var $scripts, $options;
	
	/**
	* @param array
	* @param string in format "skin: 'custom', preInit: function () { }"
	*/
	function __construct($scripts = array(null), $options = "") {
		$this->scripts = $scripts;
		$this->options = $options;
	}
	
	function head() {
		foreach ($this->scripts as $script) {
			echo "<script type='text/javascript' src='" . h($script) . "'></script>\n";
		}
	}
	
	function selectVal(&$val, $link, $field) {
		// copied from tinymce.php
        if (preg_match("~body~", $field["field"]) && $val != '&nbsp;') {
			$shortened = (substr($val, -10) == "<i>...</i>");
			if ($shortened) {
				$val = substr($val, 0, -10);
			}
			//! shorten with regard to HTML tags - http://php.vrana.cz/zkraceni-textu-s-xhtml-znackami.php
			$val = preg_replace('~<[^>]*$~', '', html_entity_decode($val, ENT_QUOTES)); // remove ending incomplete tag (text can be shortened)
			if ($shortened) {
				$val .= "<i>...</i>";
			}
			if (class_exists('DOMDocument')) { // close all opened tags
				$dom = new DOMDocument;
				if (@$dom->loadHTML("<meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head>$val")) { // @ - $val can contain errors
					$val = preg_replace('~.*<body[^>]*>(.*)</body>.*~is', '\\1', $dom->saveHTML());
				}
			}
		}
	}
	
	function editInput($table, $field, $attrs, $value) {
		static $lang = "";
		if (!$lang && preg_match("~text~", $field["type"]) && preg_match("~body~", $field["field"])) {
			$lang = "en";
			if (function_exists('get_lang')) { // since Adminer 3.2.0
				$lang = get_lang();
				$lang = ($lang == "zh" || $lang == "zh-tw" ? "zh_cn" : $lang);
			}
			return "<textarea$attrs id='fields-" . h($field["field"]) . "' rows='6' cols='50'>" . h($value) . "</textarea><script type='text/javascript'>
CKEDITOR.replace('fields-" . js_escape($field["field"]) . "',{
        height : '80',
        toolbarCanCollapse : true,
        toolbarStartupExpanded : false,
        scayt_autoStartup : true,
        removePlugins : 'elementspath',
        resize_enabled : false,
		filebrowserBrowseUrl : '" . makelink(array("controller"=> "file", "action"=> "picker", "ajax_action"=> 1, "update"=> "ck")) . "',
		filebrowserImageBrowseUrl : '" . makelink(array("controller"=> "file", "action"=> "picker", "ajax_action"=> 1, "update"=> "ck", "filter"=> 'image')) . "',
		filebrowserFlashBrowseUrl : '" . makelink(array("controller"=> "file", "action"=> "picker", "ajax_action"=> 1, "update"=> "ck")) . "',
        filebrowserUploadUrl : '" . PATH_RELATIVE . "framework/modules/file/connector/uploader.php',
        uploadUrl : '" . PATH_RELATIVE . "framework/modules/file/connector/uploader_paste.php',
		filebrowserWindowWidth : " . FM_WIDTH . ",
		filebrowserWindowHeight : " . FM_HEIGHT . ",
		filebrowserImageBrowseLinkUrl : '" . PATH_RELATIVE . "framework/modules/file/connector/ckeditor_link.php?update=ck',
		filebrowserLinkBrowseUrl : '" . PATH_RELATIVE . "framework/modules/file/connector/ckeditor_link.php?update=ck',
		filebrowserLinkWindowWidth : 320,
		filebrowserLinkWindowHeight : 600,
		extraPlugins : 'autosave,tableresize,image2,uploadimage,quicktable,showborders',
		removePlugins: 'image,forms,flash',
    });
</script>";
		}
	}
	
}
