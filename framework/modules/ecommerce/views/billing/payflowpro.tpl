{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="billing-method payflowpro creditcard-form">
    {form name="ccinfoform`$key`" controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=$calcid}
        {$billing->form.$calcid}
        {*<button id="continue-checkout{$key}" type="submit" class="{button_style}">{"Continue Checkout"|gettext}</button>*}
        {control type="buttongroup" id="continue-checkout" class="add-to-cart-btn" submit="Continue Checkout"|gettext}
    {/form}
</div>

{script unique="continue-checkout"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {    
    Y.one('#continue-checkout{/literal}$key{literal}').on('click',function(e){
        e.halt();
        Y.one('#ccinfoform{/literal}$key{literal}').submit();
    });
});
{/literal}
{/script}  