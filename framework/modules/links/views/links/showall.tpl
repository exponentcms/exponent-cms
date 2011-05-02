{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
 
<div class="module links showall">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create == 1 || $permissions.edit == 1}
				{icon class=add action=create text="Create new link" title="Create a new link"|gettext}
			{/if}
			{if $permissions.manage == 1 && $rank == 1}
				{ddrerank items=$items model="links" label="Links"|gettext}
			{/if}
		</div>
    {/permissions}
    
	{foreach name=items from=$items item=item}
		<div class="item">     
			<h2><a class="li-link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{$item->title}</a></h2>
			{permissions}
				<div class="item-actions">
					{if $permissions.edit == 1}
						{icon action=edit record=$item title="Edit this `$modelname`"}
					{/if}
					{if $permissions.delete == 1}
						{icon action=delete record=$item title="Delete this `$modelname`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
					{/if}
				</div> 
			{/permissions}

			{if $item->expFile[0]->id}
				<a class="li-link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{img file_id=$item->expFile[0]->id width=200 height=150 constrain=1 style="float:left; margin-right:10px"}</a>
			{/if}
			{if $item->body}
				<div class="bodycopy">
					{$item->body}
				</div>
			{/if}
			{clear}
		</div>
	{/foreach}
</div>
