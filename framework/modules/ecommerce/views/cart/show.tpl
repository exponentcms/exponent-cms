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
 
{css unique="cart" link="`$asset_path`css/cart.css" corecss="tables,panels,button"}

{/css}

<div id="myCart" class="module cart show hide">
	<h1>{ecomconfig var='cart_title_text' default="Your Secure Shopping Cart"}</h1>
    <div id="cart-message">{ecomconfig var='cart_description_text' default=""}</div>
    <div style="padding:8px; 0">
        <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{backlink}">Continue Shopping</a>
        {if $items|@count gt 0}
        <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" style="margin-left: 18px;" href="{securelink controller=cart action=checkout}">Checkout Now</a>
        {/if}
        <a class="awesome small red" style="float:right; margin-left: 18px;" href="{link action=empty_cart}"  onclick="return confirm('Are you sure you want to empty all items from your shopping cart?');">Empty Cart</a>
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
		</div>
        
		{include file="show_cart_only.tpl"}
        
        {if $items|@count gt 0}
            <table width="100%" id="cart-totals" border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                <thead>
                    <tr>
                        <th colspan=3 align="left">
                            {gettext str="Totals"}
                        </th>
                   </tr>
                </thead>
                <tbody>
                    <tr class="{cycle values="odd, even"}">
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
                             <tr class="{cycle values="odd, even"}">
                                <td class="cart-totals-title">
                                <a style="font-weight: none;" href="{link action=removeDiscountFromCart id=$discounts[0]->id}"  alt="Remove discount from cart.">[remove coupon code]</a>&nbsp;(<span style="background-color:#33CC00;">{$discounts[0]->coupon_code}</span>)&nbsp;{gettext str="Total Discounts"}:
                                </td>
                                <td>
                                {currency_symbol}
                                </td>
                                <td style="text-align:right;">-{$order->total_discounts|number_format:2}
                                </td>
                            </tr>
                            <tr class="{cycle values="odd, even"}">
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
                      <tr class="{cycle values="odd, even"}">
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
                    <tr class="{cycle values="odd, even"}">
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
                        <tr class="{cycle values="odd, even"}">
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
                    <tr class="{cycle values="odd, even"}">
                        <td class="cart-totals-title">
                        {gettext str="Order Total"}:
                        </td>
                        <td>
                        {currency_symbol}
                        </td>
                        <td style="text-align:right;">{$order->grand_total|number_format:2}
                        </td>
                    </tr>
                    {if !isset($noactivediscounts)}                                                
                        <tr class="{cycle values="odd, even"}">
                            <td colspan="3">
                            <div class="input-code">
                                {form action="addDiscountToCart"}
                                    {control type="text" name="coupon_code" label="Enter a Discount Code"}
                                    {control type="buttongroup" submit="Apply Code"}
                                {/form}
                            </div>                
                            <div style="clear:both"></div>
                            </td>
                        </tr>
                   {/if}
                </tbody>
            </table>       
        {/if}
	</div>
    <div style="padding:8px; 0">
        <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{backlink}">Continue Shopping</a>
        {if $items|@count gt 0}
        <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" style="margin-left: 18px;" href="{securelink controller=cart action=checkout}">Checkout Now</a>
        {/if}
    </div>
</div>

<div class="loadingdiv">{"Loading Cart"|gettext}</div>

{script unique="editform" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
        var YAHOO=Y.YUI2;

        var tabView = new YAHOO.widget.TabView('helpedit');
        Y.one('#myCart').removeClass('hide').next().remove();
    });
{/literal}
{/script}
