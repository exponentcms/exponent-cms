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
    {* edebug var=$default_order_type}
    {edebug var=$order_types *}
    {form name="passthruform" controller=cart action=preprocess}
        {control type="hidden" name="billingcalculator_id" value=6}
        {* control type=radiogroup columns=1 name="passthru_order_type" label="Select Order Type" items="Standard Order (your user),Phone Order (creates new user),Save as Quote (creates new user)"|gettxtlist values="0,1,2" default=0 *}
        <table><tr><td width="150" style="vertical-align:top;">
            {control type=radiogroup columns=1 name="order_type" label="Select Order Type"|gettext items=$order_types default=$default_order_type flip=false}
            </td><td style="vertical-align:top;">
            {control type=radiogroup columns=1 name="order_status" label="Select Order Status"|gettext items=$order_statuses default=$default_order_status flip=false}
            </td><td style="vertical-align:top;">
            {*{control type=radiogroup columns=1 name="sales_rep_1_id" label="Select Sales Rep 1"|gettext items=$sales_reps flip=false}*}
            {*{control type=radiogroup columns=1 name="sales_rep_2_id" label="Select Sales Rep 2"|gettext items=$sales_reps flip=false}*}
            {*{control type=radiogroup columns=1 name="sales_rep_3_id" label="Select Sales Rep 3"|gettext items=$sales_reps flip=false}*}
            {control type="dropdown" name="sales_rep_1_id" label="Sales Rep 1 (Initial Order)"|gettext includeblank=true items=$sales_reps value=$order->sales_rep_1_id}
            {control type="dropdown" name="sales_rep_2_id" label="Sales Rep 2 (Completed Order)"|gettext includeblank=true items=$sales_reps value=$order->sales_rep_2_id}
            {control type="dropdown" name="sales_rep_3_id" label="Sales Rep 3 (Other)"|gettext includeblank=true items=$sales_reps value=$order->sales_rep_3_id}
        </td></tr></table>
        <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="#" id="continue-passthru-checkout" class="exp-ecom-link"><strong><em>{'Continue To Last Step'|gettext}</em></strong></a>
        <button id="continue-passthru-checkout" type="submit" class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}">{"Continue Checkout"|gettext}</button>   
    {/form}
    <div style="clear:both;"></div>
</div>

{script unique="continue-passthru-checkout"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
        //Y.one('#cont-checkout').setStyle('display','none');
        Y.one('#continue-passthru-checkout').on('click',function(e){
            e.halt();
            Y.one('#passthruform').submit();
        });
    });
{/literal}
{/script}