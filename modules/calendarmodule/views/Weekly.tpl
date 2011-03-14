{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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
 
{css unique="cal" link="`$smarty.const.PATH_RELATIVE`modules/calendarmodule/assets/css/calendar.css"}

{/css}

<div class="module calendar weekly"> 
	<table width="177" border="0" cellpadding="0" cellspacing="0" bordercolor="666666">
							<tr>
									<td align="left" valign="top" bgcolor="99999">
											<table width="177" border="0" cellpadding="0" cellspacing="0" bgcolor="#999999">
									<tr>
															<td width="20" align="left" valign="top"><img class="mngmnt_icon" src="{$smarty.const.PATH_RELATIVE}modules/calendarmodule/images/topcurve.gif" width="20" height="20" /></td>
															<td width="132" class="moduletitle calendar_highlights_moduletitle">{if $moduletitle != ""}<div align="center">{$moduletitle}</div>{/if}</td>
															<td width="20"></td>
													</tr>
											</table>
									</td>
							</tr>
							<tr>
									<td height="28" align="left" valign="top" bordercolor="999999">
											<table width="177" border="0" cellpadding="0" cellspacing="0" bordercolor="#999999">
													<tr>
															<td width="3"  bgcolor="#999999"></td>
															<td align="left" valign="top">
																	<table width="176" border="0" cellspacing="5" cellpadding="0">
																			<tr>
																					<td align="left" valign="top">
	{foreach from=$days item=events key=ts}
		<div class="sectiontitle">
		<b>{$ts|format_date:$smarty.const.DISPLAY_DATE_FORMAT}</b>
		</div>
		{assign var=none value=1}
		{foreach from=$events item=event}
			{assign var=none value=0}
			<div class="paragraph">
			{if $event->is_allday == 0}{$event->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT}{/if}
			
			<a class="mngmntlink calendar_mngmntlink" href="{link action=view id=$event->id date_id=$event->eventdate->id}">{$event->title}</a>
			{if $permissions.edit == 1 || $event->permissions.edit == 1 || $permissions.delete == 1 || $event->permissions.delete == 1 || $permissions.administrate == 1 || $event->permissions.administrate == 1}
			<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			{/if}
			{permissions level=$smarty.const.UILEVEL_PERMISSIONS}
			{if $permissions.administrate == 1 || $event->permissions.administrate == 1}
			<a class="mngmntlink calendar_mngmntlink" href="{link action=userperms int=$event->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}userperms.png" title="{$_TR.alt_userperm_one}" alt="{$_TR.alt_userperm_one}" /></a>
			<a class="mngmntlink calendar_mngmntlink" href="{link action=groupperms int=$event->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}groupperms.png" title="{$_TR.alt_groupperm_one}" alt="{$_TR.alt_groupperm_one}" /></a>
			{/if}
			{/permissions}
			{permissions}
			{if $permissions.edit == 1 || $event->permissions.edit == 1}
				{if $event->approved == 1}
				<a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=$event->id date_id=$event->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>
				{else}
				<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.disabled.png" title="{$_TR.alt_edit_disabled}" alt="{$_TR.alt_edit_disabled}" />
				{/if}
			{/if}
			{if $permissions.delete == 1 || $event->permissions.delete == 1}
				{if $event->approved == 1}
				{if $event->is_recurring == 0}
				<a class="mngmntlink calendar_mngmntlink" href="{link action=delete id=$event->id}" onclick="return confirm('{$_TR.delete_confirm}');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
				{else}
				<a class="mngmntlink calendar_mngmntlink" href="{link action=delete_form id=$event->id date_id=$event->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
				{/if}
				{else}
				<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.disabled.png" title="{$_TR.alt_delete_disabled}" alt="{$_TR.alt_delete_disabled}" />
				{/if}
			{/if}
			{if $permissions.manage_approval == 1}
				<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=revisions_view id=$event->id}" title="{$_TR.alt_revisions}"><img class="mngmnt_icon" src="{$smarty.const.ICON_RELATIVE}revisions.png" title="{$_TR.alt_revisions}" alt="{$_TR.alt_revisions}"/></a>
			{/if}
			{/permissions}
			<br />
		{/foreach}
		{if $none == 1}
			<div class="paragraph"><i>{$_TR.no_event}</i></div>
		{/if}
		<br />
	{/foreach}
	{permissions}
	{if $permissions.post == 1}
	<a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=0}" title="{$_TR.alt_create}" alt="{$_TR.alt_create}">{$_TR.create}</a><br />
	{/if}
	{if $in_approval != 0 && $canview_approval_link == 1}
	<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=summary}" title="{$_TR.alt_approval}" alt="{$_TR.alt_approval}">{$_TR.approval}</a>
	{/if}
	{/permissions}
																					</td>
																			</tr>
																	</table>
															</td>
													</tr>
											</table>
									</td>
							</tr>
							<tr>
									<td height="10" align="left" valign="top" bgcolor="999999"><img class="mngmnt_icon" src="{$smarty.const.PATH_RELATIVE}modules/calendarmodule/images/bottomcurve.gif" width="10" height="10" /></td>
							</tr>
					</table>

	{permissions}
	{if $config->enable_categories == 1}
	{if $permissions.manage_categories == 1}
	<br />
	<a href="{link module=categories orig_module=calendarmodule action=manage}" class="mngmntlink calendar_mngmntlink">{$_TR.manage_categories}</a>
	{else}
	<br />
	<a class="mngmntlink calendar_mngmntlink" href="#" onclick="window.open('{$smarty.const.PATH_RELATIVE}popup.php?module=categories&m={$__loc->mod}&action=view&src={$__loc->src}','legend','width=200,height=200,title=no,status=no'); return false" title="{$_TR.alt_view_cat}" alt="{$_TR.alt_view_cat}">{$_TR.view_categories}</a>
	{/if}
	{/if}
	{/permissions}
	<br />
	<br />
</div>
