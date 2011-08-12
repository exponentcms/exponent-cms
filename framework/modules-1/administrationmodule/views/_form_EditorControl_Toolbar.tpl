{*

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006-2007 Maxim Mueller
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

// Should be moved to the EditorControl or ToolbarItem

*}
{* the header include contains the starting <div> tag*}	
	{include file="../../../themes/common/editors/_header.inc" toolbar="`$content->data`"}
	
	<h2>{$smarty.const.SITE_WYSIWYG_EDITOR}</h2>
	<!--a href="http://www.xinha.com" target="_blank"><img style="border:0px solid none;" src="{$smarty.const.PATH_RELATIVE}external/editors/Xinha/images/xinha_logo.gif" /></a-->
	<p>{'This forms allows you to define a custom toolbar for the selected editor.'|gettext}</p>
	<div>
		<div id="editorcontrol_toolbox" class="clearfloat"></div>
		<div id="msgTD"></div>
	</div>
	<div class="clearfloat">
		<hr/>
		<a class="mngmntlink administration_mngmntlink" href="#" onclick="eXp.WYSIWYG.createRow();">{'New Row'|gettext}</a><hr/>
	</div>
	<div id="editorcontrol_toolbar" class="clearfloat"></div>

	<script type="text/javascript">
	/* <![CDATA[ */
		// populate the button panel
		eXp.WYSIWYG.buildToolbox(eXp.WYSIWYG.toolbox);
{literal}				
		for(currRow = 0; currRow < eXp.WYSIWYG.toolbar.length; currRow++) {
			rows.push(new Array());
		
			for(currButton = 0; currButton < eXp.WYSIWYG.toolbar[currRow].length; currButton++) {

				if (eXp.WYSIWYG.toolbar[currRow][currButton] != "") {
					rows[currRow].push(eXp.WYSIWYG.toolbar[currRow][currButton]);
					eXp.WYSIWYG.disableToolbox(eXp.WYSIWYG.toolbar[currRow][currButton]);
				}
			}
		}
{/literal}
		eXp.WYSIWYG.buildToolbar();
	/* ]]> */
	</script>

	<br />
	<div class="clearfloat">
		<hr/>

		<form method="post">
			<input type="hidden" name="module" value="administrationmodule"/>
			<input type="hidden" name="action" value="htmlarea_saveconfig"/>
{if $content->id != null}
			<input type="hidden" name="id" value="{$content->id}"/>
{/if}
			<input type="hidden" name="config" value="" id="config_htmlarea" />
			{'Configuration'|gettext}:<br />
			<input type="text" name="config_name" value="{$content->name}" /><br />
			<input type="checkbox" name="config_activate" {if $content->active == 1}checked="checked"{/if}/> {'Activate'|gettext}?<br />

			<input type="submit" value="{'Submit'|gettext}" onclick="eXp.WYSIWYG.save(this.form); return false;"/>
			<input type="button" value="{'Back'|gettext}" onclick="window.location='{$__redirect}';"/>
		</form>
	</div>
</div>

