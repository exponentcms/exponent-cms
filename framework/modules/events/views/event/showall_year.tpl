{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{uniqueid prepend="cal" assign="name"}

{css unique="cal" link="`$asset_path`css/calendar.css"}
{literal}
    .mini-cal {
        width: auto;
    }
    table.mini-cal {
        height: 18rem;
    }
    .mini-cal ul li {
        list-style: none;
    }
    .mini-cal ul {
        padding-left: 0;
    }
{/literal}
{/css}

<div class="module events mini-cal annual ">
    <div class="row">
        <{$config.heading_level|default:'h1'}>
           {ical_link}
            <a class="evnav module-actions" href="{link action=showall view='showall_year' time=$prevyear}" rel={$prevyear} title="{'Prev Year'|gettext}">&laquo;</a>
           {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle} - {/if}{$now|format_date:"%Y"}
            <a class="evnav module-actions" href="{link action=showall view='showall_year' time=$nextyear}" rel={$nextyear} title="{'Next Year'|gettext}">&raquo;</a>
        </{$config.heading_level|default:'h1'}>
        {if $config.moduledescription != ""}
            {$config.moduledescription}
        {/if}
        {permissions}
            {if $permissions.create}
                <div class="module-actions">
                    {icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
                </div>
            {/if}
        {/permissions}
    </div>
{if !$config.list}
    <div class="row">
        {foreach $year as $monthid=>$monthly}
            {$now = $monthly.timefirst}
            <div id="mini-{$name}" class="col-xs-6 col-sm-4 col-md-3">
                {exp_include file='minical.tpl'}
            </div>
        {/foreach}
    </div>
{else}
    <div class="row">
        <div class="col-sm-3">
            {foreach $year as $monthid=>$monthly}
                {if $monthid <= 6}
                    <div class="row">
                 {$now = $monthly.timefirst}
                 <div id="mini-{$name}" class="col-xs-6 col-sm-4 col-md-3">
                     {exp_include file='minical.tpl'}
                 </div>
                    </div>
                {/if}
            {/foreach}
        </div>
        <div class="col-sm-6">
            <ul>
                {$more_events=0}
                {$item_number=0}
                {$header_printed = false}
                {$curmonth = ''}
                {$num_month=0}
                {foreach $items as $item}
                    {if (!$config.headcount || $item_number < $config.headcount) }
                        {$newmonth = $item->eventstart|format_date:'%B'}
                        {if $newmonth != $curmonth}
                            {* fixme - good place to put a 'column' break? *}
                            {$header_printed = false}
                            {$curmonth = $newmonth}
                            {$num_month = $num_month+1}
                        {/if}
                        <li>
                            <div class="vevent item">
                                {if $header_printed == false}
                                    <h4>
                                        <a href="{link controller=event action=showall time=$item->eventstart}"  title="{'View Entire Month'|gettext}">{$newmonth}</a>
                                    </h4>
                                    {$header_printed = true}
                                {/if}
                                <span class="event-date">
                                    <span class="dtstart"><span class="value-title" title="{date('c',$item->eventstart)}"></span></span>
                                    <span class="number">
                                        {$item->eventstart|format_date:'%e'}
                                    </span>
                                    -
                                </span>
                                <span class="event-info">
                                    {if $item->is_cancelled}<span class="cancelled-label">{'This Event Has Been Cancelled!'|gettext}</span>{br}{/if}
                                    <a class="url link{if $item->is_cancelled} cancelled{/if}{if !empty($item->color)} {$item->color}{/if}{if $config.lightbox && $item->location_data != 'eventregistration' && substr($item->location_data,1,8) != 'calevent'} ucalpopevent{elseif $config.lightbox && substr($item->location_data,1,8) == 'calevent'} uicalpopevent{/if}"
                                        {if substr($item->location_data,1,8) != 'calevent'}
                                            href="{if $item->location_data != 'event_registration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=show title=$item->title}{/if}"
                                            {if $item->date_id}id={$item->date_id}{/if}
                                        {/if}
                                        {if $config.lightbox && substr($item->location_data,1,8) == 'calevent'}rel="{$item->eventdate->date|format_date:'%A, %B %e, %Y'}"{/if}
                                        title="{$item->body|summarize:"html":"para"}"
                                        ><span class="summary">{$item->title}</span>
                                    </a>
                                </span>
                                <span class="hide">
                                    {'Location'|gettext}:
                                    <span class="location">
                                        {$smarty.const.ORGANIZATION_NAME}
                                    </span>
                                    {if !empty($item->event->expCat[0]->title)}<span
                                            class="category">{$item->event->expCat[0]->title}</span>{/if}
                                </span>
                                {permissions}
                                {if substr($item->location_data,0,3) == 'O:8'}
                                    <span class="item-actions">
                                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                            {if $myloc != $item->location_data}
                                                {if $permissions.manage}
                                                    {icon action=merge id=$item->id text=notext title="Merge Aggregated Content"|gettext size=extrasmall}
                                                {else}
                                                    {icon class=merge img='arrow_merge.png' text=notext title="Merged Content"|gettext size=extrasmall}
                                                {/if}
                                            {/if}
                                            {icon action=edit record=$item date_id=$item->date_id text=notext title="Edit this Event"|gettext size=extrasmall}
                                            {icon action=copy record=$item date_id=$item->date_id text=notext title="Copy this Event"|gettext size=extrasmall}
                                        {/if}
                                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                            {if $item->is_recurring == 0}
                                                {icon action=delete record=$item date_id=$item->date_id text=notext title="Delete this Event"|gettext size=extrasmall}
                                            {else}
                                                {icon action=delete_recurring class=delete record=$item date_id=$item->date_id text=notext title="Delete this Event"|gettext size=extrasmall}
                                            {/if}
                                        {/if}
                                    </span>
                                {/if}
                                {/permissions}
                            </div>
                        </li>
                        {$item_number=$item_number+1}
                    {else}
                        {$more_events=1}
                    {/if}
                    {foreachelse}
                    <li align="center"><em>{'No events.'|gettext}</em></li>
                {/foreach}
            </ul>
        </div>
        <div class="col-sm-3">
            {foreach $year as $monthid=>$monthly}
                {if $monthid > 6}
                <div class="row">
                 {$now = $monthly.timefirst}
                 <div id="mini-{$name}" class="col-xs-6 col-sm-4 col-md-3">
                     {exp_include file='minical.tpl'}
                 </div>
                </div>
                {/if}
            {/foreach}
        </div>
    </div>
{/if}
</div>
