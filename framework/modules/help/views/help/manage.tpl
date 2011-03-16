{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
    <h1>Manage Help Documents</h1>
    <p>
        This page allows you to manage help documents for Exponent CMS.
        {br}
        <em>The current version is {$current_version->version}</em>
    </p>
    
    {icon class=add action=edit_version title="Add new help version" text="Add a New Help Version"}{br}
    {icon class=add action=edit title="Add a New Help Document" text="Add a New Help Document to version `$current_version->version`"}{br}
    {icon class=add action=manage_versions title="Manage Versions" text="Manage Versions"}{br}
    {$page->links}
    <table class="exp-skin-table">
        <thead>
        <tr>
            {$page->header_columns}
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$page->records item=doc}
        <tr class="{cycle values="odd,even"}">
            <td><a href={link action=show version=$doc->help_version->version title=$doc->title}>{$doc->title}</a></td>
            <td>{$doc->body|truncate:55}</td>
            <td><a href="{link action=manage version=$doc->help_version->id}">{$doc->help_version->version|number_format:1}</a></td>
            <td>
                {permissions}
                    {if $permissions.edit == 1}
                        {icon img=edit.png action=edit id=$doc->id title="Edit Help Doc"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete img=delete.png id=$doc->id title="Delete this help doc" onclick="return confirm('Are you sure you want to delete this help document?');"}
                    {/if}
                {/permissions}
            </td>
        </tr>
        {foreachelse}
            <td colspan=4>No documents created yet</td>
        {/foreach}
        </tbody>
    </table>
    {$page->links}
        
</div>
