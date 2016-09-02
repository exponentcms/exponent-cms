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

<div class="module importexport import">
    <h2>{"Import"|gettext} {$import_type} {"Data"|gettext}</h2>
    <blockquote>
        {'Select the import file and the module to import into.'|gettext}
    </blockquote>
    {form action="import_select"}
        {control type=hidden name=import_type value=$import_type}
        {control type=uploader name=import_file accept=".eql" label='EQL File to Import'|gettext}
        <label>{'Module to import into'|gettext}</label>
        <table class="exp-skin-table aggregate">
            <thead>
                <tr>
                    {$modules->header_columns}
                </tr>
            </thead>
            <tbody>
            {foreach from=$modules->records item=mod}
                <tr class="{cycle values="even,odd"}">
                    <td>
                        {control type="checkbox" name="import_aggregate[]" value=$mod->src label=$mod->title}
                    </td>
                    <td>
                        {$mod->section}
                    </td>
                </tr>
            {foreachelse}
                <tr><td colspan=3>{'There doesn\'t appear to be any modules of this type installed to import items'|gettext}</td></tr>
            {/foreach}
            </tbody>
        </table>
        {if count($modules->records)}
            {control type="buttongroup" submit="Import into Selected Module"|gettext cancel="Cancel"|gettext}
        {/if}
    {/form}
</div>

{*<div class="module importexport import">*}
    {*<h1>{"Upload Your"|gettext} {$type->basemodel_name|capitalize} {"File to Import"|gettext}</h1>*}
    {*{form action=validate}*}
        {*{control type="hidden" name="import_type" value=$type->baseclassname}*}
        {*{control type=uploader name=import_file label='File to Import'|gettext}*}
        {*{control type="buttongroup" submit="Import"|gettext|cat:"!" cancel="Cancel"|gettext}*}
    {*{/form}*}
{*</div>*}

{script unique="file-type" jquery=1}
{literal}
$(document).ready(function(){
    $('.importexport.import .aggregate input[type="checkbox"]').on('click',function() {
        $('.importexport.import .aggregate input[type="checkbox"]').not(this).prop("checked", false);
    });
});
{/literal}
{/script}
