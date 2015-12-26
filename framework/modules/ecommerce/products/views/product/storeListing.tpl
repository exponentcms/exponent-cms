{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
    <div class="ecom-hover">
        {if $listing->availability_type != 3 && $listing->active_type == 0}
            <a href="{link controller=store action=show title=$listing->sef_url}" class="exp-ecom-link {button_style color=blue size=large}">{"View Item"|gettext}</a>
            {*{if $listing->hasChildren()}*}
                {*<a href="{link controller=store action=show title=$listing->sef_url}" class="exp-ecom-link {button_style}">{"View Item"|gettext}</a>*}
            {*{else}*}
                {*{form id="addtocart`$listing->id`" controller=cart action=addItem}*}
                    {*{control type="hidden" name="product_id" value="`$listing->id`"}*}
                    {*{control type="hidden" name="product_type" value="`$listing->product_type`"}*}
                    {*<button type="submit" class="{button_style}">{"Add to Cart"|gettext}</button>*}
                    {*{if $listing->parent_id == 0}*}
                        {*{control name="qty" type="text" value="`$listing->minimum_order_quantity`" size=3 maxlength=5 class="lstng-qty"}*}
                    {*{/if}*}
                 {*{/form}*}
            {*{/if}*}

            {if !$listing->hasChildren()}
                <div class="addtocart">
                    {form id="addtocart`$listing->id`" controller=cart action=addItem}
                        {control type="hidden" name="product_id" value="`$listing->id`"}
                        {control type="hidden" name="product_type" value="`$listing->product_type`"}
                        {*control name="qty" type="text" value="`$listing->minimum_order_quantity`" size=3 maxlength=5 class="lstng-qty"*}

                        {*FIXME we will display these in the addToCart view anyway*}
                        {*{if $listing->hasOptions()}*}
                            {*<div class="product-options">*}
                                {*{foreach from=$listing->optiongroup item=og}*}
                                    {*{if $og->hasEnabledOptions()}*}
                                        {*<div class="option {cycle values="odd,even"}">*}
                                            {*<h4>{$og->title}</h4>*}
                                            {*{optiondisplayer product=$listing options=$og->title view=$og->allow_multiple display_price_as=diff selected=$params.options required=$og->required}*}
                                        {*</div>*}
                                    {*{/if}*}
                                {*{/foreach}*}
                            {*</div>*}
                        {*{/if}*}

                       <div class="add-to-cart-btn input">
                           {if $listing->availability_type == 0 && $listing->active_type == 0}
                               <input type="text" class="text form-control" size="5" value="{$listing->minimum_order_quantity|default:1}" name="quantity">
                               <button type="submit" class="add-to-cart-btn {button_style color=blue size=large}" rel="nofollow">
                                   {"Add to Cart"|gettext}
                               </button>
                           {elseif $listing->availability_type == 1 && $listing->active_type == 0}
                               <input type="text" class="text form-control" size="5" value="{$listing->minimum_order_quantity|default:1}" name="quantity">
                               <button type="submit" class="add-to-cart-btn {button_style color=blue size=large}" rel="nofollow">
                                   {"Add to Cart"|gettext}
                               </button>
                               {if $listing->quantity <= 0}<span class="error">{$listing->availability_note}</span>{/if}
                           {elseif $listing->availability_type == 2}
                               {if $listing->quantity - $listing->minimum_order_quantity >= 0}
                                   <input type="text" class="text form-control" size="5" value="{$listing->minimum_order_quantity|default:1}" name="quantity">
                                   <button type="submit" class="add-to-cart-btn {button_style color=blue size=large}" rel="nofollow">
                                       {"Add to Cart"|gettext}
                                   </button>
                               {else}
                                   {if $user->isAdmin()}
                                       <input type="text" class="text form-control" size="5" value="{$listing->minimum_order_quantity|default:1}" name="quantity">
                                       <button type="submit" class="add-to-cart-btn {button_style color=red size=large}" rel="nofollow">
                                           {"Add to Cart"|gettext}
                                       </button>
                                   {/if}
                                   <span class="error">{$listing->availability_note}</span>
                               {/if}
                           {elseif $listing->active_type == 1}
                               {if $user->isAdmin()}
                                   <input type="text" class="text form-control" size="5" value="{$listing->minimum_order_quantity|default:1}" name="quantity">
                                   <button type="submit" class="add-to-cart-btn {button_style color=red size=large}" rel="nofollow">
                                       {"Add to Cart"|gettext}
                                   </button>
                               {/if}
                               <em class="unavailable">{"Product currently unavailable for purchase"|gettext}</em>
                           {/if}
                       </div>
                    {/form}
                </div>
            {/if}

        {else}
            {if $listing->active_type == 1}
                <a href="{link controller=store action=show title=$listing->sef_url}" class="exp-ecom-link {button_style color=grey size=large}">{"View Item"|gettext}</a>
            {elseif $listing->active_type == 2 && $user->isAdmin()}
                <a href="{link controller=store action=show title=$listing->sef_url}" class="exp-ecom-link {button_style color=red size=large}">{"View Item"|gettext}</a>
            {/if}
        {/if}
    </div>
    <div class="product-listing">
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

        <a href="{link controller=store action=show title=$listing->sef_url}" class="prod-img">
            {if $listing->expFile.mainimage[0]->id != ""}
                {img file_id=$listing->expFile.mainimage[0]->id constraint=1 w=$config.listingwidth|default:140 h=$config.listingheight|default:150 alt=$listing->title}
            {else}
                {img src="`$asset_path`images/no-image.jpg" constraint=1 w=$config.listingwidth|default:140 h=$config.listingheight|default:150 alt="'No Image Available'|gettext"}
            {/if}
        </a>

        <h3>
            <a href="{link controller=store action=show title=$listing->sef_url}">{$listing->title}</a>
        </h3>

        <!-- a href="{link controller=store action=show title=$listing->sef_url}" class="prod-img">
            {img file_id=$listing->expFile.mainimage[0]->id w=135}
        </a -->
        <div class="bodycopy">
            {$listing->body}
        </div>
    </div>
</div>
