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

<div class="module importexport import-select">
    <h2>{'Import'|gettext} {$import_type} {'items'|gettext}</h2>
    <blockquote>
        {'Select the'|gettext} {$import_type} {'items to import.'|gettext}
    </blockquote>
    {form action="import_process"}
        {control type=hidden name=import_type value=$import_type}
        {control type=hidden name=filename value=$filename}
        {control type=hidden name=source value=$source}
        <table class="exp-skin-table">
            <thead>
                <tr>
                    <th><input type='checkbox' name='checkall' title="{'Select All/None'|gettext}" style="margin-left: 1px;" onchange="selectAll(this.checked)"></th>
                    <th>{'Title'|gettext}</th>
                    <th>{'Dated'|gettext}</th>
                </tr>
            </thead>
            <tbody>
            {foreach $items as $key=>$item}
                <tr class="{cycle values="even,odd"}">
                    <td width="20">
                        {control type="checkbox" name="items[]" value=$key}
                    </td>
                    <td title="{$item.body|summarize:'html':'para'}">
                        {$item.title}
                    </td>
                    <td>
                        {if !empty($item.publish)}
                            {$item.publish|format_date}
                        {else}
                            {$item.created_at|format_date}
                        {/if}
                    </td>
                </tr>
            {foreachelse}
                <tr><td colspan=3>{'There doesn\'t appear to be any items you can import'|gettext}</td></tr>
            {/foreach}
            </tbody>
        </table>
        {if count($items)}
            {control type="checkbox" name="import_attached" label="Import item attachments?"|gettext checked=true value="1" description='Will also import any category, comments, or tags attached to item'|gettext}
            {control type="buttongroup" submit="Import Selected Items"|gettext cancel="Cancel"|gettext}
        {/if}
    {/form}
</div>

{script unique="aggregation"}
    function selectAll(val) {
        var checks = document.getElementsByName("items[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/script}