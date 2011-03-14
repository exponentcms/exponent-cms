{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<div class="company showall">
	<h1>{$moduletitle|default:'Company Listings'}</h1>

    <a href="{link controller=$controller action=create}">Add a new {$modelname}</a><br />
    <table class="exp-skin-table">
    <thead>
        {$page->header_columns}
        <th>&nbsp;</th>
    </thead>
    <tbody>
        {foreach from=$page->records item=company name=companies}
		<tr class="{cycle values="odd,even"}">
            <td>{$company->title}</td>
            <td>{$company->website}</td>
            <td>
                {permissions}
                    {if $permissions.edit == 1}
                        {icon img=edit.png action=edit id=$company->id title="Edit `$company->title`"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon img=delete.png action=delete id=$company->id title="delete `$company->title`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
                    {/if}
                {/permissions}
            </td>
        </tr>
        {/foreach}
    </tbody>
    </table>
</div>
