{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
<div class="billing-method">
    <h4>{'Pay with Paypal'|gettext}</h4>
    {form controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=$calcid}
        <input id="continue-checkout" type="image" name="submit" value="1" src="https://cms.paypal.com/cms_content/US/en_US/images/developer/US_AU_btn.gif">
    {/form}
</div>
