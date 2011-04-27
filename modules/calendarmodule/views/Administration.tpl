{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="module calendar cal-admin"> 
	<div class="module-actions">
		<a class="monthviewlink" href="{link action=viewmonth time=$time}">{$_TR.calendar_view}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="listviewlink" href="{link _common=1 view='Monthly List' action='show_view' time=$time}">{$_TR.list_view}</a>
		&nbsp;&nbsp;|&nbsp;&nbsp;<span class="adminviewlink">{$_TR.administration_view}</span>
		&nbsp;&nbsp;|&nbsp;&nbsp;
		{printer_friendly_link class="printer-friendly-link" text=$_TR.printer_friendly}
		{br}
		<a class="listviewlink" href="{link _common=1 view='Past Events' action='show_view' time=$time}">{$_TR.past_events}</a>{br}
	</div>
	<h2>
		{if $enable_ical == true}
			<a class="icallink module-actions" href="{link action=ical}" title="{$_TR.alt_ical}" alt="{$_TR.alt_ical}">{$_TR.ical}</a>
		{/if}
		{if $moduletitle != ""}{$moduletitle} - {$_TR.administration_view}{/if}
	</h2>
	{permissions}
		<div class="module-actions">
			{if $permissions.post == 1}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
	<table cellspacing="0" cellpadding="4" border="0" width="100%" class="exp-skin-table">
		<thead>
			<tr>
				<strong><em>
				<th class="header calendarcontentheader">{$_TR.event_title}</th>
				<th class="header calendarcontentheader">{$_TR.when}</th>
				<th class="header calendarcontentheader">&nbsp;</th>
				</em></strong>
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
					{permissions}
						<div class="item-actions">
							{if $permissions.edit == 1}
								{icon action=edit record=$item date_id=$item->eventdate->id title="Edit this Event"|gettext}
							{/if}
							{if $permissions.delete == 1}
								{if $item->is_recurring == 0}
									{icon action=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
								{else}
									{icon action=delete_form class=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
								{/if}
							{/if}
						</div>
					{/permissions}
				</td>
			</tr>
		{foreachelse}
			<tr><td colspan="2" align="center"><i>{$_TR.no_events}</a></td></tr>
		{/foreach}
		</tbody>
	</table>
</div>