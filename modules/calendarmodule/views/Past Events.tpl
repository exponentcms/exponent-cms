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

<div class="calendarmodule cal-admin"> 
	<div class="itemactions">
		<a class="monthviewlink" href="{link action=viewmonth time=$time}">{$_TR.calendar_view}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="listviewlink" href="{link _common=1 view='Monthly List' action='show_view' time=$time}">{$_TR.list_view}</a>
		{permissions level=$smarty.const.UILEVEL_NORMAL}
			{if $permissions.administrate == 1}
				&nbsp;&nbsp;|&nbsp;&nbsp;<a class="adminviewlink mngmntlink" href="{link _common=1 view='Administration' action='show_view' time=$time}">{$_TR.administration_view}</a>
			{/if}
			&nbsp;&nbsp;|&nbsp;&nbsp;
			{printer_friendly_link class="printer-friendly-link" text=$_TR.printer_friendly}
			{br}
			<span class="listviewlink">{$_TR.past_events}{$config->colorize}</span>
			{if $permissions.administrate == 1}
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.delete_past}" alt="{$_TR.delete_past}" />
				<a class="mngmntlink" href="{link action=delete_all_past}" onclick="return confirm('{$_TR.delete_all_confirm}');" title="{$_TR.delete_past}">{$_TR.delete_all_past}</a>
				{br}
			{/if}
		{/permissions}
	</div>
	<h2>
		{if $enable_ical == true}
			<a class="icallink itemactions" href="{link action=ical}" title="{$_TR.alt_ical}" alt="{$_TR.alt_ical}">{$_TR.ical}</a>
		{/if}
		{if $moduletitle != ""}{$moduletitle} - {$_TR.past_events}{/if}
	</h2>
	{permissions level=$smarty.const.UILEVEL_NORMAL}
		{if $permissions.post == 1}
			<a class="addevent mngmntlink" href="{link action=edit id=0}" title="{$_TR.alt_create}" alt="{$_TR.alt_create}">{$_TR.create}</a>
		{/if}
	{/permissions}
	<table cellspacing="0" cellpadding="4" border="0" width="100%" class="exp-skin-table">
		<thead>
			<tr>
				<th class="header calendarcontentheader">{$_TR.event_title}</th>
				<th class="header calendarcontentheader">{$_TR.when}</th>
				<th class="header calendarcontentheader">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$items item=item}
			<tr class="{cycle values="odd,even"}">
				<td><a class="itemtitle calendar_mngmntlink" href="{link action=view id=$item->id date_id=$item->eventdate->id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></td>
				<td>
				{if $item->is_allday == 1}
					{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
				{else}
					{if $event->eventstart != $event->eventend}
						{$item->eventstart|format_date:"%b %e %Y"} @ {$item->eventstart|format_date:"%l:%M %p"} - {$event->eventend|format_date:"%l:%M %p"}
					{else}
						{$item->eventstart|format_date:"%b %e %Y"} @ {$item->eventstart|format_date:"%l:%M %p"}
					{/if}		
				{/if}
				</td>
				<td>
					<div class="itemactions">
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
						{/permissions}
					</div>
				</td>
			</tr>
		{foreachelse}
			<tr><td colspan="2" align="center"><i>{$_TR.no_events}</a></td></tr>
		{/foreach}
		</tbody>
	</table>
</div>