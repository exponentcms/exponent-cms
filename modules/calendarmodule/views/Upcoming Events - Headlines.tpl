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

<div class="module calendar upcoming-events-headlines">
	{if $enable_ical == true}
		<a class="icallink module-actions" href="{link action=ical}" title="{$_TR.alt_ical}" alt="{$_TR.alt_ical}">{$_TR.ical}</a>
	{/if}
    {if $moduletitle != ""}<h2>{$moduletitle}</h2>{/if}
	<div class="module-actions">
		{permissions}
			<p>
			{if $permissions.administrate == 1}
				<a class="adminviewlink mngmntlink" href="{link _common=1 view='Administration' action='show_view' time=$time}">{$_TR.administration_view}</a>{br}
			{/if}
			{if $permissions.post == 1}
				<a class="add" href="{link action=edit id=0}" title={"Create Event"|gettext}>{"Create Event"|gettext}</a>{br}
			{/if}
			</p>
		{/permissions} 
	</div>
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
						{if $permissions.edit == 1 || $item->permissions.edit == 1}
							{icon action=edit id=$item->id title="Edit this Event"}
						{/if}
						{if $permissions.delete == 1 || $item->permissions.delete == 1}
							{if $item->is_recurring == 0}
								{icon action=delete id=$item->id date_id=$item->eventdate->id title="Delete this Event" onclick="return confirm('Are you sure you want to delete this event?');"}
							{else}
								{icon action=delete_form class=delete id=$item->id date_id=$item->eventdate->id title="Delete this Event"}
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