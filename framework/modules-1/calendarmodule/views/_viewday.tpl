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
 
{css unique="cal" link="`$smarty.const.PATH_RELATIVE`framework/modules-1/calendarmodule/assets/css/calendar.css"}

{/css}

<div class="module calendar viewday"> 
	<div class="module-actions">
		<a class="weekviewlink" href="{link action=viewweek time=$now view=_viewweek}" title="{'View Entire Week'|gettext}">{'View Week'|gettext}</a>&nbsp;&nbsp;|&nbsp;&nbsp;
		<a class="monthviewlink" href="{link action=viewmonth time=$item->eventstart}" title="{'View Entire Month'|gettext}" alt="{'View Entire Month'|gettext}">{'View Month'|gettext}</a>&nbsp;&nbsp;|&nbsp;&nbsp;
		{printer_friendly_link class="printer-friendly-link" text='Printer-friendly'|gettext}
	</div>
	<h2>
		{if $enable_ical == true}
			<a class="icallink module-actions" href="{link action=ical}" title="{'iCalendar Feed'|gettext}" alt="{'iCalendar Feed'|gettext}"></a>
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
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$prevday3}" title="{$prevday3|format_date:"%A, %B %e, %Y"}">{$prevday3|format_date:"%a"}</a>&nbsp;&nbsp;&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$prevday2}" title="{$prevday2|format_date:"%A, %B %e, %Y"}">{$prevday2|format_date:"%a"}</a>&nbsp;&nbsp;&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$prevday}" title="{$prevday|format_date:"%A, %B %e, %Y"}">{$prevday|format_date:"%a"}</a>&nbsp;&nbsp;&laquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span>{$now|format_date:"%A, %B %e, %Y"}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$nextday}" title="{$nextday|format_date:"%A, %B %e, %Y"}">{$nextday|format_date:"%a"}</a>&nbsp;&nbsp;&raquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$nextday2}" title="{$nextday2|format_date:"%A, %B %e, %Y"}">{$nextday2|format_date:"%a"}</a>&nbsp;&nbsp;&raquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$nextday3}" title="{$nextday3|format_date:"%A, %B %e, %Y"}">{$nextday3|format_date:"%a"}</a>
	</p>
	<dl class="viewweek">
		{assign var=count value=0}
		{foreach from=$events item=item}
			{assign var=count value=1}
			<dt>
				<span class="eventtitle"><a class="itemtitle calendar_mngmntlink" href="{link action=view id=$item->id date_id=$item->eventdate->id}"><b>{$item->title}</b></a></span>
				{permissions level=$smarty.const.UILEVEL_PERMISSIONS}
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
						{if $permissions.administrate == 1 || $permissions.edit == 1 ||
							$permissions.delete == 1 || $permissions.manage_approval == 1}
							{br}
						{/if}
					</div>
				{/permissions}
			</dt>
			<dd>
				<p>
					<span><b>
						{if $item->is_allday == 1}{'All Day'|gettext}{else}
							{if $item->eventstart != $item->eventend}
								{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} to {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
							{else}
								{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
							{/if}
						{/if}
					</b></span>
					{br}
					{$item->body|summarize:"html":"paralinks"}
				</p>
			</dd>
		{/foreach}
		{if $count == 0}
			<dd><em>{'No Events'|gettext}</em></dd>
		{/if}
	</dl>
</div>