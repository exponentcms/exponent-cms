{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

<div class="module expTags manage">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Tags"|gettext) module="manage-tags"}
        </div>
        <h2>{"Manage Tags"|gettext}</h2>
    </div>
	{permissions}
        <div class="module-actions">
            {if $permissions.create}
                {*<a class="add" href="{link controller=$model_name action=create}">{"Create a new Tag"|gettext}</a>*}
            {/if}
            {if $permissions.manage}
                {icon action=import text="Import Tags"|gettext}
                {icon action=export text="Export Tags"|gettext}
            {/if}
        </div>
    {/permissions}
    {$page->links}
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>
                    {"Tag Name"|gettext}
                </th>
                <th>
                    {"Use Count"|gettext}
                </th>
                <th>
                    {"Used in"|gettext}
                </th>
                <th>
                    {"Actions"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$page->records item=listing}
                <tr class="{cycle values="odd,even"}">
                    <td>
                        <strong>{$listing->title}</strong>
                    </td>
                    <td>
                        {$listing->attachedcount}
                    </td>
                    <td>
                        {foreach from=$listing->attached item="type" key=key name=types}
                            <strong>{$key}</strong>{br}
                            <ul class="tag-list">
                            {foreach from=$type item=ai name=ai}
                                <li>
                                {if $key!='faq'}
                                    {if !empty($ai->sef_url)}
                                        <a href="{link controller=$key action="show" title=$ai->sef_url}">{$ai->title|truncate:50:"..."}</a>
                                    {else}
                                        <a href="{link controller=$key action="show" id=$ai->id}">{$ai->title|truncate:50:"..."}</a>
                                    {/if}
                                {else}
                                    {$ai->title|truncate:50:"..."}
                                {/if}
                                </li>
                            {/foreach}
                            </ul>
                        {/foreach}
                    </td>
                    <td>
                        {permissions}
                            <div class="item-actions">
                                {if $permissions.edit}
                                    {icon controller=$controller action=edit record=$listing title="Edit this tag"|gettext}
                                {/if}
                                {if $permissions.delete}
                                    {icon controller=$controller action=delete record=$listing title="Delete this tag"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this tag?"|gettext)|cat:"');"}
                                {/if}
                            </div>
                        {/permissions}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    {$page->links}
</div>
