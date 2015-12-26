{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="module expTags manage yui-content yui3-skin-sam">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Tags"|gettext) module="manage-tags"}
        </div>
        <h2>{"Manage Module Tags"|gettext}</h2>
    </div>
	{permissions}
        <div class="module-actions">
            {if $permissions.create}
                {*<a class="add" href="{link controller=$model_name action=create}">{"Create a new Tag"|gettext}</a>*}
            {/if}
        </div>
    {/permissions}
    {$page->links}
    {form action=change_tags}
    {control type=hidden name=mod value=$page->model}
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>
                    <input type='checkbox' name='checkallp' title="{'Select All/None'|gettext}" onchange="selectAllp(this.checked)">
                </th>
                <th>
                    {"Item"|gettext}
                </th>
                <th>
                    {"Tags"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$page->records item=record}
                <tr class="{cycle values="odd,even"}">
                    <td>
                        {control type="checkbox" name="change_tag[]" label=" " value=$record->id}
                    </td>
                    <td>
                        {$record->title|truncate:50:"..."}
                    </td>
                    <td>
                        {foreach from=$record->expTag item=tag name=tags}
                            {$tag->title},
                        {/foreach}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    {$page->links}
    <blockquote>{'Select the item(s) to change, then enter the tags below'|gettext}</blockquote>
    {*{control type="text" id="addTag" name="addTag" label="Add these Tags (comma separated)"|gettext size=45 value=''}*}
    {*{control type="text" id="removeTag" name="removeTag" label="Remove these Tags (comma separated)"|gettext size=45 value=''}*}
    {control type="tags" id="addTag" name="addTag" label="Add these Tags (comma separated)"|gettext size=45 value=''}
    {control type="tags" id="removeTag" name="removeTag" label="Remove these Tags (comma separated)"|gettext size=45 value=''}
    {control type=buttongroup submit="Change Tags on Selected Items"|gettext cancel="Cancel"|gettext returntype="viewable"}
    {/form}
</div>

{script unique="edittags"}
{literal}
    function selectAllp(val) {
        var checks = document.getElementsByName("change_tag[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/literal}
{/script}
