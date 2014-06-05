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

{if !$error}
    {css unique="data-view" corecss="button, tables"}

    {/css}
    <div class="module forms showall">
        <{$config.item_level|default:'h2'}>{$title}</{$config.item_level|default:'h2'}>
        {if $description != ""}
            {$description}
        {/if}
        {permissions}
            <div class="module-actions">
                {if $permissions.create}
                    {icon class=add action=enterdata forms_id=$f->id text='Add record'|gettext}
                    &#160;&#160;|&#160;&#160;
                {/if}
                {icon class="downloadfile" action=export_csv id=$f->id text="Export as CSV"|gettext}
                {export_pdf_link landscapepdf=1 limit=999 prepend='&#160;&#160;|&#160;&#160;'}
                {if $permissions.manage}
                    &#160;&#160;|&#160;&#160;
                    {icon class=configure action=design_form id=$f->id text="Design Form"|gettext}
                    &#160;&#160;|&#160;&#160;
                    {icon action=manage select=true text="Manage Forms"|gettext}
                    {if !empty($filtered)}
                        &#160;&#160;|&#160;&#160;<span style="background-color: yellow; font-weight: bold;margin-bottom: 5px">{'Records Filtered'|gettext}: '{$filtered}'</span>
                    {/if}
                {/if}
                {if $permissions.delete}
                    &#160;&#160;|&#160;&#160;
                    {icon class=delete action=delete_records forms_id=$f->id text='Purge records'|gettext onclick="return confirm('"|cat:("Are you sure you want to delete all form records?"|gettext)|cat:"');"}
                {/if}
            </div>
        {/permissions}
        {$page->links}
        <div style="overflow: auto; overflow-y: hidden;">
            {foreach from=$page->records item=fields key=key name=fields}
                <div class="item-actions">
                    {if $permissions.edit}
                        {icon class=edit action=enterdata forms_id=$f->id id=$fields.id title='Edit this record'|gettext}
                    {/if}
                    {if $permissions.delete}
                        {icon class=delete action=delete forms_id=$f->id id=$fields.id title='Delete this record'|gettext}
                    {/if}
                </div>
                {if empty($config.report_def)}
                    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                        <tbody>
                            {foreach from=$fields key=fieldname item=value}
                                <tr class="{cycle values="even,odd"}">
                                    <td>{$captions[$fieldname]}</td>
                                    <td>{$value}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                {else}
                    {eval var=$config.report_def}
                    {clear}
                {/if}
            {/foreach}
        </div>
        {$page->links}
        {if empty($f) && $permissions.configure}
            {permissions}
                <div class="module-actions">
                    <div class="msg-queue notice" style="text-align:center">
                        <p>{'You MUST assign a form to use this module!'|gettext} {icon action="manage" select=true}</p></div>
                </div>
            {/permissions}
        {/if}
    </div>
{/if}
