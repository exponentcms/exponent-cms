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
    {if $moduletitle != ""}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        {if $permissions.post == 1}
            <a class="add" href="{link action=edit id=0}" title={"Create Event"|gettext}>{"Create Event"|gettext}</a>
        {/if}
    {/permissions} 
    <ul>
		{foreach from=$items item=item}
			<li>
				<a class="link" href="{link action=view id=$item->id date_id=$item->eventdate->id}">{$item->title}</a>
				<em class="date">{$item->eventstart|date_format}</em>
				{permissions}
					<div class="item-actions">
						{if $permissions.edit == 1 || $item->permissions.edit == 1}
							{icon action=edit id=$item->id title="Edit this Event"}
						{/if}
						{if $permissions.delete == 1 || $item->permissions.delete == 1}
							{if $item->is_recurring == 0}
								{icon action=delete id=$item->id title="Delete this Event" onclick="return confirm('Are you sure you want to delete this event?');"}
							{else}
								{icon action=delete_form class=delete id=$item->id title="Edit this Event"}
							{/if}
						{/if}
					</div>
				{/permissions}
			</li>
		{/foreach}
    </ul>
</div>