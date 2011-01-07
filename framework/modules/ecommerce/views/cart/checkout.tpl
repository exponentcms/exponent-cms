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
 
<div id="expresscheckout" class="cart checkout exp-skin hide">
    <h1>{$moduletitle|default:"Express Checkout"}</h1>

    {if $cartConfig.policy!=""}
        <a href="#" id="review-policy">{gettext str="Review Store Policies"}</a>
        <div id="storepolicies" class="exp-form">
            <div class="hd">
                {gettext str="Store Policies"}
            </div>
            <div class="bd" style="overflow-y:scroll">
                {$cartConfig.policy}
            </div>
        </div>
        {script unique="policypop" yuimodules="container"}
        {literal}
            var policies = new YAHOO.widget.Panel("storepolicies", { 
                width:"400px",
                height:"350px",
                modal:true,
                visible:false,
                zindex:57,
                constraintoviewport:true,
                close:true,
                draggable:false
                });
            policies.render();
            
            YAHOO.util.Event.on('review-policy', 'click', function(e){
                YAHOO.util.Event.stopEvent(e);
                
                policies.show();
            }, policies, false);
            
        {/literal}
        {/script}
    {/if}
    
    {if $order->forced_shipping == true}
    <ul id="forcedshipping" class="queue error">
        <li>{$order->product_forcing_shipping->title} requires you to ship this order via {$shipping->shippingmethod->option_title}</li>
    </ul>
    {/if}

    <div class="totals">
        <div class="details">
            Total{if $discounts} with discounts{/if}: <span class="carttotal">{currency_symbol}{$order->total|number_format:2}</span>
        </div>
    </div>

    <div class="cartitems separate">
        
        <p>You've got <strong>{$order->item_count}</strong> item{if $order->item_count > 1}s{/if} in your cart. <a id="expandcart" href="#" class="ecom-link">Show them?<span></span></a></p>
        <div id="shoppingcartwrapper">
            {chain controller=cart action=show view=show_cart_only}
        </div>
    </div>
    <div style="clear:both"></div>
    {if !$noactivediscounts}
        <div class="separate">
            <h2>{gettext str="Optional Promotional Code"}</h2>
            <div class="apply-codes">
                {if !$discounts}      
                <div class="input-code">
                    {form action="addDiscountToCart"}
                        {control type="text" name="coupon_code" label=" "}
                        {control type="buttongroup" submit="Apply Code"}
                    {/form}
                </div>
                {else}
                    <div class="codes-applied">
                        You've applied the following {if $discounts|@count==1}coupon{else}{$discounts|@count} coupons{/if}:
                        <ul>
                            {foreach from=$discounts item=discount}
                            <li>
                                <strong>{$discount->coupon_code}</strong>
                                &nbsp;&nbsp;{icon img="delete.png" action=removeDiscountFromCart id=$discount->id alt="Remove discount from cart."}
                                {br}
                                <em>{$discount->title}</em>
                            </li>
                            {/foreach}
                        </ul>
                        {if $discounts|@count==1}This Coupon is {else}These Coupons are {/if}saving you {currency_symbol}{$order->totalBeforeDiscounts-$order->total|number_format:2}.
                    </div>
                {/if}
                <div style="clear:both"></div>
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
                    {if $option.id == $shipping->shippingmethod->option}{assign var=shpMthdOp value=$option}{/if}
                {/foreach}
            {else}
                {foreach name="gtfoi" from=$shipping->pricelist item=option}
                    {if $smarty.foreach.gtfoi.first}{assign var=shpMthdOp value=$option}{/if}
                {/foreach}
            {/if}
            <div class="shipping-info">
                <h2>Your Shipping Information</h2>
                {*if $order->orderitem|@count>1 && $shipping->splitshipping == false && $order->forced_shipping == false}
                    <a id="miltiaddresslink" class="ecom-link {if hideMultiShip == 1}hide{/if}" href="{link action=splitShipping}">Ship to multiple addresses</a>
                {/if*}
                
                {if $shipping->selectable_calculators|@count > 1}{assign var=multicalc value=1}{/if}
                {if !$shipping->address->id}{assign var=noShippingPrices value=1}{/if}
                
                <h3>Shipping Method{if multicalc}s{/if}</h3>
                {if $multicalc}
                
                
                {if $order->forced_shipping == true}
                    <p>Your order requires <strong>{$shipping->shippingmethod->option_title}</strong></p>
                {else}
                    <p{if $noShippingPrices} class="hide"{/if}><strong id="cur-calc">{if $shipping->calculator->id}{$shipping->calculator->title}{else}No service selected{/if}</strong>  -  <a href="#" id="servicepicker">Select a Service</a></p>

                    <div id="calculators" class="exp-dropmenu">
                        <div class="hd"><span class="type-icon"></span>Select a Shipping Service</div>
                        <div class="bd">
                            <div>
                                <ul>
                                {foreach key=key from=$shipping->selectable_calculators item=calc}
                                {*if $option.id == $shipping->shippingmethod->option}{assign var=selected value=true}{else}{assign var=selected value=false}{/if*}
                                    <li><a rel="{$key}" href="#" class="servopt">{$calc}</a></li>
                                {/foreach}
                                </ul>
                                {form name=SelShpCal controller=shipping action=selectShippingCalculator}
                                    {control type=hidden name=shippingcalculator_id id=shipcalc_id value=$shipping->calculator->id}
                                {/form}
                            </div>
                        </div>
                    </div>
                {/if}
                {/if}
                {if $shipping->splitshipping == false}

                <div style="clear:both"></div>
                
                <div id="shipping-services">
                    {include file="`$smarty.const.BASE`framework/modules/ecommerce/views/shipping/renderOptions.tpl"}
                </div>

                <h3>Shipping Address</h3>
                <p>Would you like to <a class="ordermessage" href="#" rel="{$shipping->shippingmethod->id}">add a gift message</a> to this Order?</p>
                
                <div class="shipping-address">
                    <div id="shpAddSwp">
                        {if $shipping->address->id == ''}
                            No address yet
                        {else}
                            {$shipping->address|address}
                        {/if}
                    </div>    
                    
                    <div class="bracket{if !$shipping->address->id} hide{/if}">
                        <a class="exp-ecom-link" href="{link controller=address action=myaddressbook}"><strong><em>Change Address</em></strong></a>
                    </div>
                    
                </div>
                <div style="clear:both"></div>
                {else}

                {* else, we have split shipping *}
                <a id="miltiaddresslink" class="ecomlink-link" href="{link action=splitShipping}">Edit Shipping Information</a>
            
                {foreach from=$shipping->splitmethods item=method}
                    <div class="splitaddress">
                        <h4>{$order->countOrderitemsByShippingmethod($method->id)} items will be shipped to:</h4>
                        <a class="ordermessage exp-ecom-link" href="#" rel="{$method->id}"><strong><em>Add a Gift Message to this Order</em></strong></a>
                        <address>
                            {$method->firstname} {$method->middlename} {$method->lastname}{br}
                            {$method->address1}{br}
                            {if $method->address2 != ""}{$method->address2}{br}{/if}
                            {$method->city}, {$method->state|statename:abv}, {$method->zip}
                        </address>
                    </div>
                {/foreach}
                
                {/if}
            </div>
            {* end split shipping *}
            {/if} {* end shipping required check *}
            <div id="ordermessageform"  class="exp-form">
                    <div class="hd">Add a Gift Message</div>
                    <div class="bd"><span class="type-icon"></span>
                        {form controller=shipping name=omform action="leaveMessage"}
                            {control type=hidden name=shippingmessageid id=shippingmessageid value="0"}
                            {control type=hidden name=nosave id=nosave value="0"}
                            {control type=text name=shpmessageto id=shpmessageto label="To"}
                            {control type=text name=shpmessagefrom id=shpmessagefrom label="From"}
                            {control type=textarea name=shpmessage id=shpmessage label="Message"}
                            {control type=buttongroup name="savemessage" id="savemessage" submit="Save Message"}
                        {/form}
                    </div>
                </div>
        </div>
        <div class="billingdetails separate">
            <h2>Your Billing Information</h2>
            <h3>Your billing address</h3>
            
            <div class="billing-address">
                <div id="bllAddSwp">
                    {if $billing->address->id == ''}
                        You have not selected an address yet.
                    {else}
                        {$billing->address|address}
                    {/if}
                </div>
                <div class="bracket">
                    <a class="exp-ecom-link" href="{link controller=address action=myaddressbook}"><strong><em>Change Address</em></strong></a>
                </div>
            </div>
        
            <h3>Payment Information</h3>
            {foreach from=$billing->calculator_views item=cviews name=calcs}                   
                {include file=$cviews}
                {if $smarty.foreach.calcs.last!=1}
                    <strong>- OR -</strong>
                {/if}
            {/foreach}
        </div>
        <!--div class="separate">
            <a class="exp-ecom-link-dis continue" href="#" id="checkoutnow"><strong><em>Complete your checkout information to continue</em></strong></a>
        </div-->
    </div>
</div>
<div id="loadingdiv" class="loadingdiv">Loading Checkout Page</div>

{*  Kludged out while testing paypal *}
{script unique="shoppingcartcheckout" yuimodules="animation,container,json" src=`$smarty.const.JS_FULL`exp-ecomcheckout.js}
//
{/script}