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

<div class="module order show">
    {$breadcrumb = [
        0 => [
            "title" => "{'Summary'|gettext}",
            "link"  => makeLink(['controller'=>'cart','action'=>'cart'])
        ],
        1 => [
            "title" => "{'Sign In'|gettext}",
            "link"  => makeLink(['controller'=>'cart','action'=>'cart'])
        ],
        2 => [
            "title" => "{'Shipping/Billing'|gettext}",
            "link"  => makeLink(['controller'=>'cart','action'=>'cart'])
        ],
        3 => [
            "title" => "{'Confirmation'|gettext}",
            "link"  => makeLink(['controller'=>'cart','action'=>'cart'])
        ],
        4 => [
            "title" => "{'Complete'|gettext}",
            "link"  => makeLink(['controller'=>'cart','action'=>'cart'])
        ]
    ]}
    {breadcrumb items=$breadcrumb active=4}
    <h2 class="message">{'Your order was was successful. Thank you for your business.'|gettext}</h2>
	<table width=100% border="0" cellspacing="5" cellpadding="5">
		<thead>
            <tr>
                <th colspan=2>
                    <h1>{'Invoice #'|gettext} {$order->invoice_id}</h1>
                </th>
            </tr>
		</thead>
		<tbody>
            <tr>
                <td>
                    <h2>{'Shipping Address'|gettext}</h2>
                    {if $order->shipping_required == true}
                        {if $shipping->splitshipping == true}
                            {'Shipping split between'|gettext} {$shipping->splitmethods|@count} {'addresses'|gettext}.
                        {else}
                            <address>
                            {$shipping->shippingmethod->firstname} {$shipping->shippingmethod->lastname}<br>
                            {if $shipping->shippingmethod->company != ""}{$shipping->shippingmethod->company}<br>{/if}
                            {if $shipping->shippingmethod->address1 != ""}{$shipping->shippingmethod->address1}<br>{/if}
                            {if $shipping->shippingmethod->address2 != ""}{$shipping->shippingmethod->address2}<br>{/if}
                            {$shipping->shippingmethod->city}{if $shipping->shippingmethod->state != ""}, {$shipping->shippingmethod->state|statename:abbv}{/if} {$shipping->shippingmethod->zip}<br>
                            {$shipping->shippingmethod->country}
                            </address>
                            <br>
                            {if $shipping->shippingmethod->phone_number != ""}{$shipping->shippingmethod->phone_number}<br>{/if}
                            {if $shipping->shippingmethod->email != ""}{$shipping->shippingmethod->email}<br>{/if}
                            <br>
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
                    {$order->billingmethod[0]->city}{if $order->billingmethod[0]->state != ""}, {$order->billingmethod[0]->state|statename:abbv}{/if} {$order->billingmethod[0]->zip}<br>
                    {$order->billingmethod[0]->country}
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
                        {if $shipping->splitshipping == true}
                            {'Shipped via:'|gettext}
                            <ul>
                            {foreach from=$shipping->splitmethods item=method}
                               <li>{$method->option_title}</li>
                            {/foreach}
                            </ul>
                        {else}
                            {$shipping->calculator->title} ({$shipping->shippingmethod->option_title}) - {$shipping->shippingmethod->shipping_cost|currency}
                        {/if}
                    {else}
                        {'No shipping required for this order.'|gettext}
                    {/if}
                    <br><br><br><br>
                </td>
                <td>
                    <h2>{'Payment Method'|gettext}</h2>
                    {$billinginfo}
                </td>
            </tr>
		</tbody>
	</table>

	<h3>{'Items'|gettext}</h3>
	<ul>
        {foreach from=$order->orderitem item=oi}
            <li>
                <a href="{link action=show controller=store title=$oi->products_name}">{$oi->products_name}</a> - {$oi->quantity} at {$oi->products_price|currency} each
                {$oi->getExtraData()}
            </li>
        {/foreach}
	</ul>
</div>
