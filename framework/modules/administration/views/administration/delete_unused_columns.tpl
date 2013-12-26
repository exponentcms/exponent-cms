{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{css unique="install-tables" corecss="tables"}

{/css}

<div class="form_header">
 	<h1>{'Deleting Unneeded Database Columns'|gettext}</h1>
 	<blockquote>
 		{'Exponent has deleted unneeded database columns from existing tables.  Shown below is a summary of the actions that occurred.'|gettext}
    </blockquote>
</div>
<table cellpadding="2" cellspacing="0" width="100%" border="0" class="exp-skin-table">
    <thead>
        <tr>
            <th>{'Table Name'|gettext}</th>
            <th>{'Status'|gettext}</th>
        </tr>
    </thead>
    <tbody>
        {$line = 0}
            {foreach from=$status key=table item=statusnum}
                {if ($statusnum != $smarty.const.DATABASE_TABLE_EXISTED)}
                    <tr class="{cycle values='odd,even'}">
                        <td>
                            {$table}
                        </td>
                        <td>
                            {if $statusnum == $smarty.const.DATABASE_TABLE_INSTALLED}
                                <div style="color: green; font-weight: bold">
                                    {'Succeeded'|gettext}
                                </div>
                            {elseif $statusnum == $smarty.const.DATABASE_TABLE_FAILED}
                                <div style="color: red; font-weight: bold">
                                    {'Failed'|gettext}
                                </div>
                            {elseif $statusnum == $smarty.const.DATABASE_TABLE_ALTERED}
                                <div style="color: green; font-weight: bold">
                                    {'Altered Existing'|gettext}
                                </div>
                            {elseif $statusnum == $smarty.const.TABLE_ALTER_FAILED}
                                <div style="color: red; font-weight: bold">
                                    {'Altering Failed'|gettext}
                                </div>
                            {/if}
                        </td>
                    </tr>
                    {$line++}
                {/if}
            {/foreach}
        {if $line == 0}
            <tr><td style="color: green; font-weight: bold">
                {"No Tables Were Changed!"|gettext}
            </td></tr>
        {/if}
    </tbody>
</table>