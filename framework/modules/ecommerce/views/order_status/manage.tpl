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

<div class="modules order_status showall">
	<h1>{$moduletitle|default:"Manage Status Codes"}</h1>
	
	<a href="{link action=create}">Create a new status code</a>{br}{br}
	<div id="orders">
		{pagelinks paginate=$page top=1}
		<table id="prods" class="exp-skin-table">
			<thead>
				<tr>
				    <th>Default</th>
				    <th>Treat as Closed</th>
					<th>Name</th>
					<th>Admin</th>
					<th>Order</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$page->records item=listing name=listings}
				<tr class="{cycle values="odd,even"}">
				    <td>{if $smarty.foreach.listings.first == 1}{img src=`$smarty.const.ICON_RELATIVE`toggle_on.gif}{/if}</td>
				    <td>
				        {if $listing->treat_as_closed == 1}
				            <a href="{link action=toggle_closed id=$listing->id}">{img src=`$smarty.const.ICON_RELATIVE`toggle_on.gif}</a>
				        {else}
				            <a href="{link action=toggle_closed id=$listing->id}">{img src=`$smarty.const.ICON_RELATIVE`toggle_off.gif}</a>
				        {/if}
				    </td>
					<td>{$listing->title}</td>
					<td>
					    {if $permissions.manage == true}
                            {icon controller=order_status action=edit record=$listing}
                            {icon controller=order_status action=delete record=$listing}
                        {/if}
					</td>
					<td>
					    {if $permissions.manage == true}
                            {if $smarty.foreach.listings.first == 0}
                                {icon controller=order_status action=rerank img=up.png record=$listing push=up}    
                            {/if}
                            {if $smarty.foreach.listings.last == 0}
                                {icon controller=order_status action=rerank img=down.png record=$listing push=down}
                            {/if}
                        {/if}
					</td>
				</tr>
				{foreachelse}
				    <tr class="{cycle values="odd,even"}">
				        <td colspan="4">No order status codes have been created yet.</td>
				    </tr>
				{/foreach}
		</tbody>
		</table>
		{pagelinks paginate=$page bottom=1}
	</div>
</div>
