{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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


<div class="store showallCalendar">
	
<h1>{if $moduletitle}{$moduletitle}{/if}</h1>

<table id="calendar" cellspacing="0" cellpadding="0" summary="{$moduletitle|default:$_TR.default_summery}">
<caption><a href="{link action=events_calendar time=$prevmonth}" title="{$_TR.alt_previous}" class="nav">&laquo;</a> {$now|format_date:"%B %Y"} <a href="{link action=events_calendar time=$nextmonth}" title="{$_TR.alt_next}" class="nav">&raquo;</a></caption>

        <tr class="daysoftheweek">
            <th scope="col" abbr="{$_TR.sunday}" title="{$_TR.sunday}">{$_TR.sunday}</th>
            <th scope="col" abbr="{$_TR.monday}" title="{$_TR.monday}">{$_TR.monday}</th>
            <th scope="col" abbr="{$_TR.tuesday}" title="{$_TR.tuesday}">{$_TR.tuesday}</th>
            <th scope="col" abbr="{$_TR.wednesday}" title="{$_TR.wednesday}">{$_TR.wednesday}</th>
            <th scope="col" abbr="{$_TR.thursday}" title="{$_TR.thursday}">{$_TR.thursday}</th>
            <th scope="col" abbr="{$_TR.friday}" title="{$_TR.friday}">{$_TR.friday}</th>
            <th scope="col" abbr="{$_TR.saturday}" title="{$_TR.saturday}">{$_TR.saturday}</th>
        </tr>
    {math equation="x-86400" x=$now assign=dayts}
    {foreach from=$monthly item=week key=weeknum}
        <tr class="{if $currentweek == $weeknum} currentweek{/if}">
        {foreach name=w from=$week key=day item=events}
            {assign var=number value=$counts[$weeknum][$day]}
            <td {if $number == -1}class="notinmonth" {/if}>
                {if $number != -1}{math equation="x+86400" x=$dayts assign=dayts}{/if}
                {if $number > -1}
                    <span class="number">
                        {$day}
                    </span>
                {/if}
                {foreach name=e from=$events item=event}
                    <a class="calevent" class="mngmntlink calendar_mngmntlink" href="{link action=showByTitle title=$event->sef_url}">{$event->title}</a>
                {/foreach}
            </td>
        {/foreach}
        </tr>
    {/foreach}
</table>
</div>
