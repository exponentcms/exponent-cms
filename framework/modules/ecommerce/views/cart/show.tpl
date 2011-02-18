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
	<h1>{$moduletitle|default:"Your Shopping Cart"}</h1>

	<div id="cartbox">
		<div id="cart-top" width="100%" cellpadding="0" cellspacing="0">
			<div class="cart-total-label">
			    <span class="total-label">Cart Total:</span>
				<span id="cart-total" class="carttotal">{currency_symbol}{$order->total|number_format:2}</span>
			</div>
			{if $coupons}
				{img src="`$smarty.const.ICON_RELATIVE`/ecom/cart-coupon-btn.png"}
			{/if}
		</div>
		{include file="show_cart_only.tpl"}
	</div>
    <div style="padding-top: 8px;">
        <a class="awesome large blue" href="{backlink}">Continue Shopping</a>
        {if $items|@count gt 0}
        <a class="awesome large blue" style="margin-left: 18px;" href="{securelink controller=cart action=checkout}">Checkout Now</a>
        {/if}
    </div>
</div>
