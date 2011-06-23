{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
 {css unique="confirmation1" link="`$asset_path`css/ecom.css"}

 {/css}

 {css unique="confirmation2" link="`$asset_path`css/confirmation.css" corecss="button"}

 {/css}                                                

<div class="module cart confirm exp-ecom-table">
    <h1>{ecomconfig var='checkout_title_top' default="Confirm Your Secure Order"}</h1> 
    <div id="cart-message">{ecomconfig var='checkout_message_top' default=""}</div>
    {br}
    <div class="confirmationlinks">
        <a href="{if $nologin}{link controller=cart action=process nologin=1}{else}{link controller=cart action=process}{/if}" class="awesome {$smarty.const.BTN_SIZE} green next" />
            {"Looks good, submit my order!"|gettext} &raquo;
        </a>
        <a href="{link controller=cart action=checkout}" class="awesome {$smarty.const.BTN_SIZE} yellow back" />
            &laquo; {"Let me edit something"|gettext}
        </a>
    </div>
    {br}
    <div class="billinginfo">
        <h2>Billing Information</h2>
        <div class="payment-info">
            {$billinginfo}
        </div>
        <div class="address-info">
            <table border="0" cellspacing="0" cellpadding="0" class="">
                <thead>
                    <tr>
                        <th>
                            {"Billing Address"|gettext}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <address>
                            {$billing->address->firstname} {$billing->address->middlename} {$billing->address->lastname}{br}
                            {$billing->address->address1}{br}
                            {if $billing->address->address2}{$billing->address->address2}{br}{/if}
                            {$billing->address->city}, 
                            {* $billing->address->state|statename}, {$billing->address->zip *}
                            {if $billing->address->state == -2}
                                    {$billing->address->non_us_state}
                                {else}
                                    {$billing->address->state|statename:abv}
                                {/if}
                                 {$billing->address->zip}
                                {if $billing->address->state == -2}
                                    {br}{$billing->address->country|countryname}
                                {/if}
                            </address>      
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
            <table border="0" cellspacing="0" cellpadding="0" class="">
                <thead>
                    <tr>
                        <th>
                            <strong>{$shipping->shippingmethod->option_title}</strong> to:
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <address>
                            {$shipping->shippingmethod->firstname} {$shipping->shippingmethod->middlename} {$shipping->shippingmethod->lastname}{br}
                            {$shipping->shippingmethod->address1}{br}
                            {if $shipping->shippingmethod->address2}{$shipping->shippingmethod->address2}{br}{/if}
                            {$shipping->shippingmethod->city}, 
                            {* $shipping->shippingmethod->state|statename}, {$shipping->shippingmethod->zip *}
                            {if $shipping->shippingmethod->state == -2}
                                    {$shipping->shippingmethod->non_us_state}
                                {else}
                                    {$shipping->shippingmethod->state|statename:abv}
                                {/if}
                                 {$shipping->shippingmethod->zip}
                                {if $shipping->shippingmethod->state == -2}
                                    {br}{$shipping->shippingmethod->country|countryname}
                                {/if}
                            </address>      
                            {if $shipping->shippingmethod->to != "" || $shipping->shippingmethod->from != "" || $shipping->shippingmethod->message != ""}
                                {br}
                                <h4>Gift Message</h4>
                                <strong>To: </strong>{$shipping->shippingmethod->to}{br}
                                <strong>From: </strong>{$shipping->shippingmethod->from}{br}
                                <strong>Message: </strong>{$shipping->shippingmethod->message}{br}
                            {/if}
                        </td>
                    </tr>
                </tbody>
            </table>
        {/if}
    {/if}
    
    <!--div {if $shipping->splitshipping == true}class="hide"{/if}>
    <h2>Are you sending this order as gift?:</h2>
    <p>If you are send this order as a gift to someone you can put a note to the recipeint</p>
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
                &nbsp;-&nbsp;{$method->option_title} - ${$method->shipping_cost}
            </h4>
            {include file="../order/partial_summary.tpl" items=$method->orderitem}
        {/foreach}
    {else}
        <h2>{"You're purchasing"|gettext}</h2>
        <div class="purchased-items">
            {include file="../order/partial_summary.tpl" items=$order->orderitem}
        </div>
        <div class="purchased-totals">
             <div class=" order-total"> 
                <table class="collapse nowrap">
                    <thead>
                        <tr>
                            <th colspan="2">
                                Totals
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="even"><td class="right">Subtotal:</td><td class="totals subtotal">{currency_symbol}{$order->subtotal|number_format:2}</td></tr>
                        {if $order->total_discounts > 0}
                            <tr class="odd"><td class="right">Discounts:</td><td class="totals discounts">-{currency_symbol}{$order->total_discounts|number_format:2}</td></tr>
                            <tr class="even"><td class="right">Total:</td><td class="totals subtotal">{currency_symbol}{$order->total|number_format:2}</td></tr>
                        {/if}
                        <tr class="odd"><td class="right">Tax:</td><td class="totals tax">{currency_symbol}{$order->tax|number_format:2}</td></tr>
                        {if $order->shipping_required == true} 
                            <tr class="even">
                                <td class="right">Shipping:</td>
                                <td class="totals shipping">{currency_symbol}{$order->shipping_total_before_discounts|number_format:2}</td>                   
                            </tr>                    
                            {if $order->shippingDiscount > 0}
                                <tr class="odd">
                                    <td class="right">Shipping<br/>Discount:</td>
                                    <td class="totals shipping">{currency_symbol}-{$order->shippingDiscount|number_format:2}</td>
                                </tr>
                                <tr class="even">
                                    <td class="right">Total Shipping:</td>
                                    <td class="totals shipping">{currency_symbol}{$order->shipping_total|number_format:2}</td>
                                </tr>
                            {/if}
                        {/if}
                        {if $order->surcharge_total != 0} 
                        <tr class="even">
                            <td class="right">Freight Surcharge:</td>
                            <td class="totals shipping">{currency_symbol}{$order->surcharge_total|number_format:2}</td>
                        </tr>
                        {/if}
                        <tr class="odd"><td class="right">Final Total:</td><td class="totals total">{currency_symbol}{$order->grand_total|number_format:2}</td></tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
    {/if}
    </div>
    
    <div style="clear:both"></div>
    
    <div class="confirmationlinks">
        <a href="{if $nologin}{link controller=cart action=process nologin=1}{else}{link controller=cart action=process}{/if}" class="awesome {$smarty.const.BTN_SIZE} green next" />
            {"Looks good, submit my order!"|gettext} &raquo;
        </a>
        <a href="{link controller=cart action=checkout}" class="awesome {$smarty.const.BTN_SIZE} yellow back" />
            &laquo; {"Let me edit something"|gettext}
        </a>
    </div>
    <p align="center">
        <div style="width:100%; margin: auto;">
        {ecomconfig var='ssl_seal' default="" unescape="true"}
        </div>
    </p>

{ecomconfig var='checkout_message_bottom' default=""}
</div>
