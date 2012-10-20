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

<div class="module events upcoming-events-headlines">
    <h2>
        {ical_link}
        {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}{/if}
    </h2>
    {$myloc=serialize($__loc)}
	{permissions}
		<div class="module-actions">
			<p>
			{if $permissions.manage == 1}
				<a class="adminviewlink mngmntlink" href="{link action=showall view=showall_Administration time=$time}">{'Administration View'|gettext}</a>{br}
			{/if}
			{if $permissions.create == 1}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
			</p>
		</div>
	{/permissions} 
    <ul>
		{*{assign var=more_events value=0}	*}
		{*{assign var=item_number value=0}	*}
        {$more_events=0}
        {$item_number=0}
		{foreach from=$items item=item}
			{if (!$__viewconfig.num_events || $item_number < $__viewconfig.num_events) }	
				<li>
                    <a class="link"
                        {if substr($item->location_data,1,8) != 'calevent'}
                            href="{if $item->location_data != 'event_registration'}{link action=show id=$item->date_id}{else}{link controller=eventregistration action=showByTitle title=$item->title}{/if}"
                        {/if}
                        >{$item->title}
                    </a>
					<em class="date">
						{if $item->is_allday == 1}
							{$item->eventstart|format_date}
						{else}
							{$item->eventstart|format_date} @ {$item->eventstart|format_date:"%l:%M %p"}
						{/if}
					</em>
					{permissions}
                        {if substr($item->location_data,0,3) == 'O:8'}
                            <div class="item-actions">
                                {if $permissions.edit == 1}
                                    {if $myloc != $item->location_data}
                                        {if $permissions.manage == 1}
                                            {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                        {else}
                                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                        {/if}
                                    {/if}
                                    {icon action=edit record=$item date_id=$item->date_id title="Edit this Event"|gettext}
                                {/if}
                                {if $permissions.delete == 1}
                                    {if $item->is_recurring == 0}
                                        {icon action=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                    {else}
                                        {icon action=delete_form class=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                    {/if}
                                {/if}
                            </div>
                        {/if}
					{/permissions}
				</li>
				{*{assign var=item_number value=$item_number+1}*}
                {$item_number=$item_number+1}
			{else}
				{*{assign var=more_events value=1}	*}
                {$more_events=1}
			{/if}
		{foreachelse}
			<li align="center"><em>{'No upcoming events.'|gettext}</em></li>
		{/foreach}
    </ul>
	<p>
		{if $more_events == 1}
			<a class="mngmntlink monthviewlink module-actions" href="{link action=showall view='showall_Upcoming Events' time=$time}">{'More Events...'|gettext}</a>{br}
		{/if}
	</p>
</div>