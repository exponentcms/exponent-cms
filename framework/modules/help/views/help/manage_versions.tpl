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
    <h1>Manage Help Versions</h1>
    <p>
        This page allows you to manage versions for saving help documents for Exponent CMS.
        {br}
        <em>The current version is {$current_version->version}</em>
    </p>
    
    {icon class=add action=edit_version title="Add new help version" text="Add a New Help Version"}{br}
    {icon class=add action=manage title="Manage Help" text="Manage Help Docs"}{br}
    {$page->links}
    <table class="exp-skin-table">
        <thead>
        <tr>
            {$page->header_columns}
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$page->records item=version}
        <tr class="{cycle values="odd,even"}">
            <td><a href="{link action=manage version=$version->id}">{$version->version}</a></td>
            <td>{$version->title}</td>
            <td>{if $version->is_current == 1}{img src=`$smarty.const.ICON_RELATIVE`toggle_on.gif}{/if}</td>
            <td><a href="">{$version->num_docs}</a></td>
            <td>
                {permissions}
                    {if $permissions.edit == 1}
                        {icon img=edit.png action=edit_version id=$version->id title="Edit Help Version"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete_version img=delete.png id=$version->id title="Delete this version" onclick="return confirm('Are you sure you want to delete this help version and all the documentation that goes along with it?');"}
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
