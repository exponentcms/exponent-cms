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

<div class="module store showall-subcategories">
    {$depth=0}
        {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
        {permissions}
        <div class="module-actions">
            {if $permissions.create == true || $permissions.edit == true}
                {icon class="add" action=create text="Add a Product"|gettext}
            {/if}
            {if $permissions.manage == 1}
                {icon action=manage text="Manage Products"|gettext}
                {icon controller=storeCategory action=manage text="Manage Store Categories"|gettext}
            {/if}
        </div>
        {/permissions}
        {if $config.moduledescription != ""}
            {$config.moduledescription}
        {/if}
        {$myloc=serialize($__loc)}

    <div id="catnav">
        <ul>
            <li><a href="{link controller=store action=showall}">{'Browse all Products'|gettext}</a></li>
            {foreach from=$ancestors item=ancestor name=path}
                {$depth=$smarty.foreach.path.iteration*10}
                <li style="margin-left: {$depth}px">
                    {if $ancestor->id != $category->id}
                        <a href="{link controller=store action=showall title=$ancestor->sef_url}">{$ancestor->title}</a>
                    {else}
                        <strong>{$ancestor->title}</strong>
                    {/if}
                </li>
                {/foreach}      
            {$childdepth=$depth+10}
            {foreach from=$categories item=category}
                <li style="margin-left: {$childdepth}px">
                    <a href="{link controller=store action=showall title=$category->sef_url}">{$category->title}</a> <span class="productsincategory">({$category->product_count})</span>
                </li>
            {/foreach}          
            {br}
            {if $user->is_admin == 1 || $user->is_acting_admin}
                <li><a href="{link controller=store action=showallUncategorized}">{'Show uncategorized products'|gettext}</a></li>
            {/if}
        </ul>
    </div>
</div>
