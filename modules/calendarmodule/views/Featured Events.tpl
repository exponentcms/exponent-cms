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

<div class="calendarmodule cal-admin">  
	<a href="{link _common=1 view=Default action=show_view}">Month View</a>{br}
	<h2>
	{if $enable_rss == true}
		<a href="{rsslink}"><img src="{$smarty.const.ICON_RELATIVE}rss-feed.gif" title="{$_TR.alt_rssfeed}" alt="{$_TR.alt_rssfeed}" /></a>
	{/if}
	{if $moduletitle != ""}{$moduletitle}{/if}
	</h2>
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
	{foreach from=$items item=item}
	 {if $item->is_featured == 1}
		<tr>
			<td width="80" style="padding:3px 0px 3px 0px;">
				{if $item->image_path}
					<img src="{$item->image_path}" border="0" width="80" height="80"/>
				{else}
					&nbsp;
				{/if}
			</td>
			<td style="padding:3px 10px 3px 10px;">
				<table width=100% cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<a class="mngmntlink calendar_mngmntlink" href="{link action=view id=$item->id date_id=$item->eventdate->id}">{$item->title}</a>
					</td>
				</tr>
				<tr>
							<td colspan="2">
									{$item->body|summarize:html:paralinks}
							</td>
					</tr>
				<tr>
					<td align="right">
				{permissions level=$smarty.const.UILEVEL_PERMISSIONS}
					{if $permissions.administrate == 1 || $item->permissions.administrate == 1}
						<a class="mngmntlink calendar_mngmntlink" href="{link action=userperms int=$item->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}userperms.png" title="{$_TR.alt_userperm_one}" alt="{$_TR.alt_userperm_one}" /></a>
						<a class="mngmntlink calendar_mngmntlink" href="{link action=groupperms int=$item->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}groupperms.png" title="{$_TR.alt_groupperm_one}" alt="{$_TR.alt_groupperm_one}" /></a>
					{/if}
				{/permissions}
				{permissions level=$smarty.const.UILEVEL_NORMAL}
					{if $permissions.edit == 1 || $item->permissions.edit == 1}
						{if $item->approved == 1}
						<a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=$item->id date_id=$item->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>
						{else}
						<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.disabled.png" title="{$_TR.alt_edit_disabled}" alt="{$_TR.alt_edit_disabled}" />
						{/if}
					{/if}
					{if $permissions.delete == 1 || $item->permissions.delete == 1}
						{if $item->approved == 1}
						{if $item->is_recurring == 0}
						<a class="mngmntlink calendar_mngmntlink" href="{link action=delete id=$item->id}" onclick="return confirm('{$_TR.delete_confirm}');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
						{else}
						<a class="mngmntlink calendar_mngmntlink" href="{link action=delete_form id=$item->id date_id=$item->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
						{/if}
						{else}
						<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.disabled.png" title="{$_TR.alt_delete_disabled}" alt="{$_TR.alt_delete_disabled}" />
						{/if}
					{/if}
					{if $permissions.manage_approval == 1}
						<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=revisions_view id=$item->id}" title="{$_TR.alt_revisions}"><img class="mngmnt_icon" src="{$smarty.const.ICON_RELATIVE}revisions.png" title="{$_TR.alt_revisions}" alt="{$_TR.alt_revisions}"/></a>
					{/if}
				{/permissions}
					</td>
				</tr>
				</table>
			</td>
		</tr>
	 {/if}
	{foreachelse}
	<table cellspacing="0" cellpadding="4" border="1" width="100%">
		<tr><td align="center"><i>{$_TR.no_event}</i></td></tr>
	</table>
	{/foreach}
	</table>
	{permissions level=$smarty.const.UILEVEL_NORMAL}
	{if $permissions.post == 1}
	<a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=0}" title="{$_TR.alt_create}" alt="{$_TR.alt_create}">{$_TR.create}</a>
	{/if}
	{br}
	{if $in_approval != 0 && $canview_approval_link == 1}
	<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=summary}" title="{$_TR.alt_approval}" alt="{$_TR.alt_approval}">{$_TR.approval}</a>
	{/if}
	{if $config->enable_categories == 1}
	{if $permissions.administrate == 1}
	{br}
	<a href="{link module=categories orig_module=calendarmodule action=manage}" class="mngmntlink cats">{$_TR.manage_categories}</a>
	{else}
	{br}
	<a class="cats" href="#" onclick="window.open('{$smarty.const.PATH_RELATIVE}popup.php?module=categories&m={$__loc->mod}&action=view&src={$__loc->src}','legend','width=200,height=200,title=no,status=no'); return false" title="{$_TR.alt_view_cat}" alt="{$_TR.alt_view_cat}">{$_TR.view_categories}</a>
	{/if}
	{/if}
	{/permissions}
</div>