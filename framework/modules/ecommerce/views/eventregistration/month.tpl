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

    {$myloc=serialize($__loc)}
	<table id="calendar" summary="{$moduletitle|default:'Calendar'|gettext}">
        <div class="caption">
            &laquo;&#160;
            <a class="nav module-actions" href="{link action=eventsCalendar time=$prevmonth3}" rel="{$prevmonth3}" title="{$prevmonth3|format_date:"%B %Y"}">{$prevmonth3|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;
            <a class="nav module-actions" href="{link action=eventsCalendar time=$prevmonth2}" rel="{$prevmonth2}" title="{$prevmonth2|format_date:"%B %Y"}">{$prevmonth2|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;
            <a class="nav module-actions" href="{link action=eventsCalendar time=$prevmonth}" rel="{$prevmonth}" title="{$prevmonth|format_date:"%B %Y"}">{$prevmonth|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;&#160;&#160;&#160;&#160;
            <strong>{$time|format_date:"%B %Y"}</strong>&#160;&#160;&#160;&#160;&#160;&#160;&raquo;&#160;&#160;
            <a class="nav module-actions" href="{link action=eventsCalendar time=$nextmonth}" rel="{$nextmonth}" title="{$nextmonth|format_date:"%B %Y"}">{$nextmonth|format_date:"%b"}</a>&#160;&#160;&raquo;&#160;
            <a class="nav module-actions" href="{link action=eventsCalendar time=$nextmonth2}" rel="{$nextmonth2}" title="{$nextmonth2|format_date:"%B %Y"}">{$nextmonth2|format_date:"%b"}</a>&#160;&#160;&raquo;&#160;
            <a class="nav module-actions" href="{link action=eventsCalendar time=$nextmonth3}" rel="{$nextmonth3}" title="{$nextmonth3|format_date:"%B %Y"}">{$nextmonth3|format_date:"%b"}</a>&#160;&#160;&raquo;
        </div>
		<tr class="daysoftheweek">
            {if $config.show_weeks}<th></th>{/if}
			{if $smarty.const.DISPLAY_START_OF_WEEK == 0}
			<th scope="col" abbr="{'Sun'|gettext}" title="'Sunday'|gettext}">{'Sunday'|gettext}</th>
			{/if}
			<th scope="col" abbr="{'Mon'|gettext}" title="{'Monday'|gettext}">{'Monday'|gettext}</th>
			<th scope="col" abbr="{'Tue'|gettext}" title="{'Tuesday'|gettext}">{'Tuesday'|gettext}</th>
			<th scope="col" abbr="{'Wed'|gettext}" title="{'Wednesday'|gettext}">{'Wednesday'|gettext}</th>
			<th scope="col" abbr="{'Thu'|gettext}" title="{'Thursday'|gettext}">{'Thursday'|gettext}</th>
			<th scope="col" abbr="{'Fri'|gettext}" title="{'Friday'|gettext}">{'Friday'|gettext}</th>
			<th scope="col" abbr="{'Sat'|gettext}" title="{'Saturday'|gettext}">{'Saturday'|gettext}</th>
			{if $smarty.const.DISPLAY_START_OF_WEEK != 0}
			<th scope="col" abbr="{'Sun'|gettext}" title="{'Sunday'|gettext}">{'Sunday'|gettext}</th>
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
                                                {img file_id=$item->expFile.mainimage[0]->id alt=$item->image_alt_tag|default:"Image of `$item->title`" title=$title class="large-img" id="enlarged-image" w=92}
                                                {clear}
                                            </div>
                                        {/if}
                                        {$item->title}
                                    </a>
                                    {permissions}
                                        <div class="item-actions">
                                            {if $permissions.edit == 1}
                                                {icon img="edit.png" action=edit record=$item title="Edit this Event"|gettext}
                                                {icon img="copy.png" action=copyProduct record=$item title="Copy this Event"|gettext}
                                            {/if}
                                            {if $permissions.delete == 1}
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
{css unique="cal-lightbox" link="`$smarty.const.PATH_RELATIVE`framework/modules/events/assets/css/lightbox.css"}

{/css}

{*FIXME convert to yui3*}
{script unique="shadowbox" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules = {
        'yui2-lightbox' : {
            fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/events/assets/js/lightbox.js',
            requires : ['yui2-dom','yui2-event','yui2-connectioncore','yui2-json','yui2-selector','yui2-animation']
        }
    }
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-container','yui2-yahoo-dom-event','yui2-lightbox', function(Y) {
        var YAHOO = Y.YUI2;
        var lb2 = new this.EXPONENT.Lightbox(
            {
                animate: true,
                maxWidth: 650
            }
        );
        YAHOO.util.Event.addListener(YAHOO.util.Selector.query("a.calpopevent"), "click", function (e) {
            YAHOO.util.Event.preventDefault(e);
            target = YAHOO.util.Event.getTarget(e);
            lb2.cfg.contentURL = EXPONENT.PATH_RELATIVE+"index.php?controller=eventregistration&action=show&ajax_action=1&title="+target.id;
            lb2.show(e);
        }, lb2, true);
    });
{/literal}
{/script}
{/if}