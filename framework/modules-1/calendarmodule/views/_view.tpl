{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div class="module calendar view">
	<div class="module-actions">
		<a class="dayviewlink" href="{link action=viewday time=$item->eventstart}" title="{'View Entire Day'|gettext}" alt="{'View Entire Day'|gettext}">{'View Day'|gettext}</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
		<a class="weekviewlink" href="{link action=viewweek time=$item->eventstart}" title="{'View Entire Week'|gettext}" alt="{'View Entire Week'|gettext}">{'View Week'|gettext}</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
		<a class="monthviewlink" href="{link action=viewmonth time=$item->eventstart}" title="{'View Entire Month'|gettext}" alt="{'View Entire Month'|gettext}">{'View Month'|gettext}</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
		{printer_friendly_link text='Printer-friendly'|gettext}{br}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        {export_pdf_link}
	</div>
	<h2>
		{if $enable_ical == true}
			<a class="icallink" href="{link action=ical date_id=$item->eventdate->id}" title="{'Export this Event as iCalendar file'|gettext}" alt="{'Export this Event as iCalendar file'|gettext}"> </a>
		{/if}
		{$item->title}
	</h2>
	{permissions}
		<div class="item-actions">
			{br}
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
	{if $item->is_allday == 1}
		{$item->eventstart|format_date}, {'All Day'|gettext}
	{else}
		{$item->eventstart|format_date} {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} - {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
	{/if}
	<div class="bodycopy">
		{$item->body}
	</div>
	<div class="item-actions">
		{$form}
	</div>
</div>