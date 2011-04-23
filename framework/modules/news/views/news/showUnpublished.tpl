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

<div class="module news show-expired">
	<h1>{$moduletitle|default:"Expired News"}</h1>
    {pagelinks paginate=$page top=1}
	<table id="prods" class="exp-skin-table" width="95%">
	    <thead>
		<tr>
		    {$page->header_columns}
			<th>Actions</th>
		</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=listing name=listings}
			<tr class="{cycle values="odd,even"}">
				<td><a href="{link controller=news action=show id=$listing->id}">{$listing->title}</a></td>
				<td>{$listing->publish|format_date:"%B %e, %Y"}</td>
				<td>
				    {if $listing->unpublish == 0}
				        Unpublished
				    {else}
				        Expired - {$listing->unpublish|format_date:"%B %e, %Y"}
				    {/if}
				</td>
				<td>
				    {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
						<div class="item-actions">
							{if $permissions.edit == true}
								{icon controller=news action=edit record=$listing title="Edit this news post"}
							{/if}
							{if $permissions.delete == true}
								{icon controller=news action=delete record=$listing title="Delete this news post" onclick="return confirm('Are you sure you want to delete `$item->title`?');"}
							{/if}
						</div>
                    {/permissions}
				</td>
			</tr>
			{foreachelse}
			    <td colspan=3>There is no expired news.</td>
			{/foreach}
		</tbody>
	</table>
    {pagelinks paginate=$page bottom=1}
</div>
