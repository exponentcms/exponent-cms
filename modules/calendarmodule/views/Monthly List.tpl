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

<div class="module calendar monthly">
	<div class="module-actions">
		<a class="monthviewlink" href="{link action=viewmonth time=$time}">{$_TR.calendar_view}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="listviewlink"></span>{$_TR.list_view}
		{permissions}
			{if $permissions.administrate == 1}
				&nbsp;&nbsp;|&nbsp;&nbsp;<a class="adminviewlink mngmntlink" href="{link _common=1 view='Administration' action='show_view' time=$time}">{$_TR.administration_view}</a>
			{/if}
			&nbsp;&nbsp;|&nbsp;&nbsp;
			{printer_friendly_link class="printer-friendly-link" text=$_TR.printer_friendly}
			{br}
		{/permissions}
	</div>
	<h2>
		{if $enable_ical == true}
			<a class="icallink module-actions" href="{link action=ical}" title="{$_TR.alt_ical}" alt="{$_TR.alt_ical}">{$_TR.ical}</a>
		{/if}
		{if $moduletitle != ""}{$moduletitle}{/if}
	</h2>
	<div class="module-actions">
		{permissions}
			{if $permissions.post == 1}
				<a class="addevent mngmntlink" href="{link action=edit id=0}">{$_TR.create_event}</a>
			{/if}
		{/permissions}
	</div>
	<p class="caption">
		&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$prev_timestamp3}" title="{$prev_timestamp3|format_date:"%B %Y"}">{$prev_timestamp3|format_date:"%b"}</a>&nbsp;&nbsp;&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$prev_timestamp2}" title="{$prev_timestamp2|format_date:"%B %Y"}">{$prev_timestamp2|format_date:"%b"}</a>&nbsp;&nbsp;&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$prev_timestamp}" title="{$prev_timestamp|format_date:"%B %Y"}">{$prev_timestamp|format_date:"%b"}</a>&nbsp;&nbsp;&laquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<b>{$time|format_date:"%B %Y"}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$next_timestamp}" title="{$next_timestamp|format_date:"%B %Y"}">{$next_timestamp|format_date:"%b"}</a>&nbsp;&nbsp;&raquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$next_timestamp2}" title="{$next_timestamp2|format_date:"%B %Y"}">{$next_timestamp2|format_date:"%b"}</a>&nbsp;&nbsp;&raquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$next_timestamp3}" title="{$next_timestamp3|format_date:"%B %Y"}">{$next_timestamp3|format_date:"%b"}</a>&nbsp;&nbsp;&raquo;
	</p>
	<dl class="viewweek">
		{foreach from=$days item=events key=ts}
			{if_elements array=$events}
				<dt>
					<div class="sectiontitle"><strong>
						<a class="itemtitle calendar_mngmntlink" href="{link action=viewday time=$ts}">{$ts|format_date:"%A, %b %e"}</a>
					</strong></div>
				</dt>
				<dd>
					{assign var=none value=1}
					{foreach from=$events item=event}
						{assign var=none value=0}
						<div class="paragraph">
							<a class="itemtitle calendar_mngmntlink" href="{link action=view id=$event->id date_id=$event->eventdate->id}" title="{$event->body|summarize:"html":"para"}">{$event->title}</a>
							<div class="item-actions">
								{if $permissions.edit == 1 || $event->permissions.edit == 1 || $permissions.delete == 1 || $event->permissions.delete == 1 || $permissions.administrate == 1 || $event->permissions.administrate == 1 || $permissions.manage_approval == 1}
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
							<div>
								{if $event->is_allday == 1}- All Day{else}
									{if $event->eventstart != $event->eventend}
										- {$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} to {$event->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
									{else}
										- {$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
									{/if}
								{/if}
								{br}
								{$event->summary}
							</div>
						</div>
					{/foreach}
				</dd>
				{if $none == 1}
					<div class="paragraph"><dd><strong>{$_TR.no_events}</strong></dd></div>
				{/if}
				{br}
			{/if_elements}
		{/foreach}
	</dl>
</div>