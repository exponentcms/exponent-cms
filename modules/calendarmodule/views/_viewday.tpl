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
<a class="mngmntlink calendar_mngmntlink" href="{link action=viewday time=$prevday}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}left.png" title="{$_TR.prev_day}" alt="{$_TR.prev_day}" /></a>
<span style="font-weight: bold; font-size: 16px">{$now|format_date:"%A, %B %e, %Y"}</span>
<a class="mngmntlink calendar_mngmntlink" href="{link action=viewday time=$nextday}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}right.png" title="{$_TR.next_day}" alt="{$_TR.next_day}" /></a>
<br />
<a class="mngmntlink calendar_mngmntlink" href="{link action=viewweek time=$now}" title="{$_TR.alt_view_week}" alt="{$_TR.alt_view_week}">{$_TR.view_week}</a>
<br /><hr size="1" />
<table cellpadding="2" cellspacing="0" width="100%" border="0">
{assign var=count value=0}
{foreach from=$events item=event}
	{assign var=count value=1}
	<tr><td style="padding-left: 15px">
		<a class="mngmntlink calendar_mngmntlink" href="{link action=view id=$event->id date_id=$event->eventdate->id}">{$event->title}</a>
		{if $permissions.administrate == 1 || $event->permissions.administrate == 1}
			<a href="{link action=userperms int=$event->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}userperms.png" title="{$_TR.alt_userperm}" alt="{$_TR.alt_userperm}" /></a>&nbsp;
			<a href="{link action=groupperms int=$event->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}groupperms.png" title="{$_TR.alt_groupperm}" alt="{$_TR.alt_groupperm}" /></a>
		{/if}
		{if $permissions.edit == 1 || $event->permissions.edit == 1}
			{if $event->approved == 1}
			<a href="{link action=edit id=$event->id date_id=$event->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>&nbsp;
			{else}
			<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.disabled.png" title="{$_TR.alt_edit_disabled}" alt="{$_TR.alt_edit_disabled}" />
			{/if}
		{/if}
		{if $permissions.delete == 1 || $event->permissions.delete == 1}
			{if $event->approved == 1}
				{if $event->is_recurring == 0}
				<a href="{link action=delete id=$event->id}" onclick="return confirm('{$_TR.delete_confirm}');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
				{else}
				<a href="{link action=delete_form date_id=$event->eventdate->id id=$event->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
				{/if}
			{else}
			<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.disabled.png" title="{$_TR.alt_delete_disabled}" alt="{$_TR.alt_delete_disabled}" />
			{/if}
		{/if}
		{if $permissions.manage_approval == 1}
			<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=revisions_view id=$event->id}" title="{$_TR.alt_revisions}" alt="{$_TR.alt_revisions}">{$_TR.revisions}</a>
		{/if}
		<div style="padding-left: 10px">
			<b>{if $event->is_allday == 1}{$_TR.all_day}{else}
			{$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} - {$event->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
			{/if}</b><br />
			{$event->body|summarize:"html":"para"}
		</div>
	</td></tr>
{/foreach}
{if $count == 0}
	<tr><td><i>{$_TR.no_events}</i></td></tr>
{/if}
</table>