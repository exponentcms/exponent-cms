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

/** Edit all fields containing "_html" by HTML editor TinyMCE and display the HTML in select
* @link http://www.adminer.org/plugins/#use
* @uses TinyMCE, http://tinymce.moxiecode.com/
* @author Jakub Vrana, http://www.vrana.cz/
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerTinymce {
	/** @access protected */
	var $path;
	
	/**
	* @param string
	*/
	function __construct($path = "tiny_mce/tiny_mce.js") {
		$this->path = $path;
	}
	
	function head() {
		$lang = "en";
		if (function_exists('get_lang')) { // since Adminer 3.2.0
			$lang = get_lang();
			$lang = ($lang == "zh" ? "zh-cn" : ($lang == "zh-tw" ? "zh" : $lang));
			if (!file_exists(dirname($this->path) . "/langs/$lang.js")) {
				$lang = "en";
			}
		}
		?>
<script type="text/javascript" src="<?php echo h($this->path); ?>"></script>
<script type="text/javascript">
tinyMCE.init({
	mode: 'none',
	plugins: "advlist,autolink,lists,link,charmap,print,preview,hr,anchor,pagebreak" +
             ",searchreplace,wordcount,visualblocks,visualchars,code,fullscreen" +
             ",nonbreaking,save,table,contextmenu,directionality" +
             ",emoticons,paste,textcolor",  //image,imagetools.media not available at this time
	browser_spellcheck: true,
	entity_encoding: 'raw',
	relative_urls : false,
	remove_script_host : true,
	document_base_url : '<?php echo PATH_RELATIVE; ?>',
//	image_advtab: true,
//	image_title: true,
// 	image_caption: true,
	language: '<?php echo $lang; ?>',
	end_container_on_empty_block: true,
//	file_picker_callback: function expBrowser (callback, value, meta) {
//		tinymce.activeEditor.windowManager.open({
//			file: '<?php //echo makelink(array("controller" => "file", "action" => "picker", "ajax_action" => 1, "update" => "tiny")); ?>//?filter='+meta.filetype,
//			title: '<?php //echo gt('File Manager'); ?>//',
//			width: <?php //echo FM_WIDTH ?>//,
//			height: <?php //echo FM_HEIGHT ?>//,
//			resizable: 'yes'
//		}, {oninsert: function (url, alt, title) {
//				// Provide file and text for the link dialog
//				if (meta.filetype == 'file')
//					callback(url, {text: alt, title: title});
//				// Provide image and alt text for the image dialog
//				if (meta.filetype == 'image')
//					callback(url, {alt: alt});
//				// Provide alternative source and posted for the media dialog
//				if (meta.filetype == 'media')
//					callback(url);
//			}
//		});
//		return false;
//	},
});
</script>
<?php
	}
	
	function selectVal(&$val, $link, $field) {
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
        if (preg_match("~text~", $field["type"]) && preg_match("~body~", $field["field"])) {
			return "<textarea$attrs id='fields-" . h($field["field"]) . "' rows='6' cols='50'>" . h($value) . "</textarea><script type='text/javascript'>
tinyMCE.remove(tinyMCE.get('fields-" . js_escape($field["field"]) . "') || { });
tinyMCE.execCommand('mceAddEditor', true, 'fields-" . js_escape($field["field"]) . "');
document.getElementById('form').onsubmit = function () {
	tinyMCE.each(tinyMCE.editors, function (ed) {
		ed.remove();
	});
};
</script>";
		}
	}
	
}
