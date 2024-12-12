{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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
    {if !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle} - {"Expired and Unpublished News"|gettext}</{$config.heading_level|default:'h1'}>{/if}
    {pagelinks paginate=$page top=1}
    {$myloc=serialize($__loc)}
	{form action=delete_expired}
		<table id="prods" class="exp-skin-table" width="95%">
			<thead>
				<tr>
					{$page->header_columns}
					<th>{'Actions'|gettext}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$page->records item=item name=listings}
					<tr class="{cycle values="odd,even"}">
						<td><input type=checkbox name=act-upon[] value={$item->id} /></td>
						<td><a href="{link controller=news action=show id=$item->id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></td>
						<td>{$item->publish_date|format_date:"%B %e, %Y"}</td>
						<td>
							{if $item->unpublish == 0}
								{'Unpublished'|gettext}
							{else}
								{'Expired'|gettext} - {$item->unpublish|format_date:"%B %e, %Y"}
							{/if}
						</td>
						<td>
							{permissions}
								<div class="item-actions">
									{if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
										{if $myloc != $item->location_data}
											{if $permissions.manage}
												{icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
											{else}
												{icon img='arrow_merge.png' title="Merged Content"|gettext}
											{/if}
										{/if}
										{icon action=edit record=$item}
										{icon action=copy record=$item}
									{/if}
									{if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
										{icon action=delete record=$item}
									{/if}
								</div>
							{/permissions}
						</td>
					</tr>
				{foreachelse}
					<td colspan=5>{'There is no expired news'|gettext}.</td>
				{/foreach}
			</tbody>
		</table>
        {if count($page->records)}
	    	{control class=delete type=buttongroup submit="Delete Selected Items"|gettext color=red onclick="return confirm('"|cat:("Are you sure you want to delete these items?"|gettext)|cat:"');"}
        {/if}
    {/form}
    {pagelinks paginate=$page bottom=1}
</div>
