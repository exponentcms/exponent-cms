{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

{css unique="manageunused" corecss="tables"}

{/css}

<div class="module administration manage-unused-tables">
    <h1>{'Deprecated/Unused Tables'|gettext}</h1>
    <h2>{$unused_tables|@count} {'unused tables found'|gettext}</h2>
    <p>
        {'The list of tables below are ones that are no longer used by Exponent. However, these tables probably'|gettext}
        {'aren\'t hurting anything.  If you do not have a good idea of what a table does or why it is there'|gettext}
        {'it is probably best to just leave it.'|gettext}
    </p>
    
    {form action=delete_unused_tables}
        <table class="exp-skin-table">
        <thead>
            <tr>
                <th>{'Delete'|gettext}?</th>
                <th>{'Table Name'|gettext}</th>
                <th># {'Rows'|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$unused_tables item=table key=basename}
            <tr class="{cycle values="even, odd"}">
                <td>{control type="checkbox" name="tables[]" label=" " value=$table->name checked=1}</td>
                <td>{$basename}</td>
                <td>{$table->rows}</td>
            </tr>
            {foreachelse}
            <tr><td>{'No unused tables were found'|gettext}.</td></tr>
            {/foreach}
        </tbody>
        </table>
        {control type="buttongroup" submit="Delete Tables"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
