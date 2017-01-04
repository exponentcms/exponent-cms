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

{css unique="cart" link="`$asset_path`css/cart.css" corecss="tables,panels,button"}

{/css}

<div id="myCart" class="module cart show hide">
    {if $items|@count gt 0}
        {$breadcrumb = [
            0 => [
                "title" => "{'Summary'|gettext}",
                "link"  => ""
            ],
            1 => [
                "title" => "{'Sign In'|gettext}",
                "link"  => ""
            ],
            2 => [
                "title" => "{'Shipping/Billing'|gettext}",
                "link"  => ""
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
        {breadcrumb items=$breadcrumb active=0 style=flat}
    {/if}
    <h1>{ecomconfig var='cart_title_text' default="Your Secure Shopping Cart"|gettext}</h1>
    <div id="cart-message">{ecomconfig var='cart_description_text' default=""}</div>
    <div class="module-actions" style="padding:8px; 0">
        {*<a class="{button_style}" href="{backlink}">{"Continue Shopping"|gettext}</a>*}
        {$backlink = makeLink(expHistory::getBack(1))}
        {icon class="reply" button=true size=large link=$backlink text="Continue Shopping"|gettext}
        {if $items|@count gt 0}
            {*<a class="{button_style}" style="margin-left: 18px;" href="{securelink controller=cart action=checkout}">{"Checkout Now"|gettext}</a>*}
            {icon class="shopping-cart" button=true size=large color=green controller=cart action=checkout secure=true text="Checkout Now"|gettext}
            {*<a class="{button_style color=red size=small}" style="float:right; margin-left: 18px;" href="{link action=empty_cart}" onclick="return confirm('Are you sure you want to empty all items from your shopping cart?');">{'Empty Cart'|gettext}</a>*}
            <span style="float:right; margin-left: 18px;">{icon class=delete button=true size=large action=empty_cart onclick="return confirm('Are you sure you want to empty all items from your shopping cart?');" text='Empty Cart'|gettext}</span>
        {/if}
    </div>
	<div id="cartbox">
		<div id="cart-top" width="100%" cellpadding="0" cellspacing="0">
			<div class="cart-total-label">
                {if $order->total_discounts > 0}
			        <span class="total-label">{"Cart Items Total With Discounts"|gettext}:</span>
                {else}
                    <span class="total-label">{"Cart Items Total"|gettext}:</span>
                {/if}
                <span id="cart-total" class="carttotal">{$order->total|currency}</span>
			</div>
		</div>

		{exp_include file="cart_only.tpl"}

        {if $items|@count gt 0}
            <table width="100%" id="cart-totals" border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                <thead>
                    <tr>
                        <th colspan=3 align="left">
                            {"Totals"|gettext}
                        </th>
                   </tr>
                </thead>
                <tbody>
                    <tr class="{cycle values="odd, even"}">
                        <td class="cart-totals-title">
                            {"Subtotal"|gettext}:
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
                                    <a style="font-weight: normal;" href="{link action=removeDiscountFromCart id=$discounts[0]->id}"  alt="{'Remove discount from cart'|gettext}">[remove coupon code]</a>&#160;(<span style="background-color:#33CC00;">{$discounts[0]->coupon_code}</span>)&#160;{"Total Discounts"|gettext}:
                                </td>
                                <td>
                                    {currency_symbol}
                                </td>
                                <td style="text-align:right;">-{$order->total_discounts|number_format:2}
                                </td>
                            </tr>
                            <tr class="{cycle values="odd, even"}">
                                <td class="cart-totals-title">
                                    {"Cart Total"|gettext}:
                                </td>
                                <td>
                                    {currency_symbol}
                                </td>
                                <td style="text-align:right;">{$order->total|number_format:2}
                                </td>
                            </tr>
                        {/if}
                      {/if}
                      {if !$order->shipping_taxed}
                      <tr class="{cycle values="odd, even"}">
                        <td width="90%" class="cart-totals-title">
                            {"Tax"|gettext} -
                            {foreach from=$order->taxzones item=zone}
                                {$zone->name} ({$zone->rate}%):
                            {foreachelse}
                                ({'N/A'|gettext}):
                            {/foreach}
                        </td>
                        <td>
                            {currency_symbol}
                        </td>
                        <td style="text-align:right;">{$order->tax|number_format:2}
                        </td>
                    </tr>
                    {/if}
                    <tr class="{cycle values="odd, even"}">
                        <td class="cart-totals-title">
                            {if isset($discounts[0])}
                                {if $discounts[0]->isShippingDiscount()}
                                    <a style="font-weight: normal;" href="{link action=removeDiscountFromCart id=$discounts[0]->id}"  alt="{'Remove discount from cart'|gettext}">[{'remove coupon code'|gettext}]</a>&#160;(<span style="background-color:#33CC00;">{$discounts[0]->coupon_code}</span>)&#160;
                                {/if}
                            {/if}
                            {* else *}
                            {"Estimated Shipping & Handling"|gettext}:
                            {* /if *}
                        </td>
                        <td>
                            {currency_symbol}
                        </td>
                        {if is_string($order->shipping_total)}
                            <td style="text-align:center;">
                                {$order->shipping_total}
                        {else}
                            <td style="text-align:right;">
                                {$order->shipping_total|number_format:2}
                        {/if}
                        </td>
                    </tr>
                    {if $order->surcharge_total != 0}
                        <tr class="{cycle values="odd, even"}">
                            <td class="cart-totals-title">
                                {"Freight Surcharge"|gettext}
                            </td>
                            <td>
                                {currency_symbol}
                            </td>
                            <td style="text-align:right;">{$order->surcharge_total|number_format:2}
                            </td>
                        </tr>
                    {/if}
                    {if $order->shipping_taxed}
                    <tr class="{cycle values="odd, even"}">
                      <td width="90%" class="cart-totals-title">
                          {"Tax"|gettext} -
                          {foreach from=$order->taxzones item=zone}
                              {$zone->name} ({$zone->rate}%):
                          {foreachelse}
                              ({'N/A'|gettext}):
                          {/foreach}
                      </td>
                      <td>
                          {currency_symbol}
                      </td>
                      <td style="text-align:right;">{$order->tax|number_format:2}
                      </td>
                    </tr>
                    {/if}
                    <tr class="{cycle values="odd, even"}">
                        <td class="cart-totals-title">
                            {"Order Total"|gettext}:
                        </td>
                        <td>
                            {currency_symbol}
                        </td>
                        <td style="text-align:right;">{$order->grand_total|number_format:2}
                        </td>
                    </tr>
                    {if !$noactivediscounts}
                        {if !$discounts}
                            <tr class="{cycle values="odd, even"}">
                                <td colspan="3">
                                    <div class="input-code">
                                        {form action="addDiscountToCart"}
                                            {control type="text" name="coupon_code" label="Enter a Discount Code"|gettext}
                                            {control type="buttongroup" submit="Apply Code"|gettext}
                                        {/form}
                                    </div>
                                    {clear}
                                </td>
                            </tr>
                        {/if}
                   {/if}
                </tbody>
            </table>
        {/if}
	</div>
    <div class="module-actions" style="padding:8px; 0">
        {*<a class="{button_style}" href="{backlink}">{"Continue Shopping"|gettext}</a>*}
        {icon class="reply" button=true size=large link=$backlink text="Continue Shopping"|gettext}
        {if $items|@count gt 0}
            {*<a class="{button_style}" style="margin-left: 18px;" href="{securelink controller=cart action=checkout}">{"Checkout Now"|gettext}</a>*}
            {icon class="shopping-cart" button=true size=large color=green controller=cart action=checkout secure=true text="Checkout Now"|gettext}
        {/if}
    </div>
</div>
{*<div class="loadingdiv">{"Loading Cart"|gettext}</div>*}
{loading title="Loading Cart"|gettext}

{script unique="editform" yui3mods="node"}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
		Y.one('#myCart').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
