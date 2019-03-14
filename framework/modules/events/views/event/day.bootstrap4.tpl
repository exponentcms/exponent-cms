{*
 * Copyright (c) 2004-2019 OIC Group, Inc.
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

    {$myloc=serialize($__loc)}
	<p class="caption">
		<a class="evnav module-actions" href="{link action=showall view=showall_Day time=$prev_timestamp3}" rel="{$prev_timestamp3}" title="{$prev_timestamp3|format_date:"%A, %B %e, %Y"}">{$prev_timestamp3|format_date:"%a"}</a></a><span class="d-none d-sm-inline">&#160;</span>&#160;&laquo;&#160;
		<a class="evnav module-actions" href="{link action=showall view=showall_Day time=$prev_timestamp2}" rel="{$prev_timestamp2}" title="{$prev_timestamp2|format_date:"%A, %B %e, %Y"}">{$prev_timestamp2|format_date:"%a"}</a></a><span class="d-none d-sm-inline">&#160;</span>&#160;&laquo;&#160;
		<a class="evnav module-actions" href="{link action=showall view=showall_Day time=$prev_timestamp}" rel="{$prev_timestamp}" title="{$prev_timestamp|format_date:"%A, %B %e, %Y"}">{$prev_timestamp|format_date:"%a"}</a></a><span class="d-none d-sm-inline">&#160;</span>&#160;&laquo;&#160;<span class="d-none d-sm-inline">&#160;&#160;&#160;&#160;</span>
		<strong><span class="d-none d-sm-inline">{$time|format_date:"%A, %B %e, %Y"}</span><span class="d-inline d-sm-none">{$time|format_date:"%a"}</span></strong>&#160;&#160;{printer_friendly_link view='showall_Day' text=''}{export_pdf_link view='showall_Day' text=''}</a><span class="d-none d-sm-inline">&#160;&#160;&#160;</span>&#160;&raquo;&#160;</a><span class="d-none d-sm-inline">&#160;</span>
        <input type='hidden' id='day{$__loc->src|replace:'@':'_'}' value="{$time|format_date:"%Y%m%d"}"/>
		<a class="evnav module-actions" href="{link action=showall view=showall_Day time=$next_timestamp}" rel="{$next_timestamp}" title="{$next_timestamp|format_date:"%A, %B %e, %Y"}">{$next_timestamp|format_date:"%a"}</a></a><span class="d-none d-sm-inline">&#160;</span>&#160;&raquo;&#160;
		<a class="evnav module-actions" href="{link action=showall view=showall_Day time=$next_timestamp2}" rel="{$next_timestamp2}" title="{$next_timestamp2|format_date:"%A, %B %e, %Y"}">{$next_timestamp2|format_date:"%a"}</a></a><span class="d-none d-sm-inline">&#160;</span>&#160;&raquo;&#160;
		<a class="evnav module-actions" href="{link action=showall view=showall_Day time=$next_timestamp3}" rel="{$next_timestamp3}" title="{$next_timestamp3|format_date:"%A, %B %e, %Y"}">{$next_timestamp3|format_date:"%a"}</a>
	</p>
	<dl class="viewweek">
		<strong>{$time|format_date:"%A, %B %e, %Y"}</strong>
        {$count=0}
		{foreach from=$days.$time item=item}
            {$count=1}
			<dt>
				<span class="eventtitle">
                    {if $item->is_cancelled}<span class="cancelled-label">{'This Event Has Been Cancelled!'|gettext}</span>{br}{/if}
                    <a class="itemtitle{if $item->is_cancelled} cancelled{/if}{if !empty($item->color)} {$item->color}{/if}"
                        {if substr($item->location_data,1,8) != 'calevent'}
                            href="{if $item->location_data != 'event_registration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=show title=$item->title}{/if}"
                        {/if}
                        ><strong>{$item->title}</strong>
                    </a>
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
                            {if $permissions.manage || $permissions.edit || $permissions.delete}
                                {br}
                            {/if}
                        </div>
                    {/if}
				{/permissions}
			</dt>
			<dd>
				<p>
					<span><strong>
						{if $item->is_allday == 1}{'All Day'|gettext}{else}
							{if $item->eventstart != $item->eventend}
								{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} {'to'|gettext} {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
							{else}
								{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
							{/if}
						{/if}
					</strong></span>
					{br}
					{*{$item->body|summarize:"html":"paralinks"}*}
                    {$item->body|summarize:"html":"parahtml"}
				</p>
			</dd>
		{/foreach}
		{if $count == 0}
			<dd><em>{'No Events'|gettext}</em></dd>
		{/if}
	</dl>
