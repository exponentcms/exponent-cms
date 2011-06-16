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
 
 {css unique="cart" link="`$asset_path`css/cart.css" corecss="panels"}

 {/css}
 
 {script unique="cartview" yui2mods="dom"}
 {literal}
 YAHOO.util.Event.onDOMReady(function(){
     YAHOO.util.Dom.removeClass("myCart", 'hide');
     var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
     YAHOO.util.Dom.setStyle(loading, 'display', 'none');
 });
 {/literal}
 {/script}
<div class="loadingdiv">Loading Cart</div>

<div id="myCart" class="module cart show hide">
	<h1>{ecomconfig var='cart_title_text' default="Your Secure Shopping Cart"}</h1>
    <div id="cart-message">{ecomconfig var='cart_description_text' default=""}</div>
    <div>
        <div style="float:left; padding-bottom: 8px;">
            <a class="exp-ecom-link" href="{link controller=store action=showall}"><strong><em>Continue Shopping</em></strong></a>
            {if $items|@count gt 0}
                <a class="exp-ecom-link" style="margin-left: 18px;" href="{securelink controller=cart action=checkout}"><strong><em>Checkout Now</em></strong></a>
            {/if}
        </div> 
        <div style="float:right; padding-bottom: 8px; font-size: 9px;"><a href="{link action=empty_cart}"  onclick="return confirm('Are you sure you want to empty all items from your shopping cart?');">[Empty Cart]</a></div>      
        <div style="clear: both;"></div>
    </div>
	<div id="cartbox">        
		<div id="cart-top" width="100%" cellpadding="0" cellspacing="0">
			<div class="cart-total-label">
                {if $order->total_discounts > 0} 
			        <span class="total-label">Cart Items Total With Discounts:</span>
                {else}
                    <span class="total-label">Cart Items Total:</span>
                {/if}
				<span id="cart-total" class="carttotal">{currency_symbol}{$order->total|number_format:2}</span>
			</div>
			{if $coupons}
				{img src="`$smarty.const.ICON_RELATIVE`/ecom/cart-coupon-btn.png"}
			{/if}
		    <a class="checkout-now-btn" href="{link action=checkout}" title="Proceed to checkout">
		        <img src="{$smarty.const.URL_FULL}themes/common/skin/ecom/cart-checkout-btn.png" alt="checkout now">
			</a>            					
		</div>
        
		{include file="show_cart_only.tpl"}
        
        {if $items|@count gt 0}
            <table width="100%" id="cart-totals" border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th colspan=3 align="left">
                            {gettext str="Totals"}
                        </th>
                   </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="cart-totals-title">
                        {gettext str="Subtotal"}:
                        </td>
                        <td>
                        {currency_symbol}
                        </td>
                        <td style="text-align:right;">{$order->subtotal|number_format:2}
                        </td>
                    </tr>
                     {if isset($discounts[0])}                        
                        {if $discounts[0]->isCartDiscount()} 
                             <tr>
                                <td class="cart-totals-title">
                                <a style="font-weight: none;" href="{link action=removeDiscountFromCart id=$discounts[0]->id}"  alt="Remove discount from cart.">[remove coupon code]</a>&nbsp;(<span style="background-color:#33CC00;">{$discounts[0]->coupon_code}</span>)&nbsp;{gettext str="Total Discounts"}:
                                </td>
                                <td>
                                {currency_symbol}
                                </td>
                                <td style="text-align:right;">-{$order->total_discounts|number_format:2}
                                </td>
                            </tr>
                            <tr >
                                <td class="cart-totals-title">
                                {gettext str="Cart Total"}:
                                </td>
                                <td>
                                {currency_symbol}
                                </td>
                                <td style="text-align:right;">{$order->total|number_format:2}
                                </td>
                            </tr>   
                        {/if}
                      {/if}     
                      <tr>
                        <td width="90%" class="cart-totals-title">
                        {gettext str="Tax - "}
                        {foreach from=$order->taxzones item=zone}
                            {$zone->name} ({$zone->rate}%):
                        {foreachelse}
                            (N/A):
                        {/foreach}
                        </td>
                        <td>
                        {currency_symbol}
                        </td>
                        <td style="text-align:right;">{$order->tax|number_format:2}
                        </td>
                    </tr>   
                    <tr >
                        <td class="cart-totals-title">
                        {if isset($discounts[0])}                        
                            {if $discounts[0]->isShippingDiscount()}
                                <a style="font-weight: none;" href="{link action=removeDiscountFromCart id=$discounts[0]->id}"  alt="Remove discount from cart.">[remove coupon code]</a>&nbsp;(<span style="background-color:#33CC00;">{$discounts[0]->coupon_code}</span>)&nbsp; 
                            {/if}
                        {/if}
                        {* else *}
                        {gettext str="Estimated Shipping & Handling"}:
                        {* /if *}
                        </td>
                        <td>
                        {currency_symbol}
                        </td>
                        <td style="text-align:right;">                    
                           {$order->shipping_total|number_format:2}                    
                        </td>
                    </tr>
                    {if $order->surcharge_total != 0}
                        <tr>
                            <td class="cart-totals-title">
                            {gettext str="Freight Surcharge"}
                            </td>
                            <td>
                            {currency_symbol}
                            </td>
                            <td style="text-align:right;">{$order->surcharge_total|number_format:2}
                            </td>
                        </tr>
                    {/if}
                    <tr>
                        <td class="cart-totals-title">
                        {gettext str="Order Total"}:
                        </td>
                        <td>
                        {currency_symbol}
                        </td>
                        <td style="text-align:right;">{$order->grand_total|number_format:2}
                        </td>
                    </tr>
                    <tr>
                        <td class="cart-totals-title" style="text-align:right;" colspan="3">
                            {if !$discounts}      
                        <div class="input-code" style="text-align: right;">   
                            {form action="addDiscountToCart"}
                                <div style="vertical-align: middle; padding-top:5px; float: left;">{gettext str="Enter a Discount Code"}:</div>
                                <div style="float:left;">{control type="text" name="coupon_code" label=" "}</div>
                                <div style="float:left; right:0px">{control type="buttongroup" submit="Apply Code"}</div>
                            {/form}
                            <div style="clear:both;"></div>
                        </div>                
                    {/if}
                    <div style="clear:both"></div>
                        </td>
                    </tr>
                </tbody>
            </table>       
        {/if}
	</div>
    <div style="padding-top: 8px;">
        <a class="awesome large blue" href="{backlink}">Continue Shopping</a>
        {if $items|@count gt 0}
        <a class="awesome large blue" style="margin-left: 18px;" href="{securelink controller=cart action=checkout}">Checkout Now</a>
        {/if}
    </div>
</div>
