{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{css unique="cart" link="`$asset_path`css/cart.css" corecss="button,panels"}

{/css}
{uniqueid assign="id"}
{messagequeue}

<div id="expresscheckout" class="cart checkout">
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
    {breadcrumb items=$breadcrumb active=2 style=flat}
    <h1>{ecomconfig var='checkout_title_top' default="Confirm Your Secure Order"|gettext}</h1>
    <div id="cart-message">{ecomconfig var='checkout_message_top' default=""}</div>
    {if ecomconfig::getConfig('policy')!=""}
        <div>
            {pop id="review_policy" text="Review Store Policies"|gettext title="Store Policies"|gettext buttons="Close"|gettext}
                {ecomconfig var='policy' default=""}
            {/pop}
            {*<a href="#" id="review-policy">{"Review Store Policies"|gettext}</a>*}
            {*<div id="storepolicies" style="z-index:9999">*}
                {*<div class="yui3-widget-hd">*}
                    {*{"Store Policies"|gettext}*}
                {*</div>*}
                {*<div class="yui3-widget-bd" style="overflow-y:scroll">*}
                    {*{ecomconfig var='policy' default=""}*}
                {*</div>*}
            {*</div>*}
            {*script unique="policypop" jquery="bootstrap-dialog" bootstrap="modal,transition,tab"}
            {literal}
                $(document).ready(function(){
                    $('#review-policy').click(function() {
                        BootstrapDialog.show({
                            size: BootstrapDialog.SIZE_WIDE,
                            title: '{/literal}{'Store Policies'|gettext}{literal}',
                            message: {/literal}{ecomconfig var='policy' default="" json=true}{literal},
                            buttons: [{
                                label: 'Close',
                                action: function(dialogRef){
                                    dialogRef.close();
                                }
                            }]
                        });
                    });
                });
            {/literal}
            {/script*}
        </div>
    {/if}

    {* if $order->forced_shipping == true}
    <ul id="forcedshipping" class="queue error">
       <li>{$order->product_forcing_shipping->title} requires you to ship this order via {$shipping->shippingmethod->option_title}</li>
    </ul>
    {/if *}

    <div class="totals">
        <div class="details">
            {"Subtotal"|gettext}{if $discounts} {"with discounts"|gettext}{/if}: <span class="carttotal">{$order->total|currency}</span>
        </div>
    </div>

    <h2>{"Your Items"|gettext}</h2>
    <div class="cartitems separate">
        <!-- p>You have <strong>{$order->item_count}</strong> item{if $order->item_count > 1}s{/if} in your cart. <a id="expandcart" href="#" class="exp-ecom-link">[Click here to show your cart]<span></span></a></p -->
        <div id="shoppingcartwrapper">
            {*{chain controller=cart action=cart_only}*}
            {showmodule controller=cart action=cart_only}
        </div>
    </div>
    {clear}
    {if !$noactivediscounts}
        <div class="separate">
            <h2>{"Optional Promotional Code"|gettext}</h2>

            <div class="apply-codes">
                {if !$discounts}
                    <div class="input-code">
                        {form action="addDiscountToCart"}
                            {control type="text" name="coupon_code" label=" "}
                            {control type="buttongroup" submit="Apply Code"|gettext}
                        {/form}
                    </div>
                {else}
                    <div class="codes-applied">
                        {'You\'ve applied the following'|gettext} {if $discounts|@count==1}{"coupon"|gettext}{else}{$discounts|@count} {"coupons"|gettext}{/if}
                        :
                        <ul>
                            {foreach from=$discounts item=discount}
                                <li>
                                    <strong>{$discount->coupon_code}</strong>
                                    &#160;&#160;{icon class=delete action=removeDiscountFromCart record=$discount alt="Remove discount from cart."|gettext}
                                    {br}
                                    <em>{$discount->title}</em>
                                </li>
                                {if $discount->isShippingDiscount()}{$is_shipping_discount=true}{/if}
                            {/foreach}
                        </ul>
                        {if $discounts|@count==1}{'This coupon is'|gettext} {else}{'These coupons are'|gettext} {/if} {'saving you'|gettext}
                        {if $discounts[0]->isCartDiscount()}
                            {$order->total_discounts|currency}.
                        {else}
                            {$order->shippingDiscount|currency}.
                        {/if}
                    </div>
                {/if}
                {clear}
            </div>
        </div>
    {/if}

    <div id="billingadshippinginfo">
        {if $order->shipping_required == true}
            <div class="shippingdetails separate">
                {if $shipping->selectable_calculators|@count > 1}
                {/if}

                {if $shipping->shippingmethod->option!=""}
                    {foreach from=$shipping->pricelist item=option}
                        {if $option.id == $shipping->shippingmethod->option}{$shpMthdOp=$option}{/if}
                    {/foreach}
                {else}
                    {foreach name="gtfoi" from=$shipping->pricelist item=option}
                        {if $smarty.foreach.gtfoi.first}{$shpMthdOp=$option}{/if}
                    {/foreach}
                {/if}
                <div class="shipping-info">
                    <h2>{"Your Shipping Information"|gettext}</h2>
                    {if $order->forced_shipping == true || $is_shipping_discount == true}
                        <ul id="forcedshipping" class="queue message">
                            {if $order->forced_shipping == true}
                                <li>{$order->forcing_shipping_reason} {"requires you to ship this order via"|gettext} {shippingcalculator::getCalcTitle($shipping->shippingmethod->shippingcalculator_id)} {'shipping'|gettext}{if !empty($shipping->shippingmethod->option)} {'using'|gettext} {$shipping->shippingmethod->option_title}{/if}</li>
                            {/if}
                            {if $is_shipping_discount}
                                <li>{"Your full shipping discount will be reflected on the following order confirmation page, prior to submitting your order."|gettext}</li>
                            {/if}
                        </ul>
                    {/if}

                    {*if $order->orderitem|@count>1 && $shipping->splitshipping == false && $order->forced_shipping == false}
                        <a id="multiaddresslink" class="exp-ecom-link {if hideMultiShip == 1}hide{/if}" href="{link action=splitShipping}">Ship to multiple addresses</a>
                    {/if*}

                    {if $shipping->splitshipping == false}
                        {clear}
                        <h3>{"Shipping Address"|gettext}</h3>
                        <!--p>Would you like to <a class="ordermessage" href="#" rel="{$shipping->shippingmethod->id}">add a gift message</a> to this Order?</p-->

                        <div class="shipping-address">
                            <div id="shpAddSwp">
                                {if $shipping->address->id == ''}
                                    {"No address yet"|gettext}
                                {else}
                                    {$shipping->address|address}
                                {/if}
                            </div>

                            <div class="bracket{if !$shipping->address->id} hide{/if}">
                                {*<a class="{button_style}" href="{link controller=address action=myaddressbook}"><strong><em>{"Change or Add Address"|gettext}</em></strong></a>*}
                                {*{icon class=adjust button=true controller=address action=myaddressbook text="Change or Add Addresses"|gettext}*}
                                {capture assign="link"}{link controller=address action=myaddressbook}{/capture}
                                {pop id="edit_shipping_address" text="Change or Add Addresses"|gettext title="Edit Addresses"|gettext icon=adjust buttons="Close"|gettext ajax=$link}
                                    load
                                {/pop}
                            </div>
                        </div>
                        {clear}
                    {else}
                        {* else, we have split shipping *}
                        <a id="multiaddresslink" class="ecomlink-link" href="{link action=splitShipping}">{"Edit Shipping Information"|gettext}</a>
                        {foreach from=$shipping->splitmethods item=method}
                            <div class="splitaddress">
                                <h4>{$order->countOrderitemsByShippingmethod($method->id)} {'items will be shipped to:'|gettext}</h4>
                                <!--a class="ordermessage {button_style}" href="#" rel="{$method->id}"><strong><em>Add a Gift Message to this Order</em></strong></a-->
                                <address>
                                    {$method->firstname} {$method->middlename} {$method->lastname}{br}
                                    {$method->address1}{br}
                                    {if $method->address2 != ""}{$method->address2}{br}{/if}
                                    {$method->city},
                                    {if $method->state == -2}
                                        {$method->non_us_state}
                                    {else}
                                        {$method->state|statename:abv}
                                    {/if}
                                    , {$method->zip}
                                    {if $method->state == -2}
                                        {br}{$method->country|countryname}
                                    {/if}
                                </address>
                            </div>
                        {/foreach}
                    {/if}
                    {* end split shipping *}

                    {if $shipping->selectable_calculators|@count > 1}{$multicalc=1}{/if}
                    {if !$shipping->address->id}{$noShippingPrices=1}{/if}

                    {if $multicalc}
                        <h3>{"Shipping Methods"|gettext}</h3>

                        <div class="separate">
                            {if $order->forced_shipping == true}
                                <span class="servopt">{$shipping->calculator->title}</span>
                            {else}
                                {foreach key=key from=$shipping->selectable_calculators item=calc}
                                    {if $shipping->calculator->id!=$key}
                                        <a rel="{$key}"
                                           href="{link shippingcalculator_id=$key controller=shipping action=selectShippingCalculator}"
                                           title="{'Select this shipping method'|gettext}"
                                           class="servopt">
                                            {$calc}
                                        </a>
                                        {else}
                                        <span class="servopt">{$calc}</span>
                                    {/if}
                                {/foreach}
                            {/if}
                        </div>

                        {if $order->forced_shipping == true && !empty($shipping->shippingmethod->option)}
                            <p>{"Your order requires"|gettext} <strong>{$shipping->shippingmethod->option_title} {'shipping'|gettext}</strong></p>
                        {else}
                        {*
                        <p{if $noShippingPrices} class="hide"{/if}><strong id="cur-calc">{if $shipping->calculator->id}{$shipping->calculator->title}{else}{'No service selected'|gettext}{/if}</strong>  -  <a href="#" id="servicepicker">{'Select a Service'|gettext}</a></p>
                        <div id="calculators" class="exp-dropmenu">
                            <div class="hd"><span class="type-icon"></span>{'Select a Shipping Service'|gettext}</div>
                            <div class="bd">
                                <div>
                                    <ul>
                                    {foreach key=key from=$shipping->selectable_calculators item=calc}
                                        <li><a rel="{$key}" href="#" class="servopt">{$calc}</a></li>
                                    {/foreach}
                                    </ul>
                                    {form name=SelShpCal controller=shipping action=selectShippingCalculator}
                                        {control type=hidden name=shippingcalculator_id id=shipcalc_id value=$shipping->calculator->id}
                                    {/form}
                                </div>
                            </div>
                        </div>
                        *}
                        {/if}
                    {/if}
                    {if $shipping->splitshipping == false}

                        {clear}

                        <div id="shipping-services">
                            <h3>{"Selected Shipping Option"|gettext}</h3>
                            {include file="`$smarty.const.BASE`framework/modules/ecommerce/views/shipping/renderOptions.bootstrap3.tpl"}
                        </div>

                        {*<h3>{"Shipping Address"|gettext}</h3>*}
                        {*<!--p>Would you like to <a class="ordermessage" href="#" rel="{$shipping->shippingmethod->id}">add a gift message</a> to this Order?</p-->*}

                        {*<div class="shipping-address">*}
                            {*<div id="shpAddSwp">*}
                                {*{if $shipping->address->id == ''}*}
                                    {*{"No address yet"|gettext}*}
                                {*{else}*}
                                    {*{$shipping->address|address}*}
                                {*{/if}*}
                            {*</div>*}

                            {*<div class="bracket{if !$shipping->address->id} hide{/if}">*}
                                {*<a class="{button_style}" href="{link controller=address action=myaddressbook}"><strong><em>{"Change or Add Address"|gettext}</em></strong></a>*}
                                {*{icon class=adjust button=true controller=address action=myaddressbook text="Change or Add Address"|gettext}*}
                            {*</div>*}
                        {*</div>*}
                        {clear}
                    {else}

                        {* else, we have split shipping *}
                        {*<a id="multiaddresslink" class="ecomlink-link"*}
                           {*href="{link action=splitShipping}">{"Edit Shipping Information"|gettext}</a>*}

                        {*{foreach from=$shipping->splitmethods item=method}*}
                            {*<div class="splitaddress">*}
                                {*<h4>{$order->countOrderitemsByShippingmethod($method->id)} {'items will be shipped to:'|gettext}</h4>*}
                                {*<!--a class="ordermessage {button_style}" href="#" rel="{$method->id}"><strong><em>Add a Gift Message to this Order</em></strong></a-->*}
                                {*<address>*}
                                    {*{$method->firstname} {$method->middlename} {$method->lastname}{br}*}
                                    {*{$method->address1}{br}*}
                                    {*{if $method->address2 != ""}{$method->address2}{br}{/if}*}
                                    {*{$method->city},*}
                                    {*{if $method->state == -2}*}
                                        {*{$method->non_us_state}*}
                                    {*{else}*}
                                        {*{$method->state|statename:abv}*}
                                    {*{/if}*}
                                    {*, {$method->zip}*}
                                    {*{if $method->state == -2}*}
                                        {*{br}{$method->country|countryname}*}
                                    {*{/if}*}
                                {*</address>*}
                            {*</div>*}
                        {*{/foreach}*}

                    {/if}
                    {* end split shipping *}
                </div>
            {/if} {* end shipping required check *}
        </div>
        <div class="billingdetails separate">
            <h2>{"Your Billing Information"|gettext}</h2>
            <h3>{"Billing Address"|gettext}</h3>

            <div class="billing-address">
                <div id="bllAddSwp">
                    {if $billing->address->id == ''}
                        {"You have not selected an address yet."|gettext}
                    {else}
                        {$billing->address|address}
                    {/if}
                </div>
                <div class="bracket">
                    {*<a class="{button_style}" href="{link controller=address action=myaddressbook}"><strong><em>{"Change or Add Address"|gettext}</em></strong></a>*}
                    {*{icon class=adjust button=true controller=address action=myaddressbook text="Change or Add Addresses"|gettext}*}
                    {capture assign="link"}{link controller=address action=myaddressbook}{/capture}
                    {pop id="edit_billing_address" text="Change or Add Addresses"|gettext title="Edit Addresses"|gettext icon=adjust buttons="Close"|gettext ajax=$link}
                        load
                    {/pop}
                </div>
            </div>
            <div style="clear: both;"></div>
        {*</div>*}
        {*<div class="separate">*}
            {if $order->total}
                {*<h2>{"Payment Information"|gettext}</h2>*}
                {if $billing->calculator_views|@count > 1}
                    <h3>{"Payment Methods"|gettext}</h3>
                    <div id="cart-{$id}" class="">
                        <ul class="nav nav-tabs" role="tablist">
                            {foreach from=$billing->calculator_views item=cviews name=tabs}
                                <li role="presentation"{if $smarty.foreach.tabs.first} class="active"{/if}><a href="#tab{$smarty.foreach.tabs.iteration}" role="tab" data-toggle="tab">{$billing->selectable_calculators[$cviews.id]}</a></li>
                            {/foreach}
                        </ul>
                        <div class="tab-content">
                            {foreach from=$billing->calculator_views item=cviews name=calcs}
                                <div id="tab{$smarty.foreach.calcs.iteration}" role="tabpanel" class="tab-pane fade{if $smarty.foreach.calcs.first} in active{/if}">
                                    {include file=$cviews.view calcid=$cviews.id}
                                </div>
                            {/foreach}
                        </div>
                    </div>
                {else}
                    <h3>{"Payment Method"|gettext}</h3>
                    <div id="cart-{$id}" class="">
                        {foreach from=$billing->calculator_views item=cviews name=tabs}
                            <strong class="selected-info">{$billing->selectable_calculators[$cviews.id]}</strong>
                        {/foreach}
                        {foreach from=$billing->calculator_views key=key item=cviews name=calcs}
                            <div id="tab{$smarty.foreach.calcs.iteration}" role="tabpanel" class="tab-pane fade{if $smarty.foreach.calcs.first} in active{/if}">
                                {include file=$cviews.view calcid=$cviews.id}
                            </div>
                        {/foreach}
                    </div>
                {/if}
                {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
                {loading}
            {else}
                <div class="billing-method">
                    {form name="free" controller=cart action=preprocess}
                        {control type="hidden" name="billingcalculator_id" value=-1}
                        {control type="hidden" name="cash_amount" value=0}
                        <button id="continue-checkout" type="submit" class="shopping-cart {button_style size=large}">{"Continue Checkout"|gettext}</button>
                    {/form}
                </div>
            {/if}
        </div>
        <!--div class="separate">
                <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}-dis continue" href="#" id="checkoutnow"><strong><em>Complete your checkout information to continue</em></strong></a>
            </div-->
    </div>
</div>

{script unique="tabload" jquery=1}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}