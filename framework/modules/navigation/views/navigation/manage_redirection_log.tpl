{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{css unique="redirect-log" corecss="button,tables"}
{literal}
    .details {
        display:none;
        position: absolute;
        background: inherit;
        border: 1px solid black;
        padding: 4px;
        border-radius: 4px;
    }


    td.name:hover > .details {
        display:block;
    }
{/literal}
{/css}

<div class="module navigation manage-redirection-log">
    <h1>{'Page Redirection Log'|gettext}</h1>
    {if $page->total_records}{icon class="delete" action=delete_redirection_log text='Delete Page Redirection Log'|gettext onclick="return confirm('"|cat:("Delete the entire log?"|gettext)|cat:"');"}{/if}
    {$page->links}
    {*<div class="exp-ecom-table">*}
        {*{$page->table}*}
    {*</div>*}

    <table class="exp-skin-table">
        <thead>
            <tr>
                {$page->header_columns}
                <th> </th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$page->records item=url}
                <tr class="{cycle values="odd,even"}">
                    <td class="name">
                        {$url->missed_sef_name}
                        <div class="details">
                            <strong>{'Requested'|gettext}:</strong> {$url->missed_sef_name}{br}
                            <strong>{'Redirected to'|gettext}:</strong> {$url->new_sef_name}{br}
                            <strong>{'Date'|gettext}:</strong> {$url->timestamp}{br}
                            <strong>{'Referrer'|gettext}:</strong> {$url->referer}{br}
                            <strong>{'From URL'|gettext}:</strong> {$url->url_request}{br}
                            <strong>{'Agent'|gettext}:</strong> {$url->user_agent}{br}
                        </div>
                    </td>
                    <td>{$url->timestamp}</td>
                    <td>{$url->new_sef_name}</td>
                    <td>
                        {permissions}
                            <div class="item-actions">
                                {if $permissions.edit}
                                    {icon img='edit.png' action=edit_redirection missed_sef_name=$url->missed_sef_name title="Edit Page Redirection"|gettext}
                                {/if}
                            </div>
                        {/permissions}
                    </td>
                </tr>
            {foreachelse}
                <td colspan=4>{'No pages redirected yet'|gettext}</td>
            {/foreach}
        </tbody>
    </table>
	{$page->links}
</div>