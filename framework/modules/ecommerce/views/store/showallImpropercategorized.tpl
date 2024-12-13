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

{css unique="showallbadcategorized" corecss="tables"}

{/css}

<div class="module store showall-uncategorized">
    <div style="float:right; margin-top:15px;"><a href="{link controller=report action=batch_export applytoall=true}">{'Export This Data'|gettext}</a></div>
    <h1>{'Improperly Categorized Products'|gettext}</h1>
    <blockquote>
        {'These products are NOT assigned to an end-node category and therefore will NOT appear in any listing.'|gettext}
        {'They MUST be asssgned to a store category which doesn\'t have sub-categories in order to appear in any listing.'|gettext}
    </blockquote>
    {permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class="add" action=create text="Add a Product"|gettext}
        {/if}
        {if $permissions.manage}
            {icon action=manage text="Manage Products"|gettext}
            {icon controller=storeCategory action=manage text="Manage Store Categories"|gettext}
        {/if}
    </div>
    {/permissions}
    <div id="products">
        {$page->links}
        <table id="prods" class="exp-skin-table" style="width:95%">
        <thead>
            <tr>
            <th></th>
            {$page->header_columns}
            <th>{'Action'|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$page->records item=listing name=listings}
            <tr class="{cycle values="odd,even"}">
                <td><a href={link controller=store action=show title=$listing->sef_url}>{img file_id=$listing->expFile.mainimage[0]->id square=true h=50}</a></td>
                <td>{$listing->model|default:"N/A"}</td>
                <td>{$listing->title}</td>
                <td>{$listing->base_price|currency}</td>
                <td>
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit || ($permissions.create && $listing->poster == $user->id)}
                                {icon img='edit.png' action=edit id=$listing->id title="Edit `$listing->title`"}
                            {/if}
                            {if $permissions.delete || ($permissions.create && $listing->poster == $user->id)}
                                {icon img='delete.png' action=delete id=$listing->id title="Delete `$listing->title`"}
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
</div>
