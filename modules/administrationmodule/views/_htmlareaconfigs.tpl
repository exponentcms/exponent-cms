{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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
<div class="form_title">{$_TR.form_title}</div>
<div class="form_header">{$_TR.form_header_1}
<br /><br />
{$_TR.form_header_2}
<br /><br />
{$_TR.form_header_3} <a class="mngmntlink administration_mngmntlink" href="{link action=htmlarea_editconfig id=0}">{$_TR.form_header_4}
</div>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="header administration_header">{$_TR.config_name}</td>
		<td class="header administration_header">{$_TR.is_active}</td>
		<td class="header administration_header"></td>
	</tr>
	{foreach from=$configs item=config}
		<tr>
			<td>{$config->name}</td>
			<td>
				{if $config->active == 1}<b>{$_TR.yes}</b>{else}{$_TR.no}{/if}
			</td>
			<td>
				<a class="mngmntlink administration_mngmntlink" href="{link action=htmlarea_editconfig id=$config->id}">
					<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" />
				</a>
				<a class="mngmntlink administration_mngmntlink" href="{link action=htmlarea_deleteconfig id=$config->id}">
					<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" />
				</a>
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="2" align="center">
				<i>{$_TR.no_config_defined}</i>
			</td>
		</tr>
	{/foreach}
</table>