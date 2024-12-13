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

<div class="module store show-top-level">
    {$depth=0}
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
        {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class="add" action=create text="Add a Product"|gettext}
            {/if}
            {if $permissions.manage}
                {icon action=manage text="Manage Products"|gettext}
                {icon controller=storeCategory action=manage text="Manage Store Categories"|gettext}
            {/if}
        </div>
        {/permissions}
        {if $config.moduledescription != ""}
            {$config.moduledescription}
        {/if}
        {$myloc=serialize($__loc)}

        {if $current_category->title}<{$config.item_level|default:'h2'}>{$current_category->title}</{$config.item_level|default:'h2'}>{/if}
        {if $current_category->id}
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit}
                        {icon class="edit" action=edit module=storeCategory id=$current_category->id title="Edit `$current_category->title`" text="Edit this Store Category"}{br}
                    {/if}
                    {if $permissions.manage}
                        {icon class="configure" action=configure module=storeCategory id=$current_category->id title="Configure `$current_category->title`" text="Configure this Store Category"}{br}
                    {/if}
                    {*{if $permissions.manage}*}
                        {*{icon class="configure" action=configure module=ecomconfig hash="#tab2" title="Configure Categories Globally" text="Configure Categories Globally"}{br}*}
                    {*{/if}*}
                    {if $permissions.edit && $config.orderby=="rank"}
                        {ddrerank label="Products"|gettext sql=$rerankSQL model="product" controller="storeCategory" id=$current_category->id}
                    {/if}
                </div>
            {/permissions}
        {/if}
	<div id="catnav" class="catnav">
		<ul>
			{foreach from=$categories item=category}
    			{if $category->is_active==1 || $user->is_acting_admin}
                    <li style="margin-left: {$category->depth * 10}px" class="{if $curcat->id==$category->id}current{/if}{if $category->is_active!=1} inactive{/if}">
                        <a href="{link controller=store action=showall title=$category->sef_url}">{$category->title} <span class="productsincategory">{$category->product_count}</span></a>
                    </li>
				{/if}
			{/foreach}
		</ul>
	</div>
</div>
