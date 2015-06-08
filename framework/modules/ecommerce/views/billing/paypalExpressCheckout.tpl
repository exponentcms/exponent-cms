{*
 * Copyright (c) 2004-2015 OIC Group, Inc.
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
        {form controller=cart action=preprocess id=paypalexpress}
            {control type="hidden" name="billingcalculator_id" value=$calcid}
            <input id="continue-checkout" type="image" name="submit" value="1" src="https://fpdbs.paypal.com/dynamicimageweb?cmd=_dynamic-image&locale={$smarty.const.LOCALE}">
        {/form}
    {else}
        <h4>{'PayPal Express Checkout is unavailable for this transaction'|gettext}</h4>
    {/if}
</div>

