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

{css unique="aggregation" corecss="tables"}

{/css}

<div class="module importexport export">
    <h2>{"Export"|gettext} {$import_type} {"Data"|gettext}</h2>
    <blockquote>
        {'Select the the module to export from.'|gettext}
    </blockquote>
    {form action="export_process"}
        {control type=hidden name=export_type value=$export_type}
        <table class="exp-skin-table">
            <thead>
                <tr>
                    <th><input type='checkbox' name='checkall' title="{'Select All/None'|gettext}" style="margin-left: 1px;" onchange="selectAll(this.checked)"></th>
                    {$modules->header_columns}
                </tr>
            </thead>
            <tbody>
            {foreach from=$modules->records item=mod}
                <tr class="{cycle values="even,odd"}">
                    <td width="20">
                        {control type="checkbox" name="export_aggregate[]" value=$mod->src}
                    </td>
                    <td>
                        {$mod->title}
                    </td>
                    <td>
                        {$mod->section}
                    </td>
                </tr>
            {foreachelse}
                <tr><td colspan=3>{'There doesn\'t appear to be any modules installed to export from'|gettext}</td></tr>
            {/foreach}
            </tbody>
        </table>
        {if count($modules->records)}
            {control type="checkbox" name="export_attached" label="Export item attachments?"|gettext checked=true value="1" description='Will also export any category, comments, or tags attached to item'|gettext}
            {control type="buttongroup" submit='Export the Selected Module\'s Items'|gettext cancel="Cancel"|gettext}
        {/if}
    {/form}
</div>

{script unique="aggregation"}
    function selectAll(val) {
        var checks = document.getElementsByName("export_aggregate[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/script}
