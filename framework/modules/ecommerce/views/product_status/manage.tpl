{*
 * Copyright (c) 2004-2020 OIC Group, Inc.
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

{css unique="manageproductstatus" corecss="tables"}

{/css}

<div class="modules order_type showall">
	<h1>{$moduletitle|default:"Manage Product Statuses"|gettext}</h1>
    <blockquote>
        {'The top product status code will be used as the default product status'|gettext}
    </blockquote>
    {if $permissions.create}
    	{icon class="add" action=create text='Create a new product status'|gettext}
    {/if}
    {if $permissions.manage}
        {ddrerank items=$page->records model="product_status" label="Product Statuses"|gettext}
    {/if}
	<div id="orders">
		{pagelinks paginate=$page top=1}
		<table id="prods" class="exp-skin-table">
			<thead>
				<tr>
					<th>{'Name'|gettext}</th>
					<th>{'Action'|gettext}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$page->records item=listing name=listings}
                    <tr class="{cycle values="odd,even"}">
                        <!--td>{if $smarty.foreach.listings.first == 1}{img src=$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}{/if}</td-->
                        <td>{$listing->title}</td>
                        <td>
                            {if $permissions.manage}
                                <div class="item-actions">
                                    {icon action=edit record=$listing}
                                    {icon action=delete record=$listing}
                                </div>
                            {/if}
                        </td>
                    </tr>
				{foreachelse}
				    <tr class="{cycle values="odd,even"}">
				        <td colspan="4">{'No product status codes have been created yet.'|gettext}</td>
				    </tr>
				{/foreach}
            </tbody>
		</table>
		{pagelinks paginate=$page bottom=1}
	</div>
</div>
