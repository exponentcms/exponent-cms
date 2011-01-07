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

 {css unique="confirmation2" link="`$asset_path`css/confirmation.css"}

 {/css}

 

<div class="module cart confirm">
    <h1>{$moduletitle|default:"Confirm your order"}</h1> 

    <div class="billinginfo exp-ecom-table">
        <h2>Billing Information</h2>
        You will be paying by <strong>{$billing->calculator->payment_type}</strong>{br}{br}
        {$billinginfo}
    </div>
    
     <div class="exp-ecom-table order-total"> 
         <h2>Totals</h2>
        <table class="collapse nowrap">
            <tbody>
                <tr class="even"><td class="right">Subtotal:</td><td class="totals subtotal">{currency_symbol}{$order->subtotal|number_format:2}</td></tr>
                <tr class="odd"><td class="right">Discounts:</td><td class="totals discounts">-{currency_symbol}{$order->total_discounts|number_format:2}</td></tr>
                <tr class="even"><td class="right">Total:</td><td class="totals subtotal">{currency_symbol}{$order->total|number_format:2}</td></tr>
                <tr class="odd"><td class="right">Tax:</td><td class="totals tax">{currency_symbol}{$order->tax|number_format:2}</td></tr>
                {if $order->shipping_required == true} 
                <tr class="even">
                    <td class="right">Shipping:</td>
                    <td class="totals shipping">{currency_symbol}{$order->shipping_total|number_format:2}</td>
                </tr>
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
    
    <div class="shippinginfo">
    {if $order->shipping_required == true}
        <h2>Shipping Information</h2>
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
            <strong>{$shipping->shippingmethod->option_title}</strong> to:{br}
            <address>
            {$shipping->shippingmethod->firstname} {$shipping->shippingmethod->middlename} {$shipping->shippingmethod->lastname}{br}
            {$shipping->shippingmethod->address1}{br}
            {if $shipping->shippingmethod->address2}{$shipping->shippingmethod->address2}{br}{/if}
            {$shipping->shippingmethod->city}, {$shipping->shippingmethod->state|statename}, {$shipping->shippingmethod->zip}
            </address>      
            {clear}
            {if $shipping->shippingmethod->to != "" || $shipping->shippingmethod->from != "" || $shipping->shippingmethod->message != ""}
                    {br}
                    <h4>Gift Message</h4>
                    <strong>To: </strong>{$shipping->shippingmethod->to}{br}
                    <strong>From: </strong>{$shipping->shippingmethod->from}{br}
                    <strong>Message: </strong>{$shipping->shippingmethod->message}{br}
                {/if}
            {br}            
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
                {$method->address2} {$method->city}, {$method->state|statename:abbv} {$method->zip}
                &nbsp;-&nbsp;{$method->option_title} - ${$method->shipping_cost}
            </h4>
            {include file="../order/partial_summary.tpl" items=$method->orderitem}
        {/foreach}
    {else}
        {include file="../order/partial_summary.tpl" items=$order->orderitem}
    {/if}
    </div>
    
    <div class="confirmationlinks">
        <a href="{if $nologin}{link controller=cart action=process nologin=1}{else}{link controller=cart action=process}{/if}" class="exp-ecom-link next">
            <strong><em>Looks good, process it</em></strong>
        </a>
        <a href="{link controller=cart action=checkout}" class="exp-ecom-link back">
            <strong><em>Let me edit something</em></strong>
        </a>
    </div>
</div>
