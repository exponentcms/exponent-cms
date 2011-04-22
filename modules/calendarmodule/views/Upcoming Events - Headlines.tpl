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

<div class="module calendar upcoming-events-headlines">
	{if $enable_ical == true}
		<a class="icallink module-actions" href="{link action=ical}" title="{$_TR.alt_ical}" alt="{$_TR.alt_ical}">{$_TR.ical}</a>
	{/if}
    {if $moduletitle != ""}<h2>{$moduletitle}</h2>{/if}
	{permissions}
		<div class="module-actions">
			<p>
			{if $permissions.administrate == 1}
				<a class="adminviewlink mngmntlink" href="{link _common=1 view='Administration' action='show_view' time=$time}">{$_TR.administration_view}</a>{br}
			{/if}
			{if $permissions.post == 1}
				{icon class="add" action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
			</p>
		</div>
	{/permissions} 
    <ul>
		{foreach from=$items item=item}
			<li>
				<a class="link" href="{link action=view id=$item->id date_id=$item->eventdate->id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
				<em class="date">
					{if $item->is_allday == 1}
						{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
					{else}
						{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT} @ {$item->eventstart|format_date:"%l:%M %p"}
					{/if}
				</em>
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
			</li>
		{foreachelse}
			<li align="center"><i>{$_TR.no_event}</i></li>
		{/foreach}
    </ul>
</div>