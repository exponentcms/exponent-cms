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
	{permissions}
		<div class="module-actions">
			{if $permissions.post == 1}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
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
		{foreach from=$days item=items key=ts}
			{if_elements array=$items}
				<dt>
					<div class="sectiontitle"><strong>
						<a class="itemtitle calendar_mngmntlink" href="{link action=viewday time=$ts}">{$ts|format_date:"%A, %b %e"}</a>
					</strong></div>
				</dt>
				<dd>
					{assign var=none value=1}
					{foreach from=$items item=item}
						{assign var=none value=0}
						<div class="paragraph">
							<a class="itemtitle calendar_mngmntlink" href="{link action=view id=$item->id date_id=$item->eventdate->id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
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
							<div>
								{if $item->is_allday == 1}- All Day{else}
									{if $item->eventstart != $item->eventend}
										- {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} to {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
									{else}
										- {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
									{/if}
								{/if}
								{br}
								{$item->summary}
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