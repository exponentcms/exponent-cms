{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
 * Written and Designed by James Hunt
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
    {if $is_email == 1}
        <style type="text/css">
            {$css}
        </style>
    {else}
        {css unique="default-report" corecss="tables,button"}

        {/css}
    {/if}
    <div class="module forms show">
        <div class="item-actions">
        {if !$is_email && ($prev || $next) && ($config.pagelinks == "Top and Bottom" || $config.pagelinks == "Top Only")}
            {clear}
            <span style="float:left">
                {if $prev}
                    {icon img='page_prev.png' action=show forms_id=$f->id id=$prev->id title='Previous Record'|gettext}
                {else}
                    {icon img='page_prev.png' title='Previous Record'|gettext}
                {/if}
            </span>
        {/if}
        {permissions}
            {if $permissions.create}
                {icon class=add action=enterdata forms_id=$f->id text='Add record'|gettext}
            {/if}
            {if $permissions.edit && $record_id}
                {icon class=edit action=enterdata forms_id=$f->id id=$record_id title='Edit this record'|gettext}
            {/if}
            {if $permissions.delete && $record_id}
                {icon class=delete action=delete forms_id=$f->id id=$record_id title='Delete this record'|gettext}
            {/if}
            {if $permissions.viewdata}
                {icon class="view" action=showall id=$form->id text='View Records'|gettext|cat:" (`$count`)" title='View all records'|gettext}
            {/if}
        {/permissions}
        {if !$is_email && ($prev || $next) && ($config.pagelinks == "Top and Bottom" || $config.pagelinks == "Top Only")}
            <span style="float:right">
                {if $next}
                    {icon img='page_next.png' action=show forms_id=$f->id id=$next->id title='Next Record'|gettext}
                {else}
                    {icon img='page_next.png' title='Next Record'|gettext}
                {/if}
            </span>
            {clear}
        {/if}
        {if empty($config.report_def)}
            <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                <thead>
                    <tr>
                        <th colspan="2">
                            <{$config.heading_level|default:'h2'}>{$title}</{$config.heading_level|default:'h2'}>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $fields as $fieldname=>$value}
                        <tr class="{cycle values="even,odd"}">
                            <td>{$captions.$fieldname}</td>
                            <td>
                                {if $fieldname|lower == 'email' && stripos($value, '<a ') === false}
                                    <a href="mailto:{$value}">{$value}</a>
                                {elseif $fieldname|lower == 'image'}
                                    {$matches = array()}
                                    {$tmp = preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $value, $matches)}
                                    {$filename1 = $matches.2.0}
                                    {$filename2 = str_replace(URL_BASE, '/', $filename1)}
                                    {$base = str_replace(PATH_RELATIVE, '', BASE)}
                                    {$fileinfo = expFile::getImageInfo($base|cat:$filename2)}
                                    {if $fileinfo.is_image == 1}
                                        {img src=$filename1 w=64}
                                    {else}
                                        {$value}
                                    {/if}
                                {else}
                                    {$value}
                                {/if}
                            </td>
                        </tr>
                    {foreachelse}
                        <tr><td colspan="4"><p>{message text='You don\'t have any records yet'|gettext}</p></td></tr>
                    {/foreach}
                </tbody>
            </table>
        {else}
            <{$config.heading_level|default:'h2'}>{$title}</{$config.heading_level|default:'h2'}>
            {eval var=$config.report_def}
            {clear}{br}
        {/if}
        {if !empty($referrer)}
            <p>{'Referrer'|gettext}: {$referrer}</p>
        {/if}
        {if !$is_email && ($prev || $next) && ($config.pagelinks == "Top and Bottom" || $config.pagelinks == "Bottom Only")}
            <div class="module-actions">
                {clear}
                <span style="float:left">
                    {if $prev}
                        {icon img='page_prev.png' action=show forms_id=$f->id id=$prev->id title='Previous Record'|gettext}
                    {else}
                        {icon img='page_prev.png' title='Previous Record'|gettext}
                    {/if}
                </span>
                <span style="float:right">
                    {if $next}
                        {icon img='page_next.png' action=show forms_id=$f->id id=$next->id title='Next Record'|gettext}
                    {else}
                        {icon img='page_next.png' title='Next Record'|gettext}
                    {/if}
                </span>
                {clear}
            </div>
        {/if}
        {if !$is_email}
            {*<a class="{button_style}" href="{$backlink}">{'Back'|gettext}</a>*}
            {icon button=true link=$backlink text='Back'|gettext}
        {/if}
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