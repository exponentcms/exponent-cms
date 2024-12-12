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

{css unique="showcartonly" corecss="tables"}

{/css}

{if $items|@count > 0}
    <table id="cart" width="100%" cellpadding="0" cellspacing="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>{'Item'|gettext}</th>
                <th>{'Price'|gettext}</th>
                <th style="width:150px;text-align:center;">{'Quantity'|gettext}</th>
                <th style="text-align:right;">{'Amount'|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$items item=item}
                <tr class="{cycle values="odd,even"}">
                    <td class="prodrow">
                         {get_cart_summary item=$item}
                    </td>
                    <td class="prodrow price" id="price-{$item->id}">{$item->products_price|currency}</td>
                    <td class="prodrow quantity">
                        {if $item->product->isQuantityAdjustable}
                            {form class="cart-qty" action="updateQuantity" controller=cart}
                                <div class="input-group">
                                    <input class="form-control" type="text" size="2" name="quantity" value="{$item->quantity}">
                                    <input type="hidden" name="id" value="{$item->id}">
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-outline-secondary" title="{'Update quantities'|gettext}"><i class="{if $smarty.const.USE_BOOTSTRAP_ICONS}bi-arrow-repeat bi-lg{else}fas fa-sync fa-lg{/if} text-success" aria-hidden="true"></i></button>
                                        <a href="{link action=removeItem id=$item->id}" class="btn btn-outline-secondary" title="{'Remove'|gettext} {$item->product->title} {'from cart'|gettext}" onclick="return confirm('{'Are you sure you want to remove this item?'|gettext}');" style="display:inline-block;margin: 0 2px 2px -1px;">
                                            <i class="{if $smarty.const.USE_BOOTSTRAP_ICONS}bi-x-circle bi-lg{else}far fa-times-circle fa-lg{/if} text-danger" aria-hidden="true"></i>
                                        </a>
                                    </span>
                                </div>
                            {/form}
                        {else}
                            {$item->quantity}
                        {/if}
                    </td>
                    <td class="prodrow price" id="amount-{$item->id}" style="text-align: right;">{$item->getTotal()|currency}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{else}
    <div class="no-items">
        {'You currently have no items in your cart'|gettext}
    </div>
{/if}
