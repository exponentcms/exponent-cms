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
 
{css unique="cal" link="`$asset_path`css/calendar.css"}

{/css}

{$item = $event->event}
{$eventstart = $record->eventstart + $event->date}
{$eventend = $record->eventend + $event->date}
<div class="module events show">
	<div class="module-actions">
		<a class="dayviewlink" href="{link action=showall view=showall_Day time=$eventstart}" title="{'View Entire Day'|gettext}" alt="{'View Entire Day'|gettext}">{'View Day'|gettext}</a>
        &#160;&#160;|&#160;&#160;
		<a class="weekviewlink" href="{link action=showall view=showall_Week time=$eventstart}" title="{'View Entire Week'|gettext}" alt="{'View Entire Week'|gettext}">{'View Week'|gettext}</a>
        &#160;&#160;|&#160;&#160;
		<a class="monthviewlink" href="{link action=showall time=$eventstart}" title="{'View Entire Month'|gettext}" alt="{'View Entire Month'|gettext}">{'View Month'|gettext}</a>
		{printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'}
        {export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}
        {br}
	</div>
	<h2>
        {ical_link}
		{$item->title}
	</h2>
	{permissions}
		<div class="item-actions">
			{br}
			{if $permissions.edit == 1}
				{icon action=edit record=$item date_id=$event->id title="Edit this Event"|gettext}
			{/if}
			{if $permissions.delete == 1}
				{if $item->is_recurring == 0}
					{icon action=delete record=$item date_id=$event->id title="Delete this Event"|gettext}
				{else}
					{icon action=delete_form class=delete record=$item date_id=$event->id title="Delete this Event"|gettext}
				{/if}
			{/if}
		</div>
	{/permissions}
	{if $item->is_allday == 1}
		{$event->date|format_date}, {'All Day'|gettext}
	{else}
		{$event->date|format_date} {$eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} - {$eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
	{/if}
	<div class="bodycopy">
		{$item->body}
	</div>
    {if !empty($feedback_form)}
        {include file="email/$feedback_form.tpl"}
    {/if}
</div>
