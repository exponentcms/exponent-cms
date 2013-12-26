{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="exp-ecom-table columnize">
    <table>
        <thead>
            <tr>
                <th>{'Item'|gettext}</th>
                <th>{'Model/SKU'|gettext}</th>
                <th>{'Qty'|gettext}</th>
                <th>{'Item Price'|gettext}</th>
                <th>{'Total Price'|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$items item=oi}
                <tr class={cycle values="even,odd"}>
                    <td>
                        {if $oi->product_type == "product" || $oi->product_type == "childProduct"}
                            <a href='{link action="show" controller="store" title="`$oi->product->getSEFURL()`"}'>
                                {$oi->products_name}
                            </a>
                        {else}
                            {$oi->products_name}
                        {/if}

                        {if $oi->opts[0]}
                            <ul class="prod-opts-summary">
                                {foreach from=$oi->opts item=options}
                                    <li>{$oi->getOption($options)}</li>
                                {/foreach}
                            </ul>
                        {/if}
                        {$oi->getUserInputFields('list')}

                        {*{if $oi->product_type == "product" || $oi->product_type == "childProduct"}*}
                            {$oi->getExtraData()}
                        {*{else}*}
                            {*{$oi->getFormattedExtraData('list')}*}
                        {*{/if}*}
                        {$oi->getShippingSurchargeMessage()}
                    </td>
                    <td>
                        {if $oi->product->model != ""}{$oi->product->model}{else}N/A{/if}
                    </td>
                    <td>{$oi->quantity}</td>
                    <td>{$oi->products_price|currency}</td>
                    <td>{$oi->getTotal()|currency}</td>
                </tr>
            {/foreach}
            {if $show_totals == 1}
                <tr>
                    <td colspan="4" class="totals top-brdr">{'Subtotal'|gettext}</td>
                    <td class="top-brdr">{$order->subtotal|currency}</td>
                </tr>
                {if $order->total_discounts > 0}
                    <tr>
                        <td colspan="4" class="totals">{'Discounts'|gettext}</td>
                        <td align="right">-{$order->total_discounts|currency}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="totals">{'Total'|gettext}</td>
                        <td align="right">{$order->total|currency}</td>
                    </tr>
                {/if}
                <tr>
                    <td colspan="4" class="totals">
                        Tax:
                        {foreach from=$order->taxzones item=zone}
                            {br}{$zone->name} ({$zone->rate}%)
                        {foreachelse}
                            ({'Not Required'|gettext})
                        {/foreach}
                    </td>
                    <td>{$order->tax|currency}</td>
                </tr>
                <tr>
                    <td colspan="4" class="totals">{'Shipping'|gettext}</td>
                    <td>{$order->shipping_total|currency}</td>
                </tr>
                <tr>
                    <td colspan="4" class="totals">{'Order Total'|gettext}</td>
                    <td>{$order->grand_total|currency}</td>
                </tr>
                </tr>
            {/if}
        </tbody>
    </table>
</div>
 