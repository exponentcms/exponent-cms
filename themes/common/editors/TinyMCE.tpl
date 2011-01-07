{*
##################################################
#
# Copyright (c) 2005-2007  Maxim Mueller
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

#This glue file is included by subsystems/forms/controls/htmleditorcontrol.php
#it provides the code for the htmleditorcontrol class' controltoHTML() method
# it's based on James Hunt's code for that original class
*}
{* the header include contains the starting <div> tag*}	
	{include file="_header.inc" toolbar="`$view->toolbar`"}
	
	<textarea id="{$content->name}" name="{$content->name}" class="mceEditor">{$content->value}</textarea>

	<script type="text/javascript" src="{$view->path_to_editor}jscripts/tiny_mce/tiny_mce.js"></script>
	
	<!-- load languagefile, prepare for HTMLArea popup(Link + Image Browsers) -->
	<script type="text/javascript">
	/* <![CDATA[ */
{* cannot exclude this part based on SITE_WYSIWYG_INIT because the first editor might come without a toolbar, but others with *}
{if $view->toolbar != NULL}
{literal}
	// if plugins are needed, set them up
	eXp.WYSIWYG.setupPlugins = function (myToolbar) {
		plugins = new Array();
			
		for(currRow = 0; currRow < myToolbar.length; currRow++) {
			for(currButton = 0; currButton < myToolbar[currRow].length; currButton++) {
				currItem = myToolbar[currRow][currButton];
				// plugin required ?
				if(eXp.WYSIWYG.toolbox[currItem][2] != "") {
					plugins.push(eXp.WYSIWYG.toolbox[currItem][2]);
				}
			}
		}
			
		//serialize 
		if(plugins.length != 0) {
			var myStr = '';
			for(currPlugin = 0; currPlugin < plugins.length; currPlugin++) {
				myStr += plugins[currPlugin];
				if (currPlugin != plugins.length-1) {
					myStr+= ', ';
				}
			}
			this.config["plugins"] = String(myStr);
 		}
	}
		
	// convert from JS Array into Toolbar init syntax
	eXp.WYSIWYG.setupToolbar = function (myToolbar) {
		for(currRow = 0; currRow < myToolbar.length; currRow++) {
			this.config['theme_advanced_buttons' + String(currRow + 1)] = String(myToolbar[currRow]);
		}
		// the advanced theme always provides at least three rows, clear the ones that are not set by us
		if(myToolbar.length < 3) {
			this.config['theme_advanced_buttons3'] = "";
			if(myToolbar.length < 2) {
				this.config['theme_advanced_buttons2'] = "";
				//the case of myToolbar.length < 1 REALLY should not happen	
			}	
		}
	}
{/literal}
{/if}

