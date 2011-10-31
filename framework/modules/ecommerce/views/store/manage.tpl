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

{css unique="managestore" corecss="tables"}

{/css}

<div class="module store showall-uncategorized">
    <h1>Manage Products</h1>
    {permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=edit title="Create a new product" text="Add a product"}
			{/if}
		</div>
    {/permissions}
    <div id="products">
		{pagelinks paginate=$page top=1}
        <table id="prods" class="exp-skin-table" style="width:95%">
        <thead>
            <tr>
            <th></th>
            {$page->header_columns}
            <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$page->records item=listing name=listings}
			
            <tr class="{cycle values="odd,even"}">
                <!--td>{img file_id=$listing->expFile.images[0]->id square=60}</td-->
                <td>
					{if $listing->product_type == "product"}
						<a href={link controller=store action=showByTitle title=$listing->sef_url}>{img file_id=$listing->expFile.mainimage[0]->id square=true h=50}</a>
					{else}
						{img file_id=$listing->expFile.mainimage[0]->id square=true h=50}
					{/if}
				</td>
                <td>{$listing->product_type}</td> 
                <td>{$listing->model|default:"N/A"}</td>                
                <td>
					{if $listing->product_type == "product"}
						<a href={link controller=store action=showByTitle title=$listing->sef_url}>{$listing->title}</a>
					{else}
						{$listing->title}
					{/if}
				</td>
                <td>
					{if $listing->product_type == "product"}
						${$listing->base_price|number_format:2}
					{/if}
				</td>
                <td>
                    {permissions}
						<div class="item-actions">
							{if $permissions.edit == 1}
								{icon action=edit record=$listing title="Edit `$listing->title`"}
							{/if}
							{if $permissions.delete == 1}
								{icon action=delete record=$listing title="Delete `$listing->title`"}
							{/if}
							{if $permissions.edit == 1 && $listing->product_type == "product"}
								{icon action=copyProduct img="copy.png" title="Copy `$listing->title` " record=$listing}
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
    {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
        {if $permissions.configure == 1 or $permissions.administrate == 1}
        <div id="prod-admin">
                <a href="{link controller=store action=create}">Add a new Product</a>
                {br}<a href="{link controller=storeCategory action=manage}">Manage Categories</a>
                {br}<a href="{link controller=store action=config}">Configure Store</a>
				{br}<a href="{link controller=store action=nonUnicodeProducts}">Show Non-Unicode Products</a>
				{br}<a href="{link controller=store action=uploadModelAliases}">Upload Model Aliases</a>
        </div>
    {/if}
    {/permissions}
</div>
