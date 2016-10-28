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

    {$myloc=serialize($__loc)}
	<table id="calendar" summary="{$moduletitle|default:'Calendar'|gettext}">
        <div class="caption">
            &laquo;&#160;
            <a class="evnav module-actions" href="{link action=eventsCalendar time=$prevmonth3}" rel="{$prevmonth3}" title="{$prevmonth3|format_date:"%B %Y"}">{$prevmonth3|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;
            <a class="evnav module-actions" href="{link action=eventsCalendar time=$prevmonth2}" rel="{$prevmonth2}" title="{$prevmonth2|format_date:"%B %Y"}">{$prevmonth2|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;
            <a class="evnav module-actions" href="{link action=eventsCalendar time=$prevmonth}" rel="{$prevmonth}" title="{$prevmonth|format_date:"%B %Y"}">{$prevmonth|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;&#160;&#160;&#160;&#160;
            <strong>{$time|format_date:"%B %Y"}</strong>&#160;&#160;&#160;&#160;&#160;&#160;&raquo;&#160;&#160;
            <a class="evnav module-actions" href="{link action=eventsCalendar time=$nextmonth}" rel="{$nextmonth}" title="{$nextmonth|format_date:"%B %Y"}">{$nextmonth|format_date:"%b"}</a>&#160;&#160;&raquo;&#160;
            <a class="evnav module-actions" href="{link action=eventsCalendar time=$nextmonth2}" rel="{$nextmonth2}" title="{$nextmonth2|format_date:"%B %Y"}">{$nextmonth2|format_date:"%b"}</a>&#160;&#160;&raquo;&#160;
            <a class="evnav module-actions" href="{link action=eventsCalendar time=$nextmonth3}" rel="{$nextmonth3}" title="{$nextmonth3|format_date:"%B %Y"}">{$nextmonth3|format_date:"%b"}</a>&#160;&#160;&raquo;
        </div>
		<tr class="daysoftheweek">
            {if $config.show_weeks}<th></th>{/if}
			{if $smarty.const.DISPLAY_START_OF_WEEK == 0}
            <th scope="col" abbr="{$daynames.med.0}" title="{$daynames.long.0}">{$daynames.long.0}</th>
            {/if}
            <th scope="col" abbr="{$daynames.med.1}" title="{$daynames.long.1}">{$daynames.long.1}</th>
            <th scope="col" abbr="{$daynames.med.2}" title="{$daynames.long.2}">{$daynames.long.2}</th>
            <th scope="col" abbr="{$daynames.med.3}" title="{$daynames.long.3}">{$daynames.long.3}</th>
            <th scope="col" abbr="{$daynames.med.4}" title="{$daynames.long.4}">{$daynames.long.4}</th>
            <th scope="col" abbr="{$daynames.med.5}" title="{$daynames.long.5}">{$daynames.long.5}</th>
            <th scope="col" abbr="{$daynames.med.6}" title="{$daynames.long.6}">{$daynames.long.6}</th>
            {if $smarty.const.DISPLAY_START_OF_WEEK != 0}
            <th scope="col" abbr="{$daynames.med.0}" title="{$daynames.long.0}">{$daynames.long.0}</th>
			{/if}
		</tr>
        {$dayts=$now}
        {$dst=false}
		{foreach from=$monthly item=week key=weeknum}
            {*{$moredata=0}*}
			{*{foreach name=w from=$week key=day item=events}*}
                {*{$number=$counts[$weeknum][$day]}*}
                {*{if $number > -1}{$moredata=1}{/if}*}
			{*{/foreach}*}
			{*{if $moredata == 1}*}
                <tr class="week{if $currentweek == $weeknum} currentweek{/if}">
                    {if $config.show_weeks}
                        <td class="week{if $currentweek == $weeknum} currentweek{/if}">{$weeknum}</td>
                    {/if}
                    {foreach name=w from=$week key=day item=items}
                        {$number=$counts[$weeknum][$day]}
                        <td {if $dayts == $today}class="today"{elseif $number == -1}class="notinmonth"{else}class="oneday"{/if}>
                            {if $number > -1}
                                {*{if $number == 0}*}
                                    <span class="number{if $dayts == $today} today{/if}">
                                        {$day}
                                    </span>
                                {*{else}*}
                                    {*<a class="number" href="{link action=showall view=showall_Day time=$dayts}" title="{$dayts|format_date:'%A, %B %e, %Y'}">{$day}</a>*}
                                {*{/if}*}
                            {/if}
                            {foreach name=e from=$items item=item}
                                <div class="calevent{if $dayts == $today} today{/if}">
                                    {if $item->is_allday}
                                        {$title = 'All Day'|gettext}
                                    {elseif $item->eventstart != $item->eventend}
                                        {$title = $item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
                                        {$title = $title|cat:' '}
                                        {$title = $title|cat:'to'|gettext}
                                        {$title = $title|cat:' '}
                                        {$title = $title|cat:($item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT)}
                                    {else}
                                        {$title = $item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
                                    {/if}
                                    {$title = $title|cat:'-'|cat:$item->body|summarize:"html":"para"}
                                    <a href="{link controller=eventregistration action=show title=$item->sef_url}" {if $config.lightbox}class="calpopevent" id="{$item->sef_url}"{/if}
                                        title="{$title}">
                                        {if $item->expFile.mainimage[0]->url != ""}
                                            <div class="image">
                                                {img file_id=$item->expFile.mainimage[0]->id alt=$item->image_alt_tag|default:"Image of `$item->title`" title=$title w=92}
                                                {clear}
                                            </div>
                                        {/if}
                                        {$item->title}
                                    </a>
                                    {permissions}
                                        <div class="item-actions">
                                            {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                                {icon img="edit.png" action=edit record=$item title="Edit this Event"|gettext}
                                                {icon img="copy.png" action=copyProduct record=$item title="Copy this Event"|gettext}
                                            {/if}
                                            {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                                                {icon img="delete.png" action=delete record=$item title="Delete this Event"|gettext}
                                            {/if}
                                        </div>
                                    {/permissions}
                                </div>
                            {/foreach}
                            {if $number != -1}{$dayts=$dayts+86400}
                                {if !$dst}
                                    {if (date('I',$now) && !date('I',$dayts))}
                                        {$dayts=$dayts+3600}
                                        {$dst=true}
                                    {elseif (!date('I',$now) && date('I',$dayts))}
                                        {$dayts=$dayts-3600}
                                        {$dst=true}
                                    {/if}
                                {/if}
                            {/if}
                        </td>
                    {/foreach}
                </tr>
			{*{/if}*}
		{/foreach}
	</table>

{if $config.lightbox}
{script unique="shadowbox" jquery='jquery.colorbox'}
{literal}
    $('.events_calendar.events a.calpopevent').click(function(e) {
        target = e.target;
        $.colorbox({
            href: EXPONENT.PATH_RELATIVE+"index.php?controller=eventregistration&action=show&ajax_action=1&title="+target.id,
            title: target.text + ' - ' + '{/literal}{'Event'|gettext}{literal}',
            maxWidth: "100%",
            onComplete : function() {
                $('img').on('load', function() {
                    $(this).colorbox.resize();
                });
            },
            close:'<i class="fa fa-close" aria-label="close modal"></i>',
            previous:'<i class="fa fa-chevron-left" aria-label="previous photo"></i>',
            next:'<i class="fa fa-chevron-right" aria-label="next photo"></i>',
        });
        e.preventDefault();
    });
{/literal}
{/script}
{/if}