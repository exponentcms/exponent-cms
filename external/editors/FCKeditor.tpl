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

	<script type="text/javascript" src="{$view->path_to_editor}fckeditor.js"></script>
	
	<script type="text/javascript">
	/* <![CDATA[ */
{* cannot exclude this part based on SITE_WYSIWYG_INIT because the first editor might come without a toolbar, but others with*}
{if $view->toolbar != NULL}
{literal}
		eXp.WYSIWYG.serialize = function(myArray) {
			var myStr = "[";
			for (i = 0; i < myArray.length; i++) {
				// do we have more than one dimension ?
				//HACK: hoping there will never be plugins or commands that just have one character 
				if (myArray[i][0] != undefined){
					if(myArray[i][0].length > 1) {
						myStr += "['";
						for (j = 0; j < myArray[i].length; j++) {
							myStr += myArray[i][j];
							if (j != myArray[i].length-1) {
								myStr+="', '";
							}
						}
						myStr += "']";
					} else {
						myStr += "'" + myArray[i] + "'";
					}
				
					if (i != myArray.length - 1) {
						myStr += ", ";
					}
				}
			}
			myStr += "]";
			return myStr;
		}
		
		eXp.WYSIWYG.setupToolbar = function(myToolbar) {
			myLength = myToolbar.length;
			for(currRow = 1; currRow < myLength; currRow++) {
				//FCKeditor's way of forcing rowbreaks is placing an "/" behind an array element
				myToolbar.splice(currRow, 0, "/");
			}
			return this.serialize(myToolbar);
		}
	
		eXp.WYSIWYG.setupPlugins = function(myToolbar) {
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
			return this.serialize(plugins);
		}
{/literal}
{/if}		

		var oFCKeditor = new FCKeditor('{$content->name}');
		
			
		oFCKeditor.BasePath = "{$view->path_to_editor}";
		oFCKeditor.Value = "{$content->value|escape:'javascript'}";
		
		oFCKeditor.Height= '{$height}';

		oFCKeditor.Config['LinkUpload'] = "false";
		oFCKeditor.Config['ImageUpload'] = "false";
		oFCKeditor.Config['FlashUpload'] = "false";
	
		oFCKeditor.Config['LinkBrowserURL'] = "{$view->path_to_editor}../connector/FCKeditor_link.php";
		oFCKeditor.Config['ImageBrowserURL'] = "{link controller=file action=picker ajax_action=1 fck=1 update=fck}";
		//oFCKeditor.Config['ImageBrowserURL'] = "{$view->path_to_editor}../../../modules/filemanagermodule/actions/picker.php?id=0";
		oFCKeditor.Config['ImageBrowserWindowWidth'] = "805px";
		oFCKeditor.Config['ImageBrowserWindowHeight'] = "600px";
		
{if $view->toolbar != NULL}
		//HACK: god, i do hate this editor ! Why can't i simply configure the toolbars and plugins from here ? There ARE other reasons to hate it ...
		oFCKeditor.Config["CustomConfigurationsPath"] = "{$smarty.const.PATH_RELATIVE}external/editors/fcktoolbarconfig.js.php?plugins=" + encodeURI(eXp.WYSIWYG.setupPlugins(eXp.WYSIWYG.toolbar)) + "&toolbar=" + encodeURI(eXp.WYSIWYG.setupToolbar(eXp.WYSIWYG.toolbar));
{/if}
		oFCKeditor.Config["EditorAreaCSS"] = '{editorinclude filename="wysiwyg-styles.css"}';
		oFCKeditor.Config["StylesXmlPath"] = '{editorinclude filename="fckstyles.xml"}';	
		oFCKeditor.Config["TemplatesXmlPath"] = '{editorinclude filename="fcktemplates.xml"}';	
		oFCKeditor.Config["ForcePasteAsPlainText"] = "true";	
		oFCKeditor.Config["SkinPath"] = "{$smarty.const.URL_FULL}external/editors/FCKeditor/editor/skins/office2003/";
		//oFCKeditor.Config["SkinPath"] = "{$view->path_to_editor}skins/default/";
		oFCKeditor.Config["FontFormats"]='p;h1;h2;h3;h4;h5;h6';
		oFCKeditor.Config["ProcessHTMLEntities"]="false";
		oFCKeditor.CustomStyles = {literal}
		{
			//'Red Title'	: { Element : 'h3', Styles : { 'color' : 'Red' } }
		};
		{/literal}
		oFCKeditor.Create();
	/* ]]> */
	</script>
</div>
