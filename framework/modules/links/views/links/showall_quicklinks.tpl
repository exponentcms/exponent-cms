{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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
 
<div class="module links showall-quicklinks">
    {if $moduletitle}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1 || $permissions.edit == 1}
				{icon class=add action=create text="Add new link"|gettext title="Add a new link"|gettext}
			{/if}
			{if $permissions.manage == 1 && $rank == 1}
				{ddrerank items=$items model="links" label="Links"|gettext}
			{/if}
        </div>
    {/permissions}
    <ul>
        {foreach name=items from=$items item=item name=links}
		<li{if $smarty.foreach.links.last} class="item last"{/if}>
			<a class="link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{$item->title}</a>
			{permissions}
				<div class="item-actions">
					{if $permissions.edit == 1}
						{icon action=edit record=$item}
					{/if}
					{if $permissions.delete == 1}
						{icon action=delete record=$item}
					{/if}
				</div>
			{/permissions}
		</li>
        {/foreach}
    </ul>
</div>
