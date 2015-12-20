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

{uniqueid assign="id"}

<div class="module cart quick-pay">
    {form action=processQuickPay}
        {if $order->billing_required == true}
            <div class="billingdetails ">
                <h1>{'Credit Card Information'|gettext}</h1>
                <div class="info">
                    <h2>{'Name as it appears on card'|gettext}</h2>
                    {control id=fname type=text name="billing[firstname]" label="First Name"|gettext}
                    {control id=midname type=text name="billing[middlename]" label="Middle"|gettext size=5}
                    {control id=lname type=text name="billing[lastname]" label="Last Name"|gettext}
                    {clear}
                </div>
                <div class="info">
                    <h2>{'Billing address of the card you are using'|gettext}</h2>
                    {control id=addy1 type=text name="billing[address1]" label="Street Address"|gettext}
                    {control id=addy2 type=text name="billing[address2]" label="Apt/Suite #"|gettext size=10}
                    {control id=city type=text name="billing[city]" label="City"|gettext size=15}
                    {control id=state type=state name="billing[state]" label="State"|gettext}
                    {control id=zip type=text name="billing[zip]" label="Zip Code"|gettext size=6}
                    {clear}
                </div>
                <div class="info" id="ccfs">
                    <h2>{'Credit Card'|gettext}</h2>
                    {if $billing->form != ""}
                        {$calcid=$billing->calculator->id}
                        {$billing->form.$calcid}
                    {/if}
                    {clear}
                </div>
                <div class="separate">
                   <h2>{"Payment Information"|gettext}</h2>
                   <h3>{"Available Payment Methods"|gettext}</h3>
                   <div id="{$id}" class="">
                       <ul class="nav nav-tabs" role="tablist">
                           {foreach from=$billing->calculator_views item=cviews name=tabs}
                               <li role="presentation"{if $smarty.foreach.tabs.first} class="active"{/if}><a href="#tab{$smarty.foreach.tabs.iteration}" role="tab" data-toggle="tab">{$billing->selectable_calculators[$cviews.id]}</a></li>
                           {/foreach}
                       </ul>
                       <div class="tab-content">
                           {foreach from=$billing->calculator_views item=cviews name=items}
                               <div id="tab{$smarty.foreach.items.iteration}" role="tabpanel" class="tab-pane fade{if $smarty.foreach.items.first} in active{/if}">
                                   {include file=$cviews.view calcid=$cviews.id}
                               </div>
                           {/foreach}
                       </div>
                   </div>
                   {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
                    {loading}
               </div>
            </div>
        {/if} {** END IF $product->requiredBilling **}

        {if $order->shipping_required == true}
            <h1>SHIPPING INFORMATION SHOULD GO HERE ONCE IT IS IMPLEMENTED !!!!</h1>
        {/if} {**   END IF $order->shipping_required  **}

        <div class="cartitems info">
            <h2>{'Your cart contents'|gettext}</h2>
            <p>{'You\'ve got'|gettext} <strong>{$order->orderitem|@count}</strong> item{if $order->orderitem|@count > 1}s{/if} {'in your cart.'|gettext} {br}
            <a id="expandcart" href="#" class="fox-link">{'Show them?'|gettext}<span></span></a></p>
            <div id="shoppingcartwrapper">
                {*{chain controller=cart action=quickpay_donation_cart}*}
                {showmodule controller=cart action=quickpay_donation_cart}
            </div>
        </div>
        {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
{script unique="shoppingcartcheckout" yui3mods=1 src="`$smarty.const.JS_RELATIVE`exp-ecomcheckout.js"}

{/script}

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}