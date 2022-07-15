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

<div class="module help manage">
    <h1>{'Manage Help Versions'|gettext}</h1>
    <blockquote>
        {'This page allows you to manage versions for saving help documents for Exponent CMS'|gettext}.
        {br}
        <em>{'The current version is'|gettext} {$current_version->version}</em>
    </blockquote>

    {icon class=add action=edit_version text="Add a New Help Version"|gettext}{br}
    {icon action=manage title="Manage All Help Docs"|gettext text="Manage All Help Docs"|gettext}{br}
    {$page->links}
    <table class="exp-skin-table">
        <thead>
        <tr>
            {$page->header_columns}
            <th>{'Actions'|gettext}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$page->records item=version}
        <tr class="{cycle values="odd,even"}">
            <td><a href="{link action=manage version=$version->id}">{$version->version}</a></td>
            <td>{$version->title}</td>
            <td>
	            {if $version->is_current == 1}
		            <span class="active">Active</span>
		        {else}
		            <a class="inactive" href="{link action=activate_version id=$version->id}" title="Activate this Version"|gettext>Activate</a>
	            {/if}
            </td>
            <td>{$version->num_docs}</td>
            <td>
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $record->poster == $user->id)}
                            {icon img='edit.png' action=edit_version record=$version title="Edit Help Version"|gettext}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
                            {icon action=delete_version img='delete.png' record=$version title="Delete this version"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this help version and all the documentation that goes along with it?"|gettext)|cat:"');"}
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
    {$page->links}

</div>
