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

<div class="pagination-table">
	<table>
	    <thead>
    		<tr>
    		    {$page->header_columns}
    			<!--th>Admin</th-->
    		</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=listing name=listings}
			<tr class="{cycle values="odd,even"}">
			    {foreach from=$page->columns item=col key=key}
				    <td>
				        {if $key=="actupon"}
				            <input type=checkbox name=act-upon[] value={$listing->id} />
				        {else}
    			            {if $page->linkables[$key]}
    				            <a href="{link parse_attrs=$page->linkables[$key] record=$listing}">{$listing->$col}</a>
                            {else}
    				            {$listing->$col}
				            {/if}
				        {/if}
				    </td>
				{/foreach}
			    <!--td>
			        {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
                    <div class="item-actions">
                    {if $permissions.edit == true}
                        {icon controller=$page->controller action=edit id=$item->id title="Edit"}
                    {/if}
                    {if $permissions.delete == true}
                        {icon controller=$page->controller action=delete id=$item->id title="Delete" onclick="return confirm('Are you sure you want to delete this?');"}
                    {/if}
                    </div>
                    {/permissions}
			    </td-->
			</tr>
			{foreachelse}
			    <td colspan="{$page->columns|count}">No Data.</td>
			{/foreach}
		</tbody>
	</table>
</div>
