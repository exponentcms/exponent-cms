{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
    <{$config.heading_level|default:'h2'}>
        {ical_link}
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}{/if}
    </{$config.heading_level|default:'h2'}>
    {$myloc=serialize($__loc)}
	{permissions}
		<div class="module-actions">
			<p>
			{if $permissions.manage}
				{icon class="adminviewlink" action=showall view=showall_Administration time=$time text='Administration View'|gettext}{br}
			{/if}
			{if $permissions.create}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
			</p>
		</div>
	{/permissions} 
    <ul>
        {$more_events=0}
        {$item_number=0}
		{foreach from=$items item=item}
			{if (!$config.headcount || $item_number < $config.headcount) }
                <div class="vevent">
				<li>
                    {if $item->is_cancelled}<span class="cancelled-label">{'This Event Has Been Cancelled!'|gettext}</span>{br}{/if}
                    <a class="url link{if $item->is_cancelled} cancelled{/if}{if !empty($item->color)} {$item->color}{/if}"
                        {if substr($item->location_data,1,8) != 'calevent'}
                            href="{if $item->location_data != 'event_registration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=show title=$item->title}{/if}"
                        {/if}
                       title="{$item->body|summarize:"html":"para"}"
                        ><span class="summary">{$item->title}</span>
                    </a>
					<em class="date">
						{if $item->is_allday == 1}
                            <span class="dtstart">{$item->eventstart|format_date}<span class="value-title" title="{date('c',$item->eventstart)}"></span></span>
						{else}
                            <span class="dtstart">{$item->eventstart|format_date} @ {$item->eventstart|format_date:"%l:%M %p"}<span class="value-title" title="{date('c',$item->eventstart)}"></span></span>
						{/if}
					</em>
                    <span class="hide">
                        {'Location'|gettext}:
                        <span class="location">
                            {$smarty.const.ORGANIZATION_NAME}
                        </span>
                        {if !empty($item->event->expCat[0]->title)}<span class="category">{$item->event->expCat[0]->title}</span>{/if}
                    </span>

					{permissions}
                        {if substr($item->location_data,0,3) == 'O:8'}
                            <div class="item-actions">
                                {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                    {if $myloc != $item->location_data}
                                        {if $permissions.manage}
                                            {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                        {else}
                                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                        {/if}
                                    {/if}
                                    {icon action=edit record=$item date_id=$item->date_id title="Edit this Event"|gettext}
                                    {icon action=copy record=$item date_id=$item->date_id title="Copy this Event"|gettext}
                                {/if}
                                {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                    {if $item->is_recurring == 0}
                                        {icon action=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                    {else}
                                        {icon action=delete_recurring class=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                    {/if}
                                {/if}
                            </div>
                        {/if}
					{/permissions}
				</li>
                </div>
                {$item_number=$item_number+1}
			{else}
                {$more_events=1}
			{/if}
		{foreachelse}
			<li align="center"><em>{'No upcoming events.'|gettext}</em></li>
		{/foreach}
    </ul>
	<p>
		{if $more_events == 1}
			<a class="monthviewlink module-actions" href="{link action=showall view='showall_Upcoming Events' time=$time}">{'More Events...'|gettext}</a>{br}
		{/if}
	</p>
</div>