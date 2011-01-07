{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
    <h3>
        <a href="{link controller=store action=showByTitle title=$listing->sef_url}">{$listing->title}</a>
    </h3>   

    <a href="{link controller=store action=showByTitle title=$listing->sef_url}" class="prod-img">
        {img file_id=$listing->expFile.mainimage[0]->id w=135}
    </a>
    
    <p class="bodycopy">
        {$listing->summary}
    </p>

    {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
    <div class="item-permissions">
        {if $permissions.edit == 1}
            {icon img=edit.png action=edit id=$listing->id title="Edit `$listing->title`"}
        {/if}
        {if $permissions.delete == 1}
            {icon img=delete.png action=delete id=$listing->id title="Delete `$listing->title`" onclick="return confirm('Are you sure you want to delete this product?');"}
        {/if}
        {if $permissions.edit == 1}
            {icon action="copyProduct" img="copy.png" title="Copy `$listing->title` " id=$listing->id}
        {/if}
    </div>
    {/permissions}

    
    <div class="prod-price"> 
        {if $listing->availability_type == 3}       
            Call for Price
        {else}                   
            {if $listing->use_special_price}
                <span style="font-size:14px; text-decoration: line-through;">{currency_symbol}{$listing->base_price|number_format:2}</span>
                <span style="color:red;">{currency_symbol}{$listing->special_price|number_format:2}</span>
            {else}
                {currency_symbol}{$listing->base_price|number_format:2}
            {/if}
        {/if}
    </div>
    
        {if $listing->availability_type != 3 && $listing->active_type == 0}
            <a href="{link controller=store action=showByTitle title=$listing->sef_url}" class="button">View Item</a>   
            
            
            {*if $listing->hasChildren()}   <~~~  something ain't right with child products. Returns True when it shoudln't. deal with it later.
                <a href="{link controller=store action=showByTitle title=$listing->sef_url}" class="exp-ecom-link view-item" rel="nofollow"><strong><em>View Item</em></strong></a>   
            {else}
                {form id="addtocart`$listing->id`" controller=cart action=addItem} 
                    {control type="hidden" name="product_id" value="`$listing->id`"}   
                    {control type="hidden" name="product_type" value="`$listing->product_type`"}
                    <a href="#" onclick="document.getElementById('addtocart{$listing->id}').submit(); return false;" class="exp-ecom-link wqty view-item" rel="nofollow"><strong><em>Add to Cart</em></strong></a>
                    {if $listing->parent_id == 0}
                        {control name="qty" type="text" value="`$listing->minimum_order_quantity`" size=3 maxlength=5 class="lstng-qty"}
                    {/if}
                 {/form}
            {/if*}
        {else}
            {* message here saying product not availalble...? *}
        {/if}
        
    <div style="clear:both"></div>
</div>
