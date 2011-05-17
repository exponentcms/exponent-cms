{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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

{css unique="showcartonly" corecss="tables"}

{/css}

{if $items|@count > 0}
<table id="cart" width="100%" cellpadding="0" cellspacing="0" class="exp-skin-table">
    <thead>
    <tr>
        <th>Item</th>
        <th>Price</th>
        <th>Amount</th>  
        <th>Quantity</th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$items item=item}
    <tr class="{cycle values="odd,even"}">
        <td class="prodrow">
             {get_cart_summary item=$item}
        </td>
        <!--td class="prodrow price" id="price-{$item->id}">${$item->products_price*$item->quantity|number_format:2}</td-->
        <td class="prodrow price" id="price-{$item->id}">${$item->products_price|number_format:2}</td>
        <td class="prodrow price" id="amount-{$item->id}">${$item->getTotal()|number_format:2}</td>
        <td class="prodrow quantity">
            {if $item->product->isQuantityAdjustable}
            {form action="updateQuantity" controller=cart}
            {control type="hidden" name="id" value=$item->id}
            <table class="quantity-controller" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td><input type="text" size="2" name="quantity" value="{$item->quantity}"></td>
                    <td><input class="refresh-quantity" type="image" name="id" value="{$item->id}" src="{$smarty.const.URL_FULL}themes/common/skin/ecom/update.png" alt="Update quantity of this item."></td>
                </tr>
            </table>
             
            {/form}
            {else}
            
            {/if}
        </td>
        <td class="prodrow">
            <a href="{link action=removeItem id=$item->id}" title="remove `$item->title` from cart">
                <img src='{$asset_path}images/remove.png' alt=" " />
            </a>
        </td>
    </tr>
    {/foreach}
    </tbody>
</table>
{else}
<div class="no-items">
    You don't have any items in your cart.
</div>
{/if}
