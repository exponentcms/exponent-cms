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

<div class="modules order_status manage-messages">
	<h1>{$moduletitle|default:"Manage Order Status Messages"}</h1>
	
	<a class="add" href="{link action=edit_message}">Add a new message</a>
	<div id="orders">
		{$page->links}
		<table id="prods" class="exp-skin-table">
			<thead>
				<tr>
				    <th>Body</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$page->records item=listing name=listings}
				<tr class="{cycle values="odd,even"}">
					<td>{$listing->body}</td>
					<td>
					    {if $permissions.manage == true}
                            {icon controller=order_status action=edit_message img=edit.png id=$listing->id}
                            {icon controller=order_status action=delete_message img=delete.png id=$listing->id}
                        {/if}
					</td>
				</tr>
				{foreachelse}
				    <tr class="{cycle values="odd,even"}">
				        <td colspan="4">No status codes have been created yet.</td>
				    </tr>
				{/foreach}
		</tbody>
		</table>
	</div>
</div>
