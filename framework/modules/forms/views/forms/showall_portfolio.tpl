{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
        <h2>{$title}</h2>
        {if $description != ""}
            {$description}
        {/if}
        {permissions}
            <div class="module-actions">
                {if $permissions.edit == 1}
                    {icon class=add action=enterdata forms_id=$f->id text='Add record'|gettext}
                    &#160;&#160;|&#160;&#160;
                {/if}
                {icon class="downloadfile" action=export_csv id=$f->id text="Export as CSV"|gettext}
                {export_pdf_link landscapepdf=1 limit=999 prepend='&#160;&#160;|&#160;&#160;'}
                {if $permissions.manage}
                    &#160;&#160;|&#160;&#160;
                    {icon class=configure action=design_form id=$f->id text="Design Form"|gettext}
                    &#160;&#160;|&#160;&#160;
                    {icon action=manage text="Manage Forms"|gettext}
                {/if}
            </div>
        {/permissions}
        {$page->links}
        <div style="overflow: auto; overflow-y: hidden;">
            {foreach from=$page->records item=fields key=key name=fields}
                <div class="item-actions">
                    <td>
                        {if $permissions.edit == 1}
                            {icon class=edit action=enterdata forms_id=$f->id id=$fields.id title='Edit this record'|gettext}
                        {/if}
                        {if $permissions.delete == 1}
                            {icon class=delete action=delete forms_id=$f->id id=$fields.id title='Delete this record'|gettext}
                        {/if}
                    </td>
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
    </div>
{/if}
