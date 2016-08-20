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

<div class="module event import">
    <h2>{"Import Events"|gettext}</h2>
    <blockquote>
        {'Select the event data and the module to import into.'|gettext}
    </blockquote>
    {form action="import_select"}
        <div id="alt-control-file" class="alt-control">
            <div class="control"><label class="label">{'Type of Media'|gettext}</label></div>
            <div class="alt-body">
                {control type=radiogroup columns=2 name="file_type" items="Uploaded File,External Feed"|gettxtlist values="file,ext_feed" default=$record->file_type|default:"file"}
                <div id="file-div" class="alt-item" style="display:none;">
                    {control type=uploader name=import_file accept=".ics" label='.ics File to Import'|gettext}
                </div>
                <div id="ext_feed-div" class="alt-item" style="display:none;">
                    {control type=url name=ext_feed label="External .ics Feed URL"|gettext value=$record->ext_file size=100}
                </div>
            </div>
        </div>
        {$begin = expDateTime::startOfYearTimestamp(time())}
        {control type="yuidatetimecontrol" name="begin" label="Start Date"|gettext showtime=false edit_text="Beginning of This Year" checked=true value=$begin}
        {$end = expDateTime::endOfYearTimestamp(expDateTime::endOfYearTimestamp(time()) + 2)}
        {control type="yuidatetimecontrol" name="end" label="End Date"|gettext showtime=false edit_text="End of Next Year" checked=true value=$end}
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
            {control type="buttongroup" submit="Import into Events Module"|gettext cancel="Cancel"|gettext}
        {/if}
    {/form}
</div>

{script unique="file-type" jquery=1}
{literal}
$(document).ready(function(){
    var radioSwitcher_file = $('#alt-control-file input[type="radio"]');
    radioSwitcher_file.on('click', function(e){
        $("#alt-control-file .alt-item").css('display', 'none');
        var curdiv = $("#" + e.target.value + "-div");
        curdiv.css('display', 'block');
    });

    radioSwitcher_file.each(function(k, node){
        if(node.checked == true){
            $(node).trigger('click');
        }
    });

    $('.event.import .aggregate input[type="checkbox"]').on('click',function() {
        $('.event.import .aggregate input[type="checkbox"]').not(this).prop("checked", false);
    });
});
{/literal}
{/script}
