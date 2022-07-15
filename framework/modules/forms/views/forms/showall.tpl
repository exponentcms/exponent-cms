{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

{if !$error}
    <div class="module forms showall">
        {if !empty($title)}
            <{$config.heading_level|default:'h1'}>{$title}</{$config.heading_level|default:'h1'}>
        {/if}
        {if $description != ""}
            {$description}
        {/if}
        {permissions}
            <div class="module-actions">
                {if $permissions.create}
                    {icon class=add action=enterdata forms_id=$f->id text='Add record'|gettext}
                    {if !bs()}
                        &#160;&#160;|&#160;&#160;
                    {/if}
                {/if}
                {icon class="downloadfile" action=export_csv id=$f->id text="Export as CSV"|gettext}
                {export_pdf_link landscapepdf=1 limit=999 prepend='&#160;&#160;|&#160;&#160;'|not_bs}
                {if $permissions.manage}
                    {if !bs()}
                        &#160;&#160;|&#160;&#160;
                    {/if}
                    {icon class=configure action=design_form id=$f->id text="Design Form"|gettext}
                    {if !bs()}
                        &#160;&#160;|&#160;&#160;
                    {/if}
                    {icon action=manage select=true text="Manage Forms"|gettext}
                {/if}
                {if $permissions.delete}
                    {if !bs()}
                        &#160;&#160;|&#160;&#160;
                    {/if}
                    {icon class=delete action=delete_records forms_id=$f->id text='Purge records'|gettext onclick="return confirm('"|cat:("Are you sure you want to delete all form records?"|gettext)|cat:"');"}
                {/if}
                {if $permissions.manage}
                    {if !empty($filtered)}
                        {br}
                        {icon class="view" action=showall id=$f->id filter=1 text='View All Records'|gettext|cat:" (`$count`)" title='View all records'|gettext}
                        <span style="background-color: yellow; font-weight: bold;margin-bottom: 5px;padding: 4px;border: black 1px solid;">{'Records Filtered'|gettext} ({$page->total_records}) : '{$filtered}'</span>
                    {/if}
                {/if}
            </div>
            {br}
        {/permissions}
        {*{$page->links}*}
        <div style="overflow: auto; overflow-y: hidden;">
            <table id="forms-showall" border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        {*{$page->header_columns}*}
                        {foreach  $page->columns as $name=>$caption}
                            <th>{$caption}</th>
                        {/foreach}
                        <div class="item-actions">
                            <th>{'Actions'|gettext}</th>
                        </div>
                    </tr>
                </thead>
                <tbody>
                    {foreach $page->records as $ukey=>$fields}
                        <tr>
                            {foreach $page->columns as $field=>$caption}
                                <td>
                                    {if $field|lower == 'email' && !is_null($value) && stripos($value, '<a ') === false}
                                        <a href="mailto:{$fields.$field}">
                                    {elseif $caption@iteration == 1 && !$config.hide_view}
                                        <a href={link action=show forms_id=$f->id id=$fields.id}>
                                    {/if}
                                    {if $field|lower == 'image' && !empty($fields.$field)}
                                        {$matches = array()}
                                        {$tmp = preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $fields.$field, $matches)}
                                        {$filename1 = $matches.2.0}
                                        {if !empty($filename1)}
                                            {$filename2 = str_replace(array(URL_BASE, "//"), '/', $filename1)}
                                        {else}
                                            {$filename2 = $fields.$field}
                                        {/if}
                                        {if strlen(PATH_RELATIVE) > 1}
                                            {$base = str_replace(PATH_RELATIVE, '', BASE)}
                                        {else}
                                            {$base = rtrim(BASE, "\\/")}
                                        {/if}
                                        {$fileinfo = expFile::getImageInfo($base|cat:$filename2)}
                                        {if is_array($fileinfo) && $fileinfo.is_image == 1}
                                            {img src=$filename1 w=64}
                                        {else}
                                            {$fields.$field}
                                        {/if}
                                    {else}
                                        {$fields.$field}
                                    {/if}
                                    {if $field|lower == 'email' || ($caption@iteration == 1 && ! $config.hide_view)}
                                        </a>
                                    {/if}
                                </td>
                            {/foreach}
                            <div class="item-actions">
                                <td>
                                    {if !$config.hide_view || !$permissions.manage}
                                        {icon img="view.png" action=show forms_id=$f->id id=$fields.id title='View all data fields for this record'|gettext}
                                    {/if}
                                    {if $permissions.edit}
                                        {icon img="edit.png" action=enterdata forms_id=$f->id id=$fields.id title='Edit this record'|gettext}
                                    {/if}
                                    {if $permissions.delete}
                                        {icon img="delete.png" action=delete forms_id=$f->id id=$fields.id title='Delete this record'|gettext}
                                    {/if}
                                </td>
                            </div>
                        </tr>
                    {foreachelse}
                        <tr><h4>{$config.no_records_msg|default:"No Records Found"|gettext}</h4></tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {*{$page->links}*}
        {*<a class="{button_style}" href="{$backlink}">{'Back'|gettext}</a>*}
        {if empty($f) && $permissions.configure}
            {permissions}
                <div class="module-actions">
                    <div class="msg-queue notice" style="text-align:center">
                        <p>{'You MUST assign a form to use this module!'|gettext} {icon action="manage" select=true}</p>
                    </div>
                </div>
            {/permissions}
        {/if}
    </div>
{/if}

{if $page->records|count}
{if $config.pagelinks == 'Top Only'}
    {$pageit = '<"top"lfip>rt<"bottom"<"clear">'}
{elseif $config.pagelinks == 'Top and Bottom'}
    {$pageit = '<"top"lfip>rt<"bottom"ip<"clear">'}
{elseif $config.pagelinks == 'Bottom Only'}
    {$pageit = '<"top"lf>rt<"bottom"ip<"clear">'}
{elseif $config.pagelinks == 'Disable page links'}
    {$pageit = '<"top"lf>rt<"bottom"<"clear">'}
{/if}
{script unique="form-showall" jquery='jquery.dataTables'}
{literal}
    $(document).ready(function() {
        var tableContainer = $('#forms-showall');

        var table = tableContainer.DataTable({
            pagingType: "full_numbers",
//            dom: '{/literal}{$pageit}{literal}',  // pagination location
            // dom: 'T<"clear">lfrtip',
            scrollX: true,
            columnDefs: [
                { searchable: false, targets: [ -1 ] },
                { orderable: false, targets: [ -1 ] },
            ],
        });
    } );
{/literal}
{/script}
{/if}
