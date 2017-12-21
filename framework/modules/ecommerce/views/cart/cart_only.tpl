{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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
                <th>{'Item'|gettext}</th>
                <th>{'Price'|gettext}</th>
                <th>{'Amount'|gettext}</th>
                <th>{'Quantity'|gettext}</th>
                <th>{'Action'|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$items item=item}
                <tr class="{cycle values="odd,even"}">
                    <td class="prodrow">
                         {get_cart_summary item=$item}
                    </td>
                    <td class="prodrow price" id="price-{$item->id}">{$item->products_price|currency}</td>
                    <td class="prodrow price" id="amount-{$item->id}">{$item->getTotal()|currency}</td>
                    <td class="prodrow quantity">
                        {if $item->product->isQuantityAdjustable}
                            {form action="updateQuantity" controller=cart}
                                {control type="hidden" name="id" value=$item->id}
                                <table class="quantity-controller" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td><input class="form-control" type="text" size="2" name="quantity" value="{$item->quantity}"></td>
                                        <td><input class="refresh-quantity" type="image" name="id" value="{$item->id}" src="{$smarty.const.PATH_RELATIVE|cat:'framework/modules/ecommerce/assets/images/update.png'}" title="{'Update quantities'|gettext}" alt="{'Update quantities'|gettext}"></td>
                                    </tr>
                                </table>
                            {/form}
                        {else}
                            {$item->quantity}
                        {/if}
                    </td>
                    <td class="prodrow">
                        <a href="{link action=removeItem id=$item->id}" title="{'Remove'|gettext} {$item->product->title} {'from cart'|gettext}" onclick="return confirm('{'Are you sure you want to remove this item?'|gettext}');">
                            <img src='{$asset_path}images/remove.png' alt="{'Remove'|gettext}" />
                        </a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <div class="no-items">
        {'You currently have no items in your cart'|gettext}
    </div>
{/if}
