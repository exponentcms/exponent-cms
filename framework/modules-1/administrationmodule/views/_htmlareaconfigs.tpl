{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Copyright (c) 2007 Maxim Mueller
 * Written and Designed by James Hunt
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}
<div class="form_title">{'WYSIWYG Toolbar Settings'|gettext}</div>
<div class="form_header">{'A WYSIWYG (What You See is What You Get) HTML editor is a component which allows you to edit your site content using easy to use point-and-click type tools.  This form allows you to set up toolbar configurations which govern what buttons are available on the toolbar for editors to use.'|gettext}
<br /><br />
{'The active configuration is used for all WYSIWYG controls across the entire site.'|gettext}
<br /><br />
{'To create a new toolbar, use the'|gettext} <a class="mngmntlink administration_mngmntlink" href="{link action=htmlarea_editconfig id=0}">{'New Configuration</a> form.'|gettext}
</div>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="header administration_header">{'Configuration Name'|gettext}</td>
		<td class="header administration_header">{'Active?'|gettext}</td>
		<td class="header administration_header"></td>
	</tr>
	{foreach from=$configs item=config}
		<tr>
			<td>{$config->name}</td>
			<td>
				{if $config->active == 1}<b>{'yes'|gettext}</b>{else}{'no'|gettext}{/if}
			</td>
			<td>
				<a class="mngmntlink administration_mngmntlink" href="{link action=htmlarea_editconfig id=$config->id}">
					<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{'Edit'|gettext}" alt="{'Edit'|gettext}" />
				</a>
				<a class="mngmntlink administration_mngmntlink" href="{link action=htmlarea_deleteconfig id=$config->id}">
					<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{'Delete'|gettext}" alt="{'Delete'|gettext}" />
				</a>
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="2" align="center">
				<i>{'No Configurations have been defined.'|gettext}</i>
			</td>
		</tr>
	{/foreach}
</table>