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

<table class="mini-cal">
    <tr><th colspan="7">
        {if !empty($now)}
            <strong>{$now|format_date:"%B"}</strong>
        {else}
            <strong>{"Any Month"|gettext}</strong>
        {/if}
    </th></tr>
    {foreach from=$monthly item=week key=weekid}
        <tr>
            {if is_array($week)}
            {foreach from=$week key=day item=dayinfo}
                <td>
                    {if $dayinfo.ts != -1}
                        {$today = ($dayinfo.motd->month == $monthly.currentmonth && $dayinfo.motd->day == $monthly.currentday)}
                        {if empty($dayinfo.motd)}
                            {$day}
                        {else}
                            {if $today}
                                <strong>
                            {/if}
                            <a href="{link action=show month=$dayinfo.motd->month day=$dayinfo.motd->day}" title="{expString::html2text($dayinfo.motd->body)}"><em>{$day}</em></a>
                            {if $today}
                                </strong>
                            {/if}
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