{if $smarty.const.SITE_WYSIWYG_INIT != 1}		
{literal}	
	//we need this to get the data from the popup(object for "opener" interaction)
	var Dialog = new Object();
	//callback from the HTMLArea popup(__dlg_close())
	Dialog._return = function (val) {
		if (val) {
			if (val['f_dialogType'] == "Link") {
				tinyMCE.execCommand('mceInsertContent',false,'<a href="' + val['f_href'] + '" target="' + val['f_target'] + '" title="' + val['f_title'] + '">' + tinyMCE.getInstanceById('{/literal}{$content->name}{literal}').selection.getSelectedHTML() + '</a>');
			}
			if (val['f_dialogType'] == "Image") {
				// TODO: generate CSS styled images 
				// if ((val['f_align'] == 'right') or (val['f_align'] == 'right')) {
				// 	csscode = "float: " + val['f_align'];
				// } else {
				// 	csscode = "vertical-align: " + val['f_align'];
				// }
				// tinyMCE.execCommand('mceInsertContent',false,'<img src="' + val['f_url'] + '" alt="' + val['f_alt'] + '" style="margin: ' + val['f_vert'] + 'px ' + val['f_horiz'] + 'px; border: ' + val['f_border'] + 'px solid black;' + csscode + '"/>');
				//
				tinyMCE.execCommand('mceInsertContent',false,'<img src="' + val['f_url'] + '" alt="' + val['f_alt'] + '" align="' + val['f_align'] + '" vspace="' + val['f_vert'] + '" hspace="' + val['f_horiz'] + '" border="' + val['f_border'] + '"/>');
			}
		} 
	};

	function myCustomExecCommandHandler(editor_id, elm, command, user_interface, value) {
		var linkElm, imageElm, inst;
	
		switch (command) {
			case "mceLink":
				inst = tinyMCE.getInstanceById(editor_id);
				linkElm = tinyMCE.getParentElement(inst.selection.getFocusElement(), "a");
		
				//do we update or create ?
				//if (linkElm) {
					Dialog._arguments = new Array();
					Dialog._arguments['f_href'] = tinyMCE.getAttrib(linkElm, "href");
					Dialog._arguments['f_target'] = tinyMCE.getAttrib(linkElm, "target");
					Dialog._arguments['f_title'] = tinyMCE.getAttrib(linkElm, "title");
				//} else {
					// just in case values were set in a previous run
				//	if (Dialog._arguments) {
				//		delete Dialog._arguments;
				//	}
				//}
				var LinkWindow = window.open("{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/connector/link.php", "Link", "width=400, height=275, resizable=yes");
				LinkWindow.focus();
					
				return true;
		
			case "mceImage":
				inst = tinyMCE.getInstanceById(editor_id);
				imageElm = tinyMCE.getParentElement(inst.selection.getFocusElement(), "img");
		
				//do we update or create ?
				//if (imageElm) {
					Dialog._arguments = new Array();
					Dialog._arguments['f_url'] = tinyMCE.getAttrib(imageElm, "src");
					Dialog._arguments['f_alt'] = tinyMCE.getAttrib(imageElm, "alt");
					Dialog._arguments['f_border'] = tinyMCE.getAttrib(imageElm, "border");
					Dialog._arguments['f_horiz'] = tinyMCE.getAttrib(imageElm, "hspace");
					Dialog._arguments['f_vert'] = tinyMCE.getAttrib(imageElm, "vspace");
					Dialog._arguments['f_align'] = tinyMCE.getAttrib(imageElm, "align");
				//} else {
					// just in case values were set in a previous run
				//	if (Dialog._arguments) {
				//		delete Dialog._arguments;
				//	}
				//}
				var ImageWindow = window.open("{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/editors/connector/insert_image.php", "Image", "width=400, height=390, resizable=yes");
				ImageWindow.focus();
					
				return true;
		}
		
		return false;
	}
{/literal}
{/if}
{literal}
	// initialize TinyMCE
	eXp.WYSIWYG.config =	{
					mode				: "textareas",
					editor_selector			: "mceEditor",
					theme 				: "advanced",
					theme_advanced_toolbar_location : "top",
					theme_advanced_layout_manager	: "SimpleLayout",
					add_unload_trigger		: false,
					//eXp's new lang naming semantics are incompatible to pretty much everything, translation functions are needed
					language			: "{/literal}{$smarty.const.LANG|convertLangCode}{literal}",
					execcommand_callback 		: "myCustomExecCommandHandler",
					convert_urls			: false,
					//a temporary (?) fix for the layout of TinyMCE, currently it becomes "tiny" with a small custom toolbar
					width				: "100%",
					height				: "100%",
					entity_encoding			: "raw"
				};
{/literal}
{if $view->toolbar != NULL}
	eXp.WYSIWYG.setupPlugins(eXp.WYSIWYG.toolbar);
	eXp.WYSIWYG.setupToolbar(eXp.WYSIWYG.toolbar);
{/if}
		
	tinyMCE.init(eXp.WYSIWYG.config);

	/* ]]> */
	</script>
</div>	