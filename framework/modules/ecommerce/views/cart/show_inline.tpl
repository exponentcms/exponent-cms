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

{css unique="inline-cart" link="`$asset_path`css/inline-cart.css"}

{/css}

<div class="module cart show-inline">
    {if $moduletitle && !$config.hidemoduletitle}<h2>{$moduletitle}</h2>{/if}
    <div class="total">
        {"Total"|gettext}: <span class="carttotal">{currency_symbol}{$order->total|number_format:2}</span>
    </div>
    <ul>
        {foreach from=$items item=item}
            <li class="{cycle values="odd,even"}">
                <a class="image" href="{link controller=store action=show id=$item->product_id}">
                    {if $item->product->expFile.mainimage[0]->id}{img file_id=$item->product->expFile.mainimage[0]->id  w=30 h=30 zc=1 class="border"}{/if}
                </a>
                <div class="p-info">
                    <a class="title" href="{link controller=store action=show id=$item->product_id}">
                    {$item->products_name}
                    </a>
                    {$item->quantity} @ <span class="price">{currency_symbol}{$item->products_price|number_format:2}</span>
                </div>
                <a href="{link action=removeItem id=$item->id}" class="delete">Remove from cart</a>
                {clear}
            </li>
        {foreachelse}
            <li>{'You currently have no items in your cart'|gettext}</li>
        {/foreach}
    </ul>
</div>
