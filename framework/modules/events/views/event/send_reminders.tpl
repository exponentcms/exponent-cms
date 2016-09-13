{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<style type="text/css">
	{$css}
	{literal}
	.viewweek {border: none; width:100%; list-style: none; margin: 0; padding: 0;}
	.viewweek dt {line-height: 2em; border-top: 1px solid;}
	.viewweek dd {padding-left:12px;}
	.viewweek dd a.itemtitle {padding-top:4px; color:red;}
	.viewweek dd .itembody {border-bottom: 1px dashed lightgray; padding-left: 12px;}
	{/literal}
</style>
{css unique="cal" link="`$asset_path`css/calendar.css"}
{literal}

{/literal}
{/css}

<div class="module events cal-default">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	<h4 align="center">
	{if $totaldays == 1}
		<a href="{link controller=event action=showall time=$start src=$src}" title="{'Click to display in browser'|gettext}">{'Events for'|gettext} {$start|format_date:"%B %e, %Y"}</a>
	{else}
		<a href="{link controller=event action=showall time=$start src=$src}" title="{'Click to display in browser'|gettext}">{'Events for the next'|gettext} {$totaldays} {'days from'|gettext} {$start|format_date:"%B %e, %Y"}</a>
	{/if}
	</h4>
	<dl class="viewweek">
		{foreach from=$days item=events key=ts}
			{if $counts[$ts] != 0}
				<dt>
					<strong>
						<a class="itemtitle{if !empty($item->color)} {$item->color}{/if}" href="{link controller=event action=showall view=showall_Day time=$ts src=$src}" title="{'Click to display in browser'|gettext}">{$ts|format_date:"%A, %b %e"}</a>
					</strong>
				</dt>
				{foreach from=$events item=event}
                    {$catid=$event->category_id}
                    <dd>
                        <strong>
                            <a class="itemtitle" href="{link controller=event action=show date_id=$event->date_id}" title="{'Click to display in browser'|gettext}">{$event->title}</a>
                        </strong>
                        <div class="itembody">
                            {if $item->is_cancelled}<span class="cancelled-label">{'This Event Has Been Cancelled!'|gettext}</span>{br}{/if}
                            <strong>{'Time'|gettext}:</strong>
                            {if $event->is_allday == 1}
								<strong>{'All Day'|gettext}</strong>
                            {elseif $event->eventstart != $event->eventend}
                                {$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} {'to'|gettext} {$event->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
                            {else}
                                {$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
                            {/if}
                            {if $showdetail == 1}
                                {$event->body|summarize:"html":"parahtml"}
                            {/if}
                        </div>
                    </dd>
				{/foreach}
			{/if}
		{/foreach}
	</dl>
</div>