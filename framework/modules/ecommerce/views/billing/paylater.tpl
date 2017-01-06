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

{css unique="general-ecom" link="`$asset_path`css/creditcard-form.css"}

{/css}

<div class="billing-method payflowpro creditcard-form">
    {form name="ccinfoform`$key`" controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=$calcid}
        {$billing->form.$calcid}
        {*<button id="continue-checkout" type="submit" class="add-to-cart-btn {button_style}">{"Continue Checkout"|gettext}</button>*}
        {control type="buttongroup" id="continue-checkout" class="shopping-cart" color=green size=large submit="Continue Checkout"|gettext}
    {/form}
</div>
