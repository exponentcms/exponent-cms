{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="module motd showall">
    <h1>{$moduletitle|default:"Messages by day"}</h1>
    <div class="bodycopy">
        {$record->body}
    </div>
    
    {$page->links}
    {permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class="add" action=create text="Add a New Message"}
			{/if}
		</div>
    {/permissions}
    <table id="prods" class="exp-skin-table">
		<thead>
			<tr>
				{$page->header_columns}
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=listing name=listings}
			<tr class="{cycle values="odd,even"}">
				<td>{$listing->month}/{$listing->day}</td>
				<td>{$listing->body}</td>
				<td>
					{permissions}
						<div class="item-actions">
							{if $permissions.edit == 1}
								{icon action=edit record=$listing title="Edit this message"}
							{/if}
							{if $permissions.delete == 1}
								{icon action=delete record=$listing title="Delete this message" onclick="return confirm('Are you sure you want to delete this message?');"}
							{/if}
						</div>
					{/permissions}  
				</td>                   
			</tr>
			{foreachelse}
				<tr class="{cycle values="odd,even"}">
				<td colspan="6">
					There are no products in the this store yet.
				</td>                   
			</tr>
			{/foreach}
		</tbody>
    </table>
    {$page->links}
</div>
