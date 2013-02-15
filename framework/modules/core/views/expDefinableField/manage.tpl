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
{css unique="definable-field-manage" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/tables.css"}

{/css}
<div class="module expDefinableField manage">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Definable Fields"|gettext) module="manage-definable-fields"}
        </div>
        <h1>{"Manage Definable Fields"|gettext}</h1>
    </div>
	{permissions}
    	{if $permissions.create == 1}
    		{*<a class="add" href="{link controller=$model_name action=create}">{"Create a new Tag"|gettext}</a>*}
    	{/if}
    {/permissions}
 
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>
                    {"Field Name"|gettext}
                </th>
                <th>
                    {"Type"|gettext}
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
            {foreach from=$fields item=listing}
                <tr class="{cycle values='odd,even'}">
                    <td>
                        <strong>{$listing->name}</strong>
                    </td>
                    <td>
                        {$listing->type}
                    </td>
                    <td>
                    </td>
                    <td>
                        {permissions}
                            {if $permissions.edit == 1}
                                {icon action="edit" record=$listing title="Edit this field"|gettext}
                            {/if}
                            {if $permissions.delete == 1}
                                {icon action="delete" record=$listing title="Delete this field"|gettext onclick="return confirm('Are you sure you want to delete this field?');"}
                            {/if}
                        {/permissions}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>

	<hr />
	<table cellpadding="5" cellspacing="0" border="0">
        <tr>
            <td style="border:none;">
                <form method="post" action="{$smarty.const.PATH_RELATIVE}index.php">
                    <input type="hidden" name="module" value="expDefinableField" />
                    <input type="hidden" name="action" value="edit" />
                    {'Add a'|gettext} <select name="control_type" onchange="this.form.submit()">
                        {foreach from=$types key=value item=caption}
                            <option value="{$value}">{$caption}</option>
                        {/foreach}
                    </select>
                </form>
            </td>
        </tr>
    </table>
</div>
