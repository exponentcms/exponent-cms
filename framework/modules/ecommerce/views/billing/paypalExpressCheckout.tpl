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

{css unique="general-ecom" link="`$asset_path`css/creditcard-form.css"}

{/css}
<div class="billing-method creditcard-form">
    {if $order->total}
        {get_object object=paypalExpressCheckout param=$calcid assign=calc}
        {if !$calc->configdata.incontext}
            {form controller=cart action=preprocess id=paypalexpress}
                {control type="hidden" name="billingcalculator_id" value=$calcid}
                {$billing->form.$calcid}{br}
                <input id="continue-checkout" type="image" name="submit" value="1" src="https://fpdbs.paypal.com/dynamicimageweb?cmd=_dynamic-image&locale={$smarty.const.LOCALE}">
            {/form}
        {else}
            {form controller=cart action=preprocess id=paypalexpressinc}
                {control type="hidden" name="billingcalculator_id" value=$calcid}
                {$billing->form.$calcid}{br}
                {control type="hidden" name="in_context" value=1}
            {/form}
            {literal}
            <script>
                window.paypalCheckoutReady = function () {
                    paypal.checkout.setup('<{/literal}{$calc->configdata.merchantid}{literal}>' , {
                        {/literal}
                        {if $calc->configdata.testmode}environment: 'sandbox' ,{/if}
                        {if !bs()}
                        container: 'paypalexpressinc'
                        {else}
                        buttons: [{
                            container: 'paypalexpressinc',
                            // color: 'blue',
                            shape: 'rect',
                            size:  'medium'
                        }]
                        {/if}
                        {literal}
                    });
                };
            </script>
            {/literal}
            <script src="//www.paypalobjects.com/api/checkout.js" async></script>
        {/if}
    {else}
        <h4>{'PayPal Express Checkout is unavailable for this transaction'|gettext}</h4>
    {/if}
</div>
