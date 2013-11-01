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

{css unique="storeListing" link="`$asset_path`css/storefront.css" corecss="button,clearfix"}

{/css}

<div class="module store showall showall-featured-products">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
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

    <div class="products ipr{$config.images_per_row|default:3} listing-row">
    {counter assign="ipr" name="ipr" start=1}
    {foreach from=$page->records item=listing name=listings}
        {if $listing->is_featured}
            {if $smarty.foreach.listings.first || $open_row}
                <div class="product-row">
                {$open_row=0}
            {/if}
            <div class="item featured-product product">
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
                            <span class="regular-price on-sale">{$listing->base_price|currency}</span>
                            <span class="sale-price">{$listing->special_price|currency}&#160;<sup>{"SALE!"|gettext}</sup></span>
                        {else}
                            <span class="regular-price">{$listing->base_price|currency}</span>
                        {/if}
                    {/if}
                </div>
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $listing->poster == $user->id)}
                            {icon action=edit record=$listing title="Edit `$listing->title`"}
                            {icon action=copyProduct class="copy" record=$listing text="Copy" title="Copy `$listing->title` "}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $listing->poster == $user->id)}
                            {icon action=delete record=$listing title="Delete `$listing->title`" onclick="return confirm('"|cat:("Are you sure you want to delete this product?"|gettext)|cat:"');"}
                        {/if}
                    </div>
                {/permissions}
                {if $listing->expFile.featured_image[0]->id != ""}
                    {img file_id=$listing->expFile.featured_image[0]->id constraint=1 w=165 alt=$listing->title}
                {elseif $listing->expFile.images[0]->id != ""}
                    {img file_id=$listing->expFile.images[0]->id constraint=1 w=165 alt=$listing->title}
                {else}
                    {img src="`$asset_path`images/no-image.jpg" constraint=1 w=165 alt=$listing->title}
                {/if}
                <div class="bodycopy">
                    <h3><a href="{link controller=store action=show title=$listing->title}">{$listing->title}</a></h3>
                    {if $listing->featured_body != ""}
                        <div class="bodycopy">
                            {$listing->featured_body}
                        </div>
                    {/if}
                </div>
            </div>
            {if $smarty.foreach.listings.last || $ipr%$config.images_per_row==0}
                </div>
                {$open_row=1}
            {/if}
            {counter name="ipr"}
        {/if}
    {foreachelse}
        {'No Products were found!'|gettext}
    {/foreach}
    </div>
</div>
