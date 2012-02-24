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

<div class="module store showall-featured-products">
    
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
        
    {foreach from=$page->records item=listing name=listings}
    <div class="featured-product">
            {if $listing->expFile.featured_image[0]->id != ""}
                {img file_id=$listing->expFile.featured_image[0]->id constraint=1 width=165 alt=$listing->title}
            {elseif $listing->expFile.images[0]->id != ""}
                {img file_id=$listing->expFile.images[0]->id constraint=1 width=165 alt=$listing->title}
            {else}
                {'No Image'|gettext}
            {/if}
        <div class="bodycopy">
            <a href="{link controller=store action=showByTitle title=$listing->title}">
                {$listing->title}
            </a>
        </div>
    </div>
    {/foreach}
        
</div>
