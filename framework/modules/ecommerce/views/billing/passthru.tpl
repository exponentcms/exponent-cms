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

{css unique="general-ecom" link="`$asset_path`css/creditcard-form.css"}

{/css}

<div class="billing-method">
    {* edebug var=$default_order_type}
    {edebug var=$order_types *}
    {form name="passthruform" controller=cart action=preprocess}
        {*{control type="hidden" name="billingcalculator_id" value=6}*}
        {control type="hidden" name="billingcalculator_id" value=$calcid}
        <blockquote>
            {"You may place your order and pay with a check or money order.  If paying by check, your order will be held util we receive the check and it clears our bank account.  Money order orders will be processed upon our receipt of the money order."|gettext}
        </blockquote>
        {control type=text name="cash_amount" label="Cash Amount"|gettext filter=money}
        <table>
            <tr>
                <td width="150" style="vertical-align:top;">
                    {control type=radiogroup columns=1 name="order_type" label="Select Order Type"|gettext items=$order_types default=$default_order_type flip=false}
                </td>
                <td style="vertical-align:top;">
                    {control type=radiogroup columns=1 name="order_status" label="Select Order Status"|gettext items=$order_statuses default=$default_order_status flip=false}
                </td>
                {if !empty($sales_reps)}
                <td style="vertical-align:top;">
                    {control type="dropdown" name="sales_rep_1_id" label="Sales Rep 1 (Initial Order)"|gettext includeblank=true items=$sales_reps value=$order->sales_rep_1_id}
                    {control type="dropdown" name="sales_rep_2_id" label="Sales Rep 2 (Completed Order)"|gettext includeblank=true items=$sales_reps value=$order->sales_rep_2_id}
                    {control type="dropdown" name="sales_rep_3_id" label="Sales Rep 3 (Other)"|gettext includeblank=true items=$sales_reps value=$order->sales_rep_3_id}
                </td>
                {/if}
            </tr>
        </table>
        {*<a class="{button_style}" href="#" id="continue-passthru-checkout" class="exp-ecom-link"><strong><em>{'Continue To Last Step'|gettext}</em></strong></a>*}
        {*<button id="continue-passthru-checkout" type="submit" class="add-to-cart-btn {button_style}">{"Continue Checkout"|gettext}</button>*}
        {*{icon button=true action=scriptaction id="continue-passthru-checkout" class="exp-ecom-link" text='Continue To Last Step'|gettext}*}
        {control type="buttongroup" id="continue-checkout" class="shopping-cart" color=green size=large submit="Continue Checkout"|gettext}
    {/form}
    {*<div style="clear:both;"></div>*}
</div>

{*script unique="continue-passthru-checkout" yui3mods="1"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
        //Y.one('#cont-checkout').setStyle('display','none');
        Y.one('#continue-passthru-checkout').on('click',function(e){
            e.halt();
            Y.one('#passthruform').submit();
        });
    });
{/literal}
{/script*}