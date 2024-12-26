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

{if bs()}
{css unique="inline-cart" link="`$asset_path`css/inline-cart-bs.css"}

{/css}
{else}
{css unique="inline-cart" link="`$asset_path`css/inline-cart.css"}

{/css}
{/if}

<div class="module cart show-inline">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h2>{$moduletitle}</h2>{/if}
    <div class="total">
        {"Total"|gettext}: <span class="carttotal">{$order->total|currency}</span>
    </div>
    <ul>
        {foreach from=$items item=item}
            <li class="{cycle values="odd,even"}">
                <a class="image" href="{link controller=store action=show id=$item->product_id}">
                    {if $item->product->expFile.mainimage[0]->id}{img file_id=$item->product->expFile.mainimage[0]->id w=30 h=30 zc=1 class="border"}{/if}
                </a>
                <div class="p-info">
                    <a class="title" href="{link controller=store action=show id=$item->product_id}" title="{'View'|gettext} {$item->products_name}">
                        {$item->products_name}
                    </a>
                    {$item->quantity} @ <span class="price">{$item->products_price|currency}</span>
                </div>
                <a href="{link action=removeItem id=$item->id}" class="delete" title="Remove item from cart"|gettext onclick="return confirm('{'Are you sure you want to remove this item?'|gettext}');">{'Remove from cart'|gettext}</a>
                {clear}
            </li>
        {foreachelse}
            <li>
                {message class=notice text='Your cart is empty'|gettext}
            </li>
        {/foreach}
    </ul>
    <div class="module-actions" style="padding:8px; 0">
        {icon class="view" button=true size=large color=green controller=cart action=show text="View"|gettext title='View your Cart'|gettext}
        {if $items|@count gt 0}
            {icon class="shopping-cart" button=true size=large color=green controller=cart action=checkout secure=true text="Checkout"|gettext title='Checkout Now'|gettext}
        {/if}
    </div>
</div>
