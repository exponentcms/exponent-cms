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

<div class="module help manage">
    <h1>{'Manage Help Documents'|gettext}</h1>
    <blockquote>
        {'This page allows you to manage help documents for Exponent CMS'|gettext}.
        {br}
        <em>{'The current version is'|gettext} {$current_version->version}</em>
    </blockquote>

    {icon class=add action=edit_version text="Add a New Help Version"|gettext}{br}
    {icon class=add action=edit text="Add a New Help Document"|gettext}{br}
    {icon class=manage action=manage_versions text="Manage Help Versions"|gettext}{br}
{*    {$page->links}*}
    <table id="docs-manage" class="exp-skin-table">
        <thead>
        <tr>
{*            {$page->header_columns}*}
            <th>{'Title'|gettext}</th>
            <th>{'Version'|gettext}</th>
            <th>{'Section'|gettext}</th>
            <th>{'Actions'|gettext}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$page->records item=doc}
        {$myloc=serialize($__loc)}
        <tr class="{cycle values="odd,even"}">
            <td><a href={link action=show version=$doc->help_version->version title=$doc->sef_url} title="{$doc->body|summarize:"html":"para"}">{$doc->title}</a></td>
            <td><a href="{link action=manage version=$doc->help_version->id}">{$doc->help_version->version}</a></td>
	        <td>{$sections[$doc->loc->src]}</td>
            <td>
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $doc->poster == $user->id)}
                            {icon img='edit.png' action=edit record=$doc title="Edit Help Doc"|gettext}
                            {icon img='copy.png' action=copy record=$doc title="Copy Help Doc"|gettext}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $doc->poster == $user->id)}
                            {icon action=delete img='delete.png' record=$doc title="Delete this help doc"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this help document?"|gettext)|cat:"');"}
                        {/if}
                    </div>
                {/permissions}
            </td>
        </tr>
        {foreachelse}
            <td colspan=4>{'No documents created yet'|gettext}</td>
        {/foreach}
        </tbody>
    </table>
{*    {$page->links}*}
</div>

{script unique="manage-groups" jquery='jquery.dataTables'}
{literal}
    $(document).ready(function() {
        var tableContainer = $('#docs-manage');

        var table = tableContainer.DataTable({
            columns: [
                null,
                { searchable: false },
                null,
                { searchable: false, orderable: false },
            ],
            autoWidth: false,
        });
    } );
{/literal}
{/script}