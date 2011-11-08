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

{css unique="showunpublished" corecss="tables"}

{/css}

<div class="module news show-expired">
	<h1>{$moduletitle|default:"Expired News"|gettext}</h1>
    {pagelinks paginate=$page top=1}
	<table id="prods" class="exp-skin-table" width="95%">
	    <thead>
		<tr>
		    {$page->header_columns}
			<th>{'Actions'|gettext}</th>
		</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=listing name=listings}
			<tr class="{cycle values="odd,even"}">
				<td><a href="{link controller=news action=show id=$listing->id}">{$listing->title}</a></td>
				<td>{$listing->publish|format_date:"%B %e, %Y"}</td>
				<td>
				    {if $listing->unpublish == 0}
				        {'Unpublished'|gettext}
				    {else}
				        {'Expired'|gettext} - {$listing->unpublish|format_date:"%B %e, %Y"}
				    {/if}
				</td>
				<td>
				    {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
						<div class="item-actions">
							{if $permissions.edit == true}
								{icon action=edit record=$listing}
							{/if}
							{if $permissions.delete == true}
								{icon action=delete record=$listing}
							{/if}
						</div>
                    {/permissions}
				</td>
			</tr>
			{foreachelse}
			    <td colspan=3>{'There is no expired news'|gettext}.</td>
			{/foreach}
		</tbody>
	</table>
    {pagelinks paginate=$page bottom=1}
</div>
