{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div style="float:right; margin-top:15px;"><a href="{link controller=report action=batch_export applytoall=true}">[Export This Data]</a></div>
<div class="module store showall-uncategorized">
    <h1>{'Improperly Categorized Products'|gettext}</h1>
    <div id="products">
        {$page->links}
        <table id="prods" class="exp-skin-table" style="width:95%">
        <thead>
            <tr>
            <th></th>
            {$page->header_columns}
            <th>{'Edit/Delete'|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$page->records item=listing name=listings}
            <tr class="{cycle values="odd,even"}">
                <td><a href={link controller=store action=showByTitle title=$listing->sef_url}>{img file_id=$listing->expFile.mainimage[0]->id square=true h=50}</a></td>
                <td>{$listing->model|default:"N/A"}</td>
                <td>{$listing->title}</td>
                <td>${$listing->base_price|number_format:2}</td>
                <td>
                    {permissions}
                        {if $permissions.edit == 1}
                            {icon img='edit.png' action=edit id=$listing->id title="Edit `$listing->title`"}
                        {/if}
                        {if $permissions.delete == 1}
                            {icon img='delete.png' action=delete id=$listing->id title="Delete `$listing->title`"}
                        {/if}
                    {/permissions}  
                </td>                   
            </tr>
            {/foreach}
        </tbody>
        </table>
        {$page->links}
    </div>
</div>
