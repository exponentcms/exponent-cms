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
<a href="{link _common=1 view=Default action=show_view time=$time}">{$_TR.calendar_view}</a>&nbsp;&nbsp;|&nbsp;&nbsp;{$_TR.list_view}<br />

<br />
<a class="mngmntlink calendar_mngmntlink" href="{link action=show_view _common=1 view='Monthly List' time=$prev_timestamp}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}left.png" title="{$_TR.previous}" alt="{$_TR.previous}" /></a>
<b>{$time|format_date:"%B %Y"}</b>
<a class="mngmntlink calendar_mngmntlink" href="{link action=show_view _common=1 view='Monthly List' time=$next_timestamp}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}right.png" title="{$_TR.next}" alt="{$_TR.next}" /></a>
<br /><br />
{permissions level=$smarty.const.UILEVEL_PERMISSIONS}
{if $permissions.administrate == 1}
	<a href="{link action=userperms _common=1}" title="{$_TR.alt_userperms}" alt="{$_TR.alt_userperms}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}userperms.png" title="{$_TR.alt_userperm}" alt="{$_TR.alt_userperm}" /></a>&nbsp;
	<a href="{link action=groupperms _common=1}" title="{$_TR.alt_groupperms}" alt="{$_TR.alt_groupperms}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}groupperms.png" title="{$_TR.alt_groupperm}" alt="{$_TR.alt_groupperm}" /></a>
{/if}
{if $permissions.configure == 1}
	<a href="{link action=configure _common=1}" title="{$_TR.alt_configure}" alt="{$_TR.alt_configure}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}configure.png" title="{$_TR.alt_configure}" alt="{$_TR.alt_configure}" /></a>
{/if}
{/permissions}
{foreach from=$days item=events key=ts}
	{if_elements array=$events}
	<div class="sectiontitle">
	{$ts|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
	</div>
	{assign var=none value=1}
	{foreach from=$events item=event}
		{assign var=none value=0}
		<div class="paragraph">
		<a class="mngmntlink calendar_mngmntlink" href="{link action=view id=$event->id date_id=$event->eventdate->id}">{$event->title}</a>
		{if $event->is_allday == 0}&nbsp;{$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} - {$event->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}{/if}
		{if $permissions.edit == 1 || $event->permissions.edit == 1 || $permissions.delete == 1 || $event->permissions.delete == 1 || $permissions.administrate == 1 || $event->permissions.administrate == 1}
		<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		{/if}
		{permissions level=$smarty.const.UILEVEL_PERMISSIONS}
		{if $permissions.administrate == 1 || $event->permissions.administrate == 1}
		<a class="mngmntlink calendar_mngmntlink" href="{link action=userperms int=$event->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}userperms.png" title="{$_TR.alt_userperm_one}" alt="{$_TR.alt_userperm_one}" /></a>
		<a class="mngmntlink calendar_mngmntlink" href="{link action=groupperms int=$event->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}groupperms.png" title="{$_TR.alt_groupperm_one}" alt="{$_TR.alt_groupperm_one}" /></a>
		{/if}
		{/permissions}
		{permissions level=$smarty.const.UILEVEL_NORMAL}
		{if $permissions.edit == 1 || $event->permissions.edit == 1}
			{if $event->approved == 1}
			<a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=$event->id date_id=$event->eventdate->id}" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>
			{else}
			<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.disabled.png" title="{$_TR.alt_edit_disabled}" alt="{$_TR.alt_edit_disabled}" />
			{/if}
		{/if}
		{if $permissions.delete == 1 || $event->permissions.delete == 1}
			{if $event->approved == 1}
			{if $event->is_recurring == 0}
			<a class="mngmntlink calendar_mngmntlink" href="{link action=delete id=$event->id}" onclick="return confirm('{$_TR.delete_confirm}');" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
			{else}
			<a class="mngmntlink calendar_mngmntlink" href="{link action=delete_form id=$event->id date_id=$event->eventdate->id}" onclick="return confirm('{$_TR.delete_confirm}');" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
			{/if}
			{else}
			<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.disabled.png" title="{$_TR.alt_delete_disabled}" alt="{$_TR.alt_delete_disabled}" />
			{/if}
		{/if}
		{if $permissions.manage_approval == 1}
			<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=revisions_view id=$event->id}">
				{$_TR.revisions}
			</a>
		{/if}
		{/permissions}
		</div>
		<br />
	{/foreach}
	{if $none == 1}
		<div class="paragraph"><strong>{$_TR.no_events}</strong></div>
	{/if}
	<br />
	{/if_elements}
{/foreach}
{permissions level=$smarty.const.UILEVEL_NORMAL}
{if $permissions.post == 1}
<a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=0}">{$_TR.create_event}</a><br />
{/if}
{if $in_approval != 0 && $canview_approval_link == 1}
<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=summary}">{$_TR.view_approval}</a>
{/if}
{if $modconfig->enable_categories == 1}
{if $permissions.manage_categories == 1}
<br />
<a href="{link module=categories orig_module=calendarmodule action=manage}" class="mngmntlink calendar_mngmntlink">{$_TR.manage_categories}</a>
{else}
<br />
<a class="mngmntlink calendar_mngmntlink" href="#" onclick="window.open('{$smarty.const.PATH_RELATIVE}popup.php?module=categories&m={$__loc->mod}&action=view&src={$__loc->src}','legend','width=200,height=200,title=no,status=no'); return false" title="{$_TR.alt_view_cat}" alt="{$_TR.alt_view_cat}">{$_TR.view_categories}</a>
{/if}
{/if}
{/permissions}
