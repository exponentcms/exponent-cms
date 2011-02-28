{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="calendarmodule list"> 
	<a class="monthviewlink" href="{link _common=1 view=Default action=show_view time=$time}">{$_TR.calendar_view}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="listviewlink">{$_TR.list_view}</span><br />
	<a href="#" onclick="window.open('popup.php?module=calendarmodule&src={$__loc->src}&view=Monthly List&template=printerfriendly&time={$time}','printer','title=no,scrollbars=no,width=800,height=600'); return false">{$_TR.printer_friendly}</a>
	<br />
	<br />
	<a class="mngmntlink calendar_mngmntlink" href="{link action=show_view _common=1 view='Monthly List' time=$prev_timestamp}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}left.png" title="{$_TR.previous}" alt="{$_TR.previous}" /></a>
	<b>{$time|format_date:"%B %Y"}</b>
	<a class="mngmntlink calendar_mngmntlink" href="{link action=show_view _common=1 view='Monthly List' time=$next_timestamp}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}right.png" title="{$_TR.next}" alt="{$_TR.next}" /></a>
	<br /><br />
	{foreach from=$days item=events key=ts}
		{if_elements array=$events}
		<div class="sectiontitle">
		{$ts|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
		</div>
		{assign var=none value=1}
		{foreach from=$events item=event}
			{assign var=none value=0}
			<div class="paragraph">
			<a class="mngmntlink calendar_mngmntlink" href="{link action=view id=$event->id date_id=$event->eventdate->id}" title="{$event->body|summarize:"html":"para"}">{$event->title}</a>
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
	{/permissions}
</div>
