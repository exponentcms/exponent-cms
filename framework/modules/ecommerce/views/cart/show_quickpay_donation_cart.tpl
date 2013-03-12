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

{css unique="show_quickpay" corecss="tables"}

{/css}

{if $items|@count > 0}
<table id="cart" class="exp-skin-table" width="100%" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th>{'Item'|gettext}</th>
        <th>{'Price'|gettext}</th>
        <th>&#160;</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$items item=item}
        <tr class="{cycle values="odd,even"}">
            <td class="prodrow item">
                 {get_cart_summary item=$item}
            </td>
            <td class="prodrow price" id="price-{$item->id}">{$item->products_price*$item->quantity|currency}</td>
            <!--<td class="prodrow price" id="price-{$item->id}">${$item->getTotal()|number_format:2}</td>-->
            <td class="prodrow">{icon img="../../../modules/ecommerce/assets/images/delete-from-cart.png" action=removeItem record=$item title="Remove from cart"|gettext onclick="return confirm('"|cat:("Are you sure you want to remove this item?"|gettext)|cat:"');"}</td>
        </tr>
    {/foreach}
    </tbody>
</table>
{else}
    <div class="no-items">
        {'You currently have no items in your cart'|gettext}
    </div>
{/if}
