{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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

{css unique="showalluncategorized" corecss="tables"}

{/css}

<div class="module store showall-uncategorized">
    <h1>Uncategorized Products</h1>
    <div id="products">
		{pagelinks paginate=$page top=1}
        <table id="prods" class="exp-skin-table" style="width:95%">
        <thead>
            <tr>
            <th></th>
            {$page->header_columns}
            <th>Edit/Delete</th>
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
						<div class="item-actions">
							{if $permissions.edit == 1}
								{icon action=edit record=$listing title="Edit `$listing->title`"}
							{/if}
							{if $permissions.delete == 1}
								{icon action=delete record=$listing title="Delete `$listing->title`"}
							{/if}
						</div>
                    {/permissions}  
                </td>                   
            </tr>
            {/foreach}
        </tbody>
        </table>
		{pagelinks paginate=$page bottom=1}
    </div>
</div>
