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
{literal}
	.viewweek {border: none;width:100%;list-style: none;margin: 0;padding: 0;}
	.viewweek dt {line-height: 2em; border-top: 1px solid;}
{/literal}
{/css}
 
<div class="module events cal-default">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	<h4 align="center">
	{if $totaldays == 1}
		<a href="{link controller=event action=showall time=$start}">{'Events for'|gettext} {$start|format_date:"%B %e, %Y"}</a>
	{else}
		<a href="{link controller=event action=showall time=$start}">{'Events for the next'|gettext} {$totaldays} {'days from'|gettext} {$start|format_date:"%B %e, %Y"}</a>
	{/if}
	</h4>
	<dl class="viewweek">
		{foreach from=$days item=events key=ts}
			{if $counts[$ts] != 0}
				<dt>
					<strong>
						<a class="itemtitle{if $config.usecategories && !empty($item->color)} {$item->color}{/if}" href="{link controller=event action=showall view=showall_Day time=$ts}">{$ts|format_date:"%A, %b %e"}</a>
					</strong>
				</dt>
				{foreach from=$events item=event}
                    {if !$event->is_cancelled}
                        {$catid=$event->category_id}
                        <dd>
                            <strong>
                                <a class="itemtitle" href="{link controller=event action=show date_id=$event->date_id}">{$event->title}</a>
                            </strong>
                            <div>
                                &#160;-&#160;
                                {if $event->is_allday == 1}
                                    {'All Day'|gettext}
                                {elseif $event->eventstart != $event->eventend}
                                    {$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} {'to'|gettext} {$event->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
                                {else}
                                    {$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
                                {/if}
                                {if $showdetail == 1}
                                    {*&#160;-&#160;{$event->body|summarize:"html":"paralinks"}*}
                                    &#160;-&#160;{$event->body|summarize:"html":"parahtml"}
                                {/if}
                                {br}
                            </div>
                        </dd>
                    {/if}
				{/foreach}
			{/if}
		{/foreach}
	</dl>
</div>