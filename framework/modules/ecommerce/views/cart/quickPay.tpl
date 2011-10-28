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
<div class="module cart quick-pay">
    {form action=processQuickPay}
    {if $order->billing_required == true}
        <div class="billingdetails ">            
            <h1>Credit Card Information</h1>
            <div class="info">
                <h2>Name as it appears on card</h2>
                {control id=fname type=text name="billing[firstname]" label="First Name"}
                {control id=midname type=text name="billing[middlename]" label="Middle" size=5}
                {control id=lname type=text name="billing[lastname]" label="Last Name"}
                {clear}
            </div>
            <div class="info">
                <h2>Billing address of the card you are using</h2>
                {control id=addy1 type=text name="billing[address1]" label="Street Address"}
                {control id=addy2 type=text name="billing[address2]" label="Apt/Suite #" size=10}
                {control id=city type=text name="billing[city]" label="City" size=15}
                {control id=state type=state name="billing[state]" label="State"}
                {control id=zip type=text name="billing[zip]" label="Zip Code" size=6}
                {clear}
            </div>
            
            <div class="info" id="ccfs">
                <h2>Credit Card</h2>
                {if $billing->form != ""}
					{assign var='calcid' value=$billing->calculator->id}
                    {$billing->form.$calcid}
                {/if}
                {clear}
            </div>
        </div>
    {/if} {** END IF $product->requiredBilling **}
    
    {if $order->shipping_required == true}
        <h1>SHIPPING INFORMATION SHOULD GO HERE ONCE IT IS IMPLEMENTED !!!!</h1>
    {/if} {**   END IF $order->shipping_required  **}
    
    
    <div class="cartitems info">
        <h2>Your cart contents</h2>
        <p>You’ve got <strong>{$order->orderitem|@count}</strong> item{if $order->orderitem|@count > 1}s{/if} in your cart. {br}
        <a id="expandcart" href="#" class="fox-link hide">Show them?<span></span></a></p>
        <div id="shoppingcartwrapper">
            {chain controller=cart action=show view=show_quickpay_donation_cart}
        </div>
    </div>
    {control type="buttongroup" submit="Submit" cancel="Cancel"}
    {/form}
    
</div>
{script unique="shoppingcartcheckout" yuimodules="animation,container,json" src=$smarty.const.JS_FULL|cat:'exp-ecomcheckout.js'}
//
{/script}


