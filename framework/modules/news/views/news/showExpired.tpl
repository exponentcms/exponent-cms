{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
    {if !$config.hidemoduletitle}<h1>{$moduletitle|default:"Expired News"|gettext}</h1>{/if}
    {assign var=myloc value=serialize($__loc)}
    {pagelinks paginate=$page top=1}
	<table id="prods">
	    <thead>
		<tr>
		    {$page->header_columns}
			<th>{'Admin'|gettext}</th>
		</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=listing name=listings}
			<tr class="{cycle values="odd,even"}">
				<td><a href="{link controller=news action=show id=$listing->id}" title="{$listing->body|summarize:"html":"para"}">{$listing->title}</a></td>
				<td>{$listing->publish_date|format_date:"%B %e, %Y"}</td>
				<td>{$listing->unpublish|format_date:"%B %e, %Y"}</td>
				<td>
				    {permissions}
						<div class="item-actions">
                            {if $myloc != $listing->location_data}{icon class=merge img='arrow_merge.png' title="Aggregated Content"|gettext}{/if}
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
