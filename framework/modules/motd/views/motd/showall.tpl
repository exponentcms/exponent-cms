{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{css unique="showallmotd" corecss="tables"}

{/css}

<div class="module motd showall">
    {if !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle|default:"Messages by day"|gettext}</h1>{/if}
    <div class="bodycopy">
        {$record->body}
    </div>
    {pagelinks paginate=$page top=1}
    {permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=edit text="Add a New Message"|gettext}
			{/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
    <table id="prods" class="exp-skin-table">
		<thead>
			<tr>
				{$page->header_columns}
                <th>{'Actions'|gettext}</th>
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
                                {if $myloc != $listing->location_data}
                                    {if $permissions.manage == 1}
                                        {icon action=merge id=$listing->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
								{icon action=edit record=$listing title="Edit this message"|gettext}
							{/if}
							{if $permissions.delete == 1}
								{icon action=delete record=$listing title="Delete this message"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this message?"|gettext)|cat:"');"}
							{/if}
						</div>
					{/permissions}  
				</td>                   
			</tr>
			{foreachelse}
				<tr class="{cycle values="odd,even"}">
				<td colspan="6">
					{'There are no messages yet.'|gettext}
				</td>                   
			</tr>
			{/foreach}
		</tbody>
    </table>
    {pagelinks paginate=$page bottom=1}
</div>
