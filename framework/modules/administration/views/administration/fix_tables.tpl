{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="fix-tables" corecss="tables"}

{/css}

<h1>{'Fix Mixed Case Database Table Names'|gettext}</h1>
{if $tables}
    <p>{'The following tables were renamed to mixed case'|gettext}</p>
    <table  class="exp-skin-table" cellspacing="0" cellpadding="0" border="0" width="100%">
        <thead>
            <tr>
                <th>{'Table Name'|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$tables item=table}
                <tr class="{cycle values="even, odd"}">
                    <td>{$table}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <h4>{'There were no mis-named tables found'|gettext}</h4>
{/if}