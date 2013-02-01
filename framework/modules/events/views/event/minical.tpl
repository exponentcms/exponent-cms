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
 
<table class="mini-cal">
    <a class="nav module-actions" href="{link action=showall view='showall_Mini-Calendar' time=$prevmonth}" rel={$prevmonth} title="{'Prev Month'|gettext}">&laquo;</a>
    &#160;&#160;{$now|format_date:"%B"}&#160;&#160;
    <a class="nav module-actions" href="{link action=showall view='showall_Mini-Calendar' time=$nextmonth}" rel={$nextmonth} title="{'Next Month'|gettext}">&raquo;</a>

    <tr class="daysoftheweek">
        {if $smarty.const.DISPLAY_START_OF_WEEK == 0}
        <th scope="col" abbr="{'Sun'|gettext}" title="{'Sunday'|gettext}">{'S'|gettext}</th>
        {/if}
        <th scope="col" abbr="{'Mon'|gettext}" title="{'Monday'|gettext}">{'M'|gettext}</th>
        <th scope="col" abbr="{'Tue'|gettext}" title="{'Tuesday'|gettext}">{'T'|gettext}</th>
        <th scope="col" abbr="{'Wed'|gettext}" title="'Wednesday'|gettext}">{'W'|gettext}</th>
        <th scope="col" abbr="{'Thu'|gettext}" title="{'Thursday'|gettext}">{'T'|gettext}</th>
        <th scope="col" abbr="{'Fri'|gettext}" title="{'Friday'|gettext}">{'F'|gettext}</th>
        <th scope="col" abbr="{'Sat'|gettext}" title="{'Saturday'|gettext}">{'S'|gettext}</th>
        {if $smarty.const.DISPLAY_START_OF_WEEK != 0}
        <th scope="col" abbr="{'Sun'|gettext}" title="{'Sunday'|gettext}">{'S'|gettext}</th>
        {/if}
    </tr>
    {foreach from=$monthly item=week key=weekid}
        <tr class="{if $currentweek == $weekid}calendar_currentweek{/if}">
            {foreach from=$week key=day item=dayinfo}
                <td>
                    {if $dayinfo.number > -1}
                        {if $dayinfo.number == 0}
                            {$day}
                        {else}
                            <a href="{link action=showall view=showall_Day time=$dayinfo.ts}" title="{$dayinfo.ts|format_date} - {$dayinfo.number} {'Event'|gettext|plural:$dayinfo.number}"><em>{$day}</em></a>
                        {/if}
                    {else}
                        &#160;
                    {/if}
                </td>
            {/foreach}
        </tr>
    {/foreach}
</table>
