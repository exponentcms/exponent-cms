{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{css unique="confirmation1" link="`$asset_path`css/ecom.css"}

{/css}
{css unique="confirmation2" link="`$asset_path`css/confirmation.css" corecss="button"}

{/css}
{css unique="cart" link="`$asset_path`css/cart.css" corecss="panels"}

{/css}

<div class="module cart confirm exp-ecom-table">
    {$breadcrumb = [
        0 => [
            "title" => "{'Summary'|gettext}",
            "link"  => makeLink(['controller'=>'cart','action'=>'show'])
        ],
        1 => [
            "title" => "{'Sign In'|gettext}",
            "link"  => ""
        ],
        2 => [
            "title" => "{'Shipping/Billing'|gettext}",
            "link"  => makeLink(['controller'=>'cart','action'=>'checkout'])
        ],
        3 => [
            "title" => "{'Confirmation'|gettext}",
            "link"  => ""
        ],
        4 => [
            "title" => "{'Complete'|gettext}",
            "link"  => ""
        ]
    ]}
    {breadcrumb items=$breadcrumb active=3 style=flat}
    <h1>{ecomconfig var='checkout_title_top' default="Confirm Your Secure Order"|gettext}</h1>
    <div id="cart-message">{ecomconfig var='checkout_message_top' default=""}</div>
    {br}
    <div class="confirmationlinks">
        <a href="{securelink controller=cart action=checkout}" class="{button_style color=yellow size=large} back">
            &laquo; {"Let me edit something"|gettext}
        </a>
        <a href="{if $nologin}{link controller=cart action=process nologin=1}{else}{link controller=cart action=process}{/if}"
           class="{button_style color=green size=large} next">
        {"Looks good, submit my order!"|gettext} &raquo;
        </a>
    </div>
    {br}
    <div class="billinginfo">
        <h2>{'Billing Information'|gettext}</h2>

        <div class="payment-info">
            {$billinginfo}
        </div>
        <div class="address-info">
            <table>
                <thead>
                    <tr>
                        <th>
                        {"Billing Address"|gettext}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="even">
                        <td>
                        {$order->billingmethod[0]->addresses_id|address}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="shippinginfo">
    {if $order->shipping_required == true}
        <h2>{"Shipping Information"|gettext}</h2>
        {if $shipping->splitshipping == true}
        {*foreach from=$shipping->splitmethods item=method}
            <h3>1 order via {$method->option_title} @ {$method->shipping_cost}</h3>
            <address>
            {$method->firstname} {$method->middlename} {$method->lastname}{br}
            {$method->address1}{br}
            {if $method->address2}{$method->address2}{br}{/if}
            {$method->city}, {$method->state|statename}, {$method->zip}
            </address>
            {clear}
            {if $method->to != "" || $method->from != "" || $method->message != ""}
                {br}
                <h4>Gift Message</h4>
                <strong>To: </strong>{$method->to}{br}
                <strong>From: </strong>{$method->from}{br}
                <strong>Message: </strong>{$method->message}{br}
            {/if}
            <ul>
            {foreach from=$method->orderitem item=item}
                <li> {$item->products_name} @ ${$item->products_price}
                    {if $item->opts[0]}
                    <h4>Selected Options</h4>
                    <ul style="padding:0 0 0 15px;margin:0 0 5px 0;">
                        {foreach from=$item->opts item=options}
                            <li>{$options[1]}</li>
                        {/foreach}
                    </ul>
                    {/if}

                </li>
            {/foreach}
            </ul>
            <hr>
        {/foreach*}
            {else}
            <table>
                <thead>
                    <tr>
                        <th>
                            <strong>{$shipping->shippingmethod->option_title}</strong> to:
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="even">
                        <td>
                            {$shipping->shippingmethod->addresses_id|address}
                            {if $shipping->shippingmethod->to != "" || $shipping->shippingmethod->from != "" || $shipping->shippingmethod->message != ""}
                                {br}
                                <h4>{'Gift Message'|gettext}</h4>
                                <strong>{'To:'|gettext} </strong>{$shipping->shippingmethod->to}{br}
                                <strong>{'From'|gettext}: </strong>{$shipping->shippingmethod->from}{br}
                                <strong>{'Message'|gettext}: </strong>{$shipping->shippingmethod->message}{br}
                            {/if}
                        </td>
                    </tr>
                </tbody>
            </table>
        {/if}
    {/if}

    <!--div {if $shipping->splitshipping == true}class="hide"{/if}>
    <h2>Are you sending this order as gift?:</h2>
    <p>If you are send this order as a gift to someone you can put a note to the recipient</p>
    </div-->
    {if $shipping->splitshipping == true}
        {foreach from=$shipping->splitmethods item=method}
        {*foreach from=$order->shippingmethods item=method name=methods*}
            <h4>
                Items shipping to {$method->firstname} {$method->lastname}
                {$method->organization}
                {$method->address1}
                {$method->address2} {$method->city},
            {* $method->state|statename:abbv} {$method->zip *}
                {if $method->state == -2}
                    {$method->non_us_state}
                    {else}
                    {$method->state|statename:abv}
                {/if}
                {$method->zip}
                {if $method->state == -2}
                    {br}{$method->country|countryname}
                {/if}
                &#160;-&#160;{$method->option_title} - {$method->shipping_cost|currency}
            </h4>
            {include file="../order/partial_summary.tpl" items=$method->orderitem}
        {/foreach}
    {else}
        <h2>{'You\'re purchasing'|gettext}</h2>
        {include file="../order/partial_summary.tpl" items=$order->orderitem}
        <div class=" order-total">
            <table class="nowrap">
                <thead>
                    <tr>
                        <th colspan="2">
                            {'Totals'|gettext}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="{cycle values="odd, even"}">
                        <td class="right">{'Subtotal'|gettext}:</td>
                        <td class="totals subtotal">{$order->subtotal|currency}</td>
                    </tr>
                        {if $order->total_discounts > 0}
                        <tr class="{cycle values="odd, even"}">
                            <td class="right">{'Discounts'|gettext}:</td>
                            <td class="totals discounts">-{$order->total_discounts|currency}</td>
                        </tr>
                        <tr class="{cycle values="odd, even"}">
                            <td class="right">{'Total'|gettext}:</td>
                            <td class="totals subtotal">{$order->total|currency}</td>
                        </tr>
                        {/if}
                    {if !$order->shipping_taxed}
                    <tr class="{cycle values="odd, even"}">
                        <td class="right">
                            {"Tax"|gettext} -
                            {foreach from=$order->taxzones item=zone}
                                {$zone->name} ({$zone->rate}%):
                            {foreachelse}
                                ({'N/A'|gettext}):
                            {/foreach}
                        </td>
                        <td class="totals tax">{$order->tax|currency}</td>
                    </tr>
                    {/if}
                        {if $order->shipping_required == true}
                        <tr class="{cycle values="odd, even"}">
                            <td class="right">{'Shipping'|gettext}:</td>
                            <td class="totals shipping">{$order->shipping_total_before_discounts|currency}</td>
                        </tr>
                            {if $order->shippingDiscount > 0}
                            <tr class="{cycle values="odd, even"}">
                                <td class="right">{'Shipping'|gettext}<br/>{'Discount'|gettext}:</td>
                                <td class="totals shipping">-{$order->shippingDiscount|currency}</td>
                            </tr>
                            <tr class="{cycle values="odd, even"}">
                                <td class="right">{'Total Shipping'|gettext}:</td>
                                <td class="totals shipping">{$order->shipping_total|currency}</td>
                            </tr>
                            {/if}
                        {/if}
                        {if $order->surcharge_total != 0}
                        <tr class="{cycle values="odd, even"}">
                            <td class="right">{'Freight Surcharge'|gettext}:</td>
                            <td class="totals shipping">{$order->surcharge_total|currency}</td>
                        </tr>
                        {/if}
                    {if $order->shipping_taxed}
                    <tr class="{cycle values="odd, even"}">
                        <td class="right">
                            {"Tax"|gettext} -
                            {foreach from=$order->taxzones item=zone}
                                {$zone->name} ({$zone->rate}%):
                            {foreachelse}
                                ({'N/A'|gettext}):
                            {/foreach}
                        </td>
                        <td class="totals tax">{$order->tax|currency}</td>
                    </tr>
                    {/if}
                    <tr class="{cycle values="odd, even"}">
                        <td class="right">{'Final Total'|gettext}:</td>
                        <td class="totals total">{$order->grand_total|currency}</td>
                    </tr>
                    {*</tr>*}
                </tbody>
            </table>
        </div>
    {/if}
    </div>
    {clear}{br}
    <div class="confirmationlinks">
        <a href="{securelink controller=cart action=checkout}" class="{button_style color=yellow size=large} back">
            &laquo; {"Let me edit something"|gettext}
        </a>
        <a href="{if $nologin}{link controller=cart action=process nologin=1}{else}{link controller=cart action=process}{/if}"
           class="{button_style color=green size=large} next">
        {"Looks good, submit my order!"|gettext} &raquo;
        </a>
    </div>
    <p align="center">
        <div style="width:100%; margin: auto;">
            {ecomconfig var='ssl_seal' default="" unescape="true"}
        </div>
    </p>

    {ecomconfig var='checkout_message_bottom' default=""}
</div>
