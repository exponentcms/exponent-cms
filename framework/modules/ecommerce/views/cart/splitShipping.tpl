{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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

{css unique="splitshipping" corecss="tables"}

{/css}

<div class="module cart split-shipping">
    <h1>{$moduletitle|default:"Choose which addresses to ship your items."|gettext}</h1>

    {if $addresses_dd|@count < 1}
        <a href="{link controller=address action=create}">
            {'It doesn\'t appear you have any addresses setup yet.  Click here to add an address.'|gettext}
        </a>
    {else}
        {form action="saveSplitShipping"}
            {foreach from=$order->orderitem item=orderitem}
                {if $orderitem->product->requiresShipping == true}
                    <table class="split-shipping exp-skin-table">
                        <thead>
                            <tr>
                                <th class="product-name" colspan="2">
                                    <h2>{$orderitem->products_name} - <span class=price>{$orderitem->products_price|currency}</span></h2>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {section name=quantity start=0 loop=$orderitem->quantity}
                            <tr class="{cycle values="odd,even"}">
                                <td>{$smarty.section.quantity.iteration}.</td>
                                <td>{control class="splitdd" type="dropdown" name="orderitems[`$orderitem->id`][]" label=" " items=$addresses_dd value=$orderitem->shippingmethod->addresses_id}</td>
                            </tr>
                            {/section}
                        <tbody>
                    </table>
                {/if}
            {/foreach}
            {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
        {/form}
    {/if}
</div>
