{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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

<div class="module events list">
    {$myloc=serialize($__loc)}
    {if !$config.disable_links}
        {icon class="monthviewlink" action=showall time=$time text='Month View'|gettext}
		{if !bs()}
			{nbsp count=2}|{nbsp count=2}
	    {/if}
    {/if}
    <span class="listviewlink">{'List View'|gettext}</span><br />
	<a href="#" onclick="window.open('popup.php?controller=event&src={$__loc->src}&action=showall&view=showall_Monthly+List&template=printerfriendly&time={$time}','printer','title=no,scrollbars=no,width=800,height=600'); return false">{'Printer-friendly'|gettext}</a>
	{br}{br}
	<a href="{link action=showall view='showall_Monthly List' time=$prev_timestamp}"><img style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'navigate-left-icon.png'}" title="{'Prev'|gettext}" alt="{'Prev'|gettext}" /></a>
	<strong>{$time|format_date:"%B %Y"}</strong>
	<a href="{link action=showall view='showall_Monthly List' time=$next_timestamp}"><img style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'navigate-right-icon.png'}" title="{'Next'|gettext}" alt="{'Next'|gettext}" /></a>
	{br}{br}
	{foreach from=$days item=items key=ts}
        {$none=1}
		{if_elements array=$items}
			<div class="sectiontitle">
			{$ts|format_date}
			</div>
			{foreach from=$items item=item}
                {$none=0}
				<div class="paragraph">
                    <a class="itemtitle{if $item->is_cancelled} cancelled{/if}{if !empty($item->color)} {$item->color}{/if}"
                        {if substr($item->location_data,1,8) != 'calevent'}
                            href="{if $item->location_data != 'event_registration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=show title=$item->title}{/if}"
                        {/if}
                        title="{$item->body|summarize:"html":"para"}">{$item->title}
                    </a>
					{if $item->is_allday == 0}&#160;{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} - {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}{/if}
					{if $permissions.edit || $permissions.delete || $permissions.manage}
						<br />&#160;&#160;&#160;&#160;&#160;&#160;
					{/if}
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
				</div>
				{br}
			{/foreach}
		{/if_elements}
        {if $none == 1}
            <div class="paragraph"><strong>{'No Events'|gettext}</strong></div>
        {/if}
        {br}
	{/foreach}
	{permissions}
		{if $permissions.create}
			<div class="module-actions">
				{icon class="add" action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			</div>
		{/if}
	{/permissions}
</div>
