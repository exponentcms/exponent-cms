{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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
    <tr><th colspan="7">
        {if empty($year)}
            <a class="evnav module-actions" href="{link action=showall view='showall_Mini-Calendar' time=$prevmonth}" rel={$prevmonth} title="{'Prev Month'|gettext}">&laquo;</a>
            &#160;&#160;{$now|format_date:"%B"}&#160;&#160;
            <a class="evnav module-actions" href="{link action=showall view='showall_Mini-Calendar' time=$nextmonth}" rel={$nextmonth} title="{'Next Month'|gettext}">&raquo;</a>
        {else}
            <a class="evnav module-actions" href="{link action=showall time=$now}" title="{'View Calendar'|gettext}">{$now|format_date:"%B %Y"}</a>
        {/if}
    </th></tr>
    <tr class="daysoftheweek">
        {if $smarty.const.DISPLAY_START_OF_WEEK == 0}
        <th scope="col" abbr="{$daynames.med.0}" title="{$daynames.long.0}">{$daynames.short.0}</th>
        {/if}
        <th scope="col" abbr="{$daynames.med.1}" title="{$daynames.long.1}">{$daynames.short.1}</th>
        <th scope="col" abbr="{$daynames.med.2}" title="{$daynames.long.2}">{$daynames.short.2}</th>
        <th scope="col" abbr="{$daynames.med.3}" title="{$daynames.long.3}">{$daynames.short.3}</th>
        <th scope="col" abbr="{$daynames.med.4}" title="{$daynames.long.4}">{$daynames.short.4}</th>
        <th scope="col" abbr="{$daynames.med.5}" title="{$daynames.long.5}">{$daynames.short.5}</th>
        <th scope="col" abbr="{$daynames.med.6}" title="{$daynames.long.6}">{$daynames.short.6}</th>
        {if $smarty.const.DISPLAY_START_OF_WEEK != 0}
        <th scope="col" abbr="{$daynames.med.0}" title="{$daynames.long.0}">{$daynames.short.0}</th>
        {/if}
    </tr>
    {foreach from=$monthly item=week key=weekid}
        <tr class="{if $currentweek == $weekid}calendar_currentweek{/if}">
            {if is_array($week)}
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
            {/if}
        </tr>
    {/foreach}
</table>
