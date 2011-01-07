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

<div class="module order show">
    <h2 class="message">Your order was was successful. Thank you for your business.</h2>
	<table width=100% border="0" cellspacing="5" cellpadding="5">
		<thead>
		<tr>
			<th colspan=2>
		        <h1>Invoice # {$order->invoice_id}</h1>
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>
				<h2>Shipping Address</h2>
				{if $order->shipping_required == true}
				    {if $shipping->splitshipping == true}
				        Shipping split between {$shipping->splitmethods|@count} addresses.
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
				    No shipping required for this order.
				{/if}
			</td>
			<td>
				<h2>Billing Address</h2>
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
				<h2>Shipping Method</h2>
				{if $order->shipping_required == true}
				    {if $shipping->splitshipping == true}
				        Shipped via:
				        <ul>
				        {foreach from=$shipping->splitmethods item=method}
				           <li>{$method->option_title}</li>
				        {/foreach}
				        </ul>
				    {else}
    				    {$shipping->calculator->title} ({$shipping->shippingmethod->option_title}) - {currency_symbol}{$shipping->shippingmethod->shipping_cost|number_format:2}
    				{/if}
				{else}
				    No shipping required for this order.
				{/if}				
				<br><br><br><br>
			</td>
			<td>
				<h2>Payment Method</h2>
				{$billinginfo}
			</td>
		</tr>
		</tbody>
	</table>

	<h3>Items</h3>
	<ul>
	{foreach from=$order->orderitem item=oi}
		<li>
		    <a href="{link action=showByTitle controller=store title=$oi->products_name}">{$oi->products_name}</a> - {$oi->quantity} at ${$oi->products_price|number_format:2} each
		    {$oi->getExtraData()}
		</li>
	{/foreach}
	</ul>
</div>
