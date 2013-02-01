{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
{$eventstart = $item->eventstart + $event->date}
{$eventend = $item->eventend + $event->date}
<div class="module events show">
	<div class="module-actions">
		{icon class="dayviewlink" action=showall view=showall_Day time=$eventstart title='View Entire Day'|gettext text='View Day'|gettext}
        &#160;&#160;|&#160;&#160;
		{icon class="weekviewlink" action=showall view=showall_Week time=$eventstart title='View Entire Week'|gettext text='View Week'|gettext}
        &#160;&#160;|&#160;&#160;
		{icon class="monthviewlink" action=showall time=$eventstart title='View Entire Month'|gettext text='View Month'|gettext}
		{printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'}
        {export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}
        {br}
	</div>
    {if $item->expFile[0]->url != ""}
        <div class="image" style="margin: 1em 0;padding:10px;float:left;overflow: hidden;">
            {img file_id=$item->expFile[0]->id title="`$item->title`" class="large-img" id="enlarged-image"}
            {clear}
        </div>
    {/if}
    {if $item->is_cancelled}<h2 class="cancelled-label">{'This Event Has Been Cancelled!'|gettext}</h2>{/if}
	<h2{if $item->is_cancelled} class="cancelled"{/if}>
        {ical_link}
		{$item->title}
	</h2>
    {tags_assigned record=$item}
	{permissions}
		<div class="item-actions">
			{if $permissions.edit == 1}
				{icon action=edit record=$item date_id=$event->id title="Edit this Event"|gettext}
                {icon action=copy record=$item date_id=$event->id title="Copy this Event"|gettext}
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
    {elseif $event->eventstart != $event->eventend}
        {$event->date|format_date} {$eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} - {$eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
	{else}
		{$event->date|format_date} {$eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
	{/if}
	<div class="bodycopy">
		{$item->body}
	</div>
    {if !empty($feedback_form)}
        {include file="email/$feedback_form.tpl"}
    {/if}
</div>
