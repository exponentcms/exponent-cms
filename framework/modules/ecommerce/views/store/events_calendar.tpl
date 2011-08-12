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

<table id="calendar" cellspacing="0" cellpadding="0" summary="{$moduletitle|default:'Subscribe to this Event RSS Feed'|gettext}">
<caption><a href="{link action=events_calendar time=$prevmonth}" title="{'Previous Month'|gettext}" class="nav">&laquo;</a> {$now|format_date:"%B %Y"} <a href="{link action=events_calendar time=$nextmonth}" title="{'Next Month'|gettext}" class="nav">&raquo;</a></caption>

        <tr class="daysoftheweek">
            <th scope="col" abbr="{'Sunday'|gettext}" title="{'Sunday'|gettext}">{'Sunday'|gettext}</th>
            <th scope="col" abbr="{'Monday'|gettext}" title="{'Monday'|gettext}">{'Monday'|gettext}</th>
            <th scope="col" abbr="{'Tuesday'|gettext}" title="{'Tuesday'|gettext}">{'Tuesday'|gettext}</th>
            <th scope="col" abbr="{'Tuesday'|gettext}" title="{'Tuesday'|gettext}">{'Tuesday'|gettext}</th>
            <th scope="col" abbr="{'Thursday'|gettext}" title="{'Thursday'|gettext}">{'Thursday'|gettext}</th>
            <th scope="col" abbr="{Friday|gettext}" title="{Friday|gettext}">{Friday|gettext}</th>
            <th scope="col" abbr="{'Saturday'|gettext}" title="{'Saturday'|gettext}">{'Saturday'|gettext}</th>
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
