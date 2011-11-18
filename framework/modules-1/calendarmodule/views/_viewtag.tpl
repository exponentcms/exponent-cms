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

<div class="module calendar viewtag">
	<div class="module-actions">
		<a class="monthviewlink" href="{link action=viewmonth time=$time}">{'Calendar View'|gettext}</a>
		{permissions}
			{if $permissions.administrate == 1}
				&nbsp;&nbsp;|&nbsp;&nbsp;<a class="adminviewlink mngmntlink" href="{link _common=1 view='Administration' action='show_view' time=$time}">{'Administration View'|gettext}</a>
			{/if}
		{/permissions}
		&nbsp;&nbsp;|&nbsp;&nbsp;{printer_friendly_link class="printer-friendly-link" text='Printer-friendly'|gettext}
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
	<dl class="viewweek">
	{foreach from=$items item=item}
		<dt>
			<b><a class="itemtitle" href="{link action=view id=$item->id date_id=$item->eventdate->id}">{$item->title}</a></b>
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
		</dt>
		<dd>
			<strong>
			{if $item->is_allday == 1}
				{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
			{elseif $item->eventstart != $item->eventend}
				{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT} @ {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} {'to'|gettext} {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
			{else}
				{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT} @ {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
			{/if}
			</strong>
		</dd>
		<dd>
			{$item->body|summarize:html:paralinks}
		</dd>
	{foreachelse}
		<<dd><em>{'No upcoming events.'|gettext}</em></dd>
	{/foreach}
	</dl>
</div>
