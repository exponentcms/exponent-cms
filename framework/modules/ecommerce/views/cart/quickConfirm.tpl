{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

<div class="module cart quick-confirm">
    <h1>{'Quick Confirmation'|gettext}</h1>
    {form action=processQuickPay}
        {if $order->billing_required == true}
            <div class="billingdetails ">
                <div class="info">
                    <h2>{'Name'|gettext}</h2>
                    {control id=fname type=text name="billing[firstname]" label="First Name"|gettext}
                    {control id=midname type=text name="billing[middlename]" label="Middle"|gettext size=5}
                    {control id=lname type=text name="billing[lastname]" label="Last Name"|gettext}
                    {clear}
                </div>
                <div class="info">
                    <h2>{'Address'|gettext}</h2>
                    {control id=addy1 type=text name="billing[address1]" label="Street Address"|gettext}
                    {control id=addy2 type=text name="billing[address2]" label="Apt/Suite #"|gettext size=10}
                    {control id=city type=text name="billing[city]" label="City"|gettext size=15}
                    {control id=state type=state name="billing[state]" label="State"|gettext}
                    {control id=zip type=text name="billing[zip]" label="Zip Code"|gettext size=6}
                    {clear}
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
