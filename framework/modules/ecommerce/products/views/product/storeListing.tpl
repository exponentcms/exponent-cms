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

<div class="product">
    {if $listing->availability_type != 3 && $listing->active_type == 0}
        <a href="{link controller=store action=show title=$listing->sef_url}" class="exp-ecom-link awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}">{"View Item"|gettext}</a>
        {*if $listing->hasChildren()}            
            <a href="{link controller=store action=show title=$listing->sef_url}" class="exp-ecom-link awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}">{"View Item"|gettext}</a>
        {else}
            {form id="addtocart`$listing->id`" controller=cart action=addItem} 
                {control type="hidden" name="product_id" value="`$listing->id`"}   
                {control type="hidden" name="product_type" value="`$listing->product_type`"}
                <button type="submit" class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}">{"Add to Cart"|gettext}</button>
                {if $listing->parent_id == 0}
                    {control name="qty" type="text" value="`$listing->minimum_order_quantity`" size=3 maxlength=5 class="lstng-qty"}
                {/if}
             {/form}
        {/if*}
    {else}
        {if $listing->active_type == 1}
            <a href="{link controller=store action=show title=$listing->sef_url}" class="exp-ecom-link awesome {$smarty.const.BTN_SIZE} grey">{"View Item"|gettext}</a>
        {elseif $listing->active_type == 2 && $user->isAdmin()}
            <a href="{link controller=store action=show title=$listing->sef_url}" class="exp-ecom-link awesome {$smarty.const.BTN_SIZE} red">{"View Item"|gettext}</a>
        {/if}
    {/if}
    <div class="prod-price"> 
        {if $listing->availability_type == 3}       
            {"Call for Price"|gettext}
        {else}                   
            {if $listing->use_special_price}
                <span class="regular-price on-sale">{currency_symbol}{$listing->base_price|number_format:2}</span>
                <span class="sale-price">{currency_symbol}{$listing->special_price|number_format:2}&#160;<sup>{"SALE!"|gettext}</sup></span>
            {else}
                <span class="regular-price">{currency_symbol}{$listing->base_price|number_format:2}</span>
            {/if}
        {/if}
    </div>

    {permissions}
        <div class="item-actions">
            {if $permissions.edit == 1}
                {icon action=edit record=$listing title="Edit `$listing->title`"}
            {/if}
            {if $permissions.delete == 1}
                {icon action=delete record=$listing title="Delete `$listing->title`" onclick="return confirm('"|cat:("Are you sure you want to delete this product?"|gettext)|cat:"');"}
            {/if}
            {if $permissions.edit == 1}
                {icon action=copyProduct class="copy" record=$listingtitle text="Copy" title="Copy `$listing->title` " record=$listing}
            {/if}
        </div>
    {/permissions}

    <a href="{link controller=store action=show title=$listing->sef_url}" class="prod-img">
        {img file_id=$listing->expFile.mainimage[0]->id w=140 h=150}
    </a>

    <h3>
        <a href="{link controller=store action=show title=$listing->sef_url}">{$listing->title}</a>
    </h3>   

    <!-- a href="{link controller=store action=show title=$listing->sef_url}" class="prod-img">
        {img file_id=$listing->expFile.mainimage[0]->id w=135}
    </a>
    
    <p class="bodycopy">
        {$listing->summary}
    </p -->
</div>
