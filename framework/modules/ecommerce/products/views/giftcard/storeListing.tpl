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
 
<div class="prod-listing">    
    <div class="bd">
        <div class="thimage">
            <a href="{link action=showByTitle title=$listing->sef_url}" title="{$listing->body|format_tooltip}">
                {if $listing->expFile.images[0]->id != ""}
                    {*img class=listingimage file_id=$listing->expFile[0]->id constraint=1 width=150 height=550 alt=$listing->title*}
                    {img class=listingimage file_id=$listing->expFile.images[0]->id square=149 alt=$listing->title}
                    {br}
                {else}
                    {'No Image'|gettext}
                {/if}
            </a>                    
        </div>
        <div class="bodycopy">
            <h2>
                <a href="{link action=showByTitle title=$listing->sef_url}">
                    {$listing->title}
                </a>
            </h2>
            <span class="description">{$listing->body|truncate:50:"..."}</span>
            <span class="price">${$listing->base_price|number_format:2}</span>
			<a href="{link controller=cart action=addItem product_id=$listing->id product_type=$listing->product_type}" class="fox-link addtocart" rel="nofollow"><em>Add to cart</em><span></span></a>
            
            {permissions}
            <div class="item-actions">
                {if $permissions.configure == 1 or $permissions.manage == 1}
                    <a href="{link action=edit id=$listing->id}" title="{"Edit this entry"|gettext}">
                        <img src="{$smarty.const.ICON_RELATIVE|cat:'edit.png'}" title="{"Edit this entry"|gettext}" alt="{"Edit this entry"|gettext}" />
                    </a>
                    {icon action=delete record=$listing title="Delete this product"|gettext}
                {/if}
            </div>
            {/permissions}
        </div>
    </div>
    <div class="ft">
    </div>
</div>
