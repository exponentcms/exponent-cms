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

<div class="module order print-invoice">
    {printer_friendly_link text="Print this invoice"|gettext show=1}
    {br}{br}
	<table width=100% border="0" cellspacing="5" cellpadding="5">
		<thead>
            <tr>
                <th colspan=2>
                    <h1>{'Invoice #'|gettext} {$order->invoice_id}</h1>
                    <span>{'Order was placed on:'|gettext} {$order->purchased|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}</span>
                </th>
            </tr>
		</thead>
		<tbody>
            <tr>
                <td>
                    <h2>{'Shipping Address'|gettext}</h2>
                    {if $order->shipping_required == true}
                        {if $order->shippingmethods|@count <= 1}
                            <address>
                            {$shipping->shippingmethod->firstname} {$shipping->shippingmethod->lastname}<br>
                            {if $shipping->shippingmethod->company != ""}{$shipping->shippingmethod->company}<br>{/if}
                            {if $shipping->shippingmethod->address1 != ""}{$shipping->shippingmethod->address1}<br>{/if}
                            {if $shipping->shippingmethod->address2 != ""}{$shipping->shippingmethod->address2}<br>{/if}
                            {$shipping->shippingmethod->city},&#160;
                            {if $shipping->shippingmethod->state == -2}
                                 {$shipping->shippingmethod->non_us_state}
                            {elseif $shipping->shippingmethod->state != ""}
                                 {$shipping->shippingmethod->state|statename:abbv}
                            {/if} {$shipping->shippingmethod->zip}
                            {if $shipping->shippingmethod->state == -2}
                                {br}{$shipping->shippingmethod->country|countryname}
                            {/if}
                            </address>
                            <br>
                            {if $shipping->shippingmethod->phone_number != ""}{$shipping->shippingmethod->phone_number}<br>{/if}
                            {if $shipping->shippingmethod->email != ""}{$shipping->shippingmethod->email}<br>{/if}
                            <br>
                        {else}
                            {'See shipping information below.'|gettext}
                        {/if}
                    {else}
                        {'No shipping required for this order.'|gettext}
                    {/if}
                </td>
                <td>
                    <h2>{'Billing Address'|gettext}</h2>
                    <address>
                    {$order->billingmethod[0]->firstname} {$order->billingmethod[0]->lastname}<br>
                    {if $order->billingmethod[0]->company != ""}{$order->billingmethod[0]->company}<br>{/if}
                    {if $order->billingmethod[0]->address1 != ""}{$order->billingmethod[0]->address1}{br}{/if}
                    {if $order->billingmethod[0]->address2 != ""}{$order->billingmethod[0]->address2}<br>{/if}
                    {$order->billingmethod[0]->city},&#160;
                            {if $order->billingmethod[0]->state == -2}
                                 {$order->billingmethod[0]->non_us_state}
                            {elseif $order->billingmethod[0]->state != ""}
                                 {$order->billingmethod[0]->state|statename:abbv}
                            {/if} {$order->billingmethod[0]->zip}
                            {if $order->billingmethod[0]->state == -2}
                                {br}{$order->billingmethod[0]->country|countryname}
                            {/if}
                    {br}{$order->billingmethod[0]->phone}
                    </address>
                    <br>
                    {if $order->billingmethod[0]->phone_number != ""}{$order->billingmethod[0]->phone_number}<br>{/if}
                    {if $order->billingmethod[0]->email != ""}{$order->billingmethod[0]->email}<br>{/if}
                    <br>
                </td>
            </tr>
            <tr>
                <td>
                    <h2>{'Shipping Method'|gettext}</h2>
                    {if $order->shipping_required == true}
                        {if $order->shippingmethods|@count <= 1}
                            {$shipping->calculator->title} ({$shipping->shippingmethod->option_title}) - {$shipping->shippingmethod->shipping_cost|currency}
                            <br><br><br><br>
                         {else}
                            {'See shipping information below.'|gettext}
                         {/if}
                    {else}
                        {'No shipping required for this order.'|gettext}
                    {/if}
                </td>
                <td>
                    <h2>{'Payment Method'|gettext}</h2>
                    {$billing->info}
                </td>
            </tr>
            <tr>
                <td colspan="2">{'Order Details'|gettext}</td>
            </tr>
		</tbody>
	</table>

	<h3>{'Items'|gettext}</h3>
	<ul>
        {if $order->shippingmethods|@count > 1}
            {foreach from=$order->shippingmethods item=method}
                <li>
                    <h2>{$method->option_title} - {$method->shipping_cost|currency}</h2>
                    <h6>{$method->firstname} {$method->lastname} {$method->address1} {$method->address2} {$method->city}, {$method->state|statename:abbv} {$method->zip}</h6>
                    <ul>
                        {foreach from=$method->orderitem item=oi}
                            <li>
                                <a href="{link action=show controller="storeController" title=$oi->products_name}">{$oi->products_name}</a> - {$oi->quantity} {'at'|gettext} {$oi->products_price|currency} {'each'|gettext}
                                {$oi->getExtraData()}
                            </li>
                        {/foreach}
                    </ul>
                </li>
            {/foreach}
        {else}
            {foreach from=$order->orderitem item=oi}
                <li>
                    <a href="{link action=show controller="storeController" title=$oi->products_name}">{$oi->products_name}</a> - {$oi->quantity} {'at'|gettext} {$oi->products_price|currency} {'each'|gettext}
                    {$oi->getExtraData()}
                </li>
            {/foreach}
        {/if}
	</ul>
</div>
