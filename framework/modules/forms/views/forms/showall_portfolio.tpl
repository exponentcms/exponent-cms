{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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
    {css unique="data-view" corecss="button, tables"}

    {/css}
    {css unique="portfolio"}
    {literal}
        .forms.showall .item {
            margin-left: 10px;
            margin-right: 10px;
            padding-left: 10px;
            padding-right: 10px;
        }
        .forms.showall .category {
        	border-top: 1px black solid;
        	border-bottom: 1px black solid;
        	background-color: #fcf4ce;
        	padding-left: 4px;
        }
    {/literal}
    {/css}

    <div class="module forms showall portfolio">
        {if !empty($title)}
            <{$config.heading_level|default:'h1'}>{$title}</{$config.heading_level|default:'h1'}>
        {elseif $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}
            <{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>
        {/if}
        {if $description != ""}
            {$description}
        {elseif $config.moduledescription != ""}
            {$config.moduledescription}
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
                    {icon class=configure action=design_form id=$f->id text="Design Form"|gettext title=$f->title}
                    {if !bs()}
                        &#160;&#160;|&#160;&#160;
                    {/if}
                    {icon action=manage select=true text="Manage Forms"|gettext title=$f->title}
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
        {/permissions}
    {if $config.order_dropdown}
        {control type="dropdown" name="select_it" label=$config.order_dropdown_text|default:"Limit Records to"|gettext items=$list}
    {/if}
        {pagelinks paginate=$page top=1}
        <div style="overflow: auto; overflow-y: hidden;">
            {$cat="bad"}
            {$sort=$config.order}
            {foreach from=$page->records item=fields key=key name=fields}
                {if $cat !== $fields.$sort && $config.usecategories}
                    <{$config.item_level|default:'h2'} class="category">{if $fields.$sort!= ""}{$fields.$sort}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</{$config.item_level|default:'h2'}>
                {/if}
                <div class="item">
                    {permissions}
                    <div class="item-actions">
                        {if $permissions.edit}
                            {icon class=edit action=enterdata forms_id=$f->id id=$fields.id title='Edit this record'|gettext}
                        {/if}
                        {if $permissions.delete}
                            {icon class=delete action=delete forms_id=$f->id id=$fields.id title='Delete this record'|gettext}
                        {/if}
                    </div>
                    {/permissions}
                    {if !empty($config.report_def_showall)}
                        {eval var=$config.report_def_showall}
                        {clear}
                    {elseif !empty($config.report_def)}
                        {eval var=$config.report_def}
                        {clear}
                    {else}
                        <table class="exp-skin-table">
                            <tbody>
                                {foreach from=$fields key=fieldname item=value}
                                    <tr class="{cycle values="even,odd"}">
                                        <td>
                                            {$captions.$fieldname}
                                        </td>
                                        <td>
                                            {if $fieldname|lower == 'email' && !is_null($value) && stripos($value, '<a ') === false}
                                                <a href="mailto:{$value}">{$value}</a>
                                            {elseif $fieldname|lower == 'image' && !empty($fields.$field)}
                                                {$matches = array()}
                                                {$tmp = preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $value, $matches)}
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
                                                    {$value}
                                                {/if}
                                            {else}
                                                {$value}
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    {/if}
                    {$cat=$fields.$sort}
                </div>
            {foreachelse}
                <h4>{$config.no_records_msg|default:"No Records Found"|gettext}</h4>
            {/foreach}
        </div>
        {pagelinks paginate=$page bottom=1}
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

{if $config.order_dropdown}
{script unique="sort-submit"}
    var url = "{makeLink([controller=>forms, action=>showall, view=>showall_portfolio, src=>$__loc->src, id=>$f->id, filter=>1])}";
{literal}
    $('#select_it option:contains("{/literal}{$selected}{literal}")').prop('selected',true);
    $('#select_it').on('change',function(e){
        var loc = url.slice(0, -1)+encodeURI($("#select_it option:selected").text());
        window.location=loc;
    });
{/literal}
{/script}
{/if}