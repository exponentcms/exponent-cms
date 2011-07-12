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

{css unique="manageordertype" corecss="tables"}

{/css}

<div class="modules order_type showall">
	<h1>{$moduletitle|default:"Manage Order Types"}</h1>
	
	<a href="{link action=create}">Create a new order type</a>{br}{br}
	<div id="orders">
		{pagelinks paginate=$page top=1}
		<table id="prods" class="exp-skin-table">
			<thead>
				<tr>
				    <th>Default</th>
                    <th>Creates New User</th> 
                    <th>Emails Customer</th> 
                    <th>Affects Inventory</th> 
					<th>Name</th>
					<th>Admin</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$page->records item=listing name=listings}
				<tr class="{cycle values="odd,even"}">				    
                    <td>{if $listing->is_default == 1}{icon img="clean.png"}{/if}</td>
                    <td>{if $listing->creates_new_user == 1}{icon img="clean.png"}{/if}</td>
                    <td>{if $listing->emails_customer == 1}{icon img="clean.png"}{/if}</td>
                    <td>{if $listing->affects_inventory == 1}{icon img="clean.png"}{/if}</td>
					<td>{$listing->title}</td>
					<td>
					    {if $permissions.manage == true}
                            {icon controller=order_type action=edit record=$listing}
                            {icon controller=order_type action=delete record=$listing}
                        {/if}
					</td>
				</tr>
				{foreachelse}
				    <tr class="{cycle values="odd,even"}">
				        <td colspan="4">No order types have been created yet.</td>
				    </tr>
				{/foreach}
		</tbody>
		</table>
		{pagelinks paginate=$page bottom=1}
	</div>
</div>
