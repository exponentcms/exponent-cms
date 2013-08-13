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

<div class="module store showall-featured-products">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
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

    {foreach from=$page->records item=listing name=listings}
        {if $listing->is_featured}
            <div class="featured-product">
                <div class="bodycopy">
                    <a href="{link controller=store action=show title=$listing->title}">
                        {if $listing->expFile.featured_image[0]->id != ""}
                            {img file_id=$listing->expFile.featured_image[0]->id constraint=1 w=165 alt=$listing->title}
                        {elseif $listing->expFile.images[0]->id != ""}
                            {img file_id=$listing->expFile.images[0]->id constraint=1 w=165 alt=$listing->title}
                        {else}
                            {img src="`$asset_path`images/no-image.jpg" constraint=1 w=165 alt=$listing->title}
                        {/if}
                        {br}{$listing->title}
                    </a>
                    {if $listing->featured_body != ""}
                        <div class="bodycopy">
                            {$listing->featured_body}
                        </div>
                    {/if}
                </div>
            </div>
        {/if}
    {foreachelse}
       {'No Products were found!'|gettext}
    {/foreach}
</div>
