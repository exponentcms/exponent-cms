{*
 * Copyright (c) 2004-2020 OIC Group, Inc.
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

<style type="text/css">
    {*{literal}*}
        {*.address.show span {*}
            {*display:block;*}
        {*}*}
        {*span.pmt-label,*}
        {*td.pmt-label {*}
            {*color: #555555;*}
            {*display: inline-block;*}
            {*font-weight: bold;*}
            {*padding: 5px;*}
            {*text-align: right;*}
            {*width: 170px;*}
        {*}*}
    {*{/literal}*}
    {$css}
</style>

<div id="invoice" style="color:#000000; font-size:100%; position:relative; text-align: left; margin: 0px; padding: 0px;">
    <div id="invoice-data">
        <table style="border:0 none; width:100%; color:#000000; margin-bottom: 1em; " border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="background:none repeat scroll 0 0 #CDCDCD;">
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top;">
                        {"Source Site"|gettext}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top;">
                        {"Order #"|gettext}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top;">
                        {"Order Date"|gettext}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top;">
                        {"Date Shipped"|gettext}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #DEDEDE;">
                        {ecomconfig var=storename}
                        {ecomconfig var=ecomheader}
                    </td>
                    <td style="border:1px solid #DEDEDE;">
                        {$order->invoice_id}
                    </td>
                    <td style="border:1px solid #DEDEDE;">
                        {$order->purchased|format_date:"%A, %B %e, %Y"}
                    </td>
                    <td style="border:1px solid #DEDEDE;">
                        {if $order->shipped}
                            {$order->shipped|format_date:"%A, %B %e, %Y":"Not Shipped Yet"}
                            {if $shipping->shippingmethod->delivery}
                                {br}{'Estimated Delivery Date'|gettext}: {$shipping->shippingmethod->delivery|date_format}
                            {/if}
                        {else}
                            {"Not Shipped Yet"|gettext}
                            {if $shipping->shippingmethod->delivery}
                                {br}{'Estimated Delivery Date'|gettext}: {$shipping->shippingmethod->delivery|date_format}
                            {/if}
                        {/if}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="payment-info" style="margin-bottom:1em;" width="100%" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="background:none repeat scroll 0 0 #CDCDCD;">
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top; width: 30%;">
                        {"Billing Address"|gettext}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top; width: 30%">
                        {"Shipping Address"|gettext}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top; width: 39%">
                        {"Payment Info"|gettext}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #DEDEDE; text-align:left; vertical-align:top; padding:0.5em;">
                        {$order->billingmethod[0]->addresses_id|address}
                    </td>
                    <td style="border:1px solid #DEDEDE; text-align:left; vertical-align:top; padding:0.5em;">
                        {$shipping->shippingmethod->addresses_id|address}
                        {br}
                        <strong>{"Shipping Method"|gettext}:</strong>{br}
                        {$shipping->shippingmethod->option_title}
                    </td>
                    <td class="div-rows" style="border:1px solid #DEDEDE; text-align:left; vertical-align:top; padding:0.5em;">
                        {$billinginfo}
                        {*<div class="odd">*}
                            {*<span class="pmt-label">*}
                                {*{"Payment Method"|gettext}*}
                            {*</span>*}
                            {*<span class="pmt-value">*}
                                {*{$billing->calculator->getPaymentMethod($billing->billingmethod)}*}
                            {*</span>*}
                        {*</div>*}
                        {*<div class="even">*}
                            {*<span class="pmt-label">*}
                                {*{"Payment Status"|gettext}*}
                            {*</span>*}
                            {*<span class="pmt-value">*}
                                {*{$billing->calculator->getPaymentStatus($billing->billingmethod)}*}
                            {*</span>*}
                        {*</div>*}
                        {*<div class="odd">*}
                            {*<span class="pmt-label">*}
                                {*{"Payment Authorization #"|gettext}*}
                            {*</span>*}
                            {*<span class="pmt-value">*}
                                {*{$billing->calculator->getPaymentAuthorizationNumber($billing->billingmethod)}*}
                            {*</span>*}
                        {*</div>*}
                        {*<div class="even">*}
                            {*<span class="pmt-label">*}
                                {*{"Payment Reference #"|gettext}*}
                            {*</span>*}
                            {*<span class="pmt-value">*}
                                {*{$billing->calculator->getPaymentReferenceNumber($billing->billingmethod)}*}
                            {*</span>*}
                        {*</div>*}
                        {*<div class="odd">*}
                            {*<span class="pmt-label">*}
                                {*{"AVS Address Verified"|gettext}*}
                            {*</span>*}
                            {*<span class="pmt-value">*}
                                {*{$billing->calculator->getAVSAddressVerified($billing->billingmethod)}*}
                            {*</span>*}
                        {*</div>*}
                        {*<div class="even">*}
                            {*<span class="pmt-label">*}
                                {*{"AVS ZIP Verified"|gettext}*}
                            {*</span>*}
                                {*<span class="pmt-value">*}
                            {*{$billing->calculator->getAVSZipVerified($billing->billingmethod)}*}
                            {*</span>*}
                        {*</div>*}
                        {*<div class="odd">*}
                            {*<span class="pmt-label">*}
                                {*{"CVV # Matched"|gettext}*}
                            {*</span>*}
                            {*<span class="pmt-value">*}
                                {*{$billing->calculator->getCVVMatched($billing->billingmethod)}*}
                            {*</span>*}
                        {*</div>                       *}
                    </td>
                </tr>
            </tbody>
        </table>

        {$sm=$order->orderitem[0]->shippingmethod}
        {if $sm->to != "" || $sm->from != "" || $sm->message != ""}
        <table style="margin-bottom:1em;" class="gift-message" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="background:none repeat scroll 0 0 #CDCDCD;">
                    <th>
                    {"Gift Message"|gettext}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{"To:"|gettext} </strong>{$sm->to}{br}
                        <strong>{"From"|gettext}: </strong>{$sm->from}{br}
                        <strong>{"Message"|gettext}: </strong>{$sm->message}{br}
                    </td>
                </tr>
            </tbody>
        </table>
        {/if}

        <table style="margin-bottom:1em;" class="order-items" border="0" width="100%" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="background:none repeat scroll 0 0 #CDCDCD;">
                    <th style="border:1px solid #DEDEDE;">
                        {"QTY"|gettext}
                    </th>
                    <th style="border:1px solid #DEDEDE;">
                        {"SKU"|gettext}
                    </th>
                    <th style="border:1px solid #DEDEDE;">
                        {"Description"|gettext}
                    </th>
                    <th style="text-align:right; border:1px solid #DEDEDE;">
                        {"Price"|gettext}
                    </th>
                    <th style="text-align:right; border:1px solid #DEDEDE;">
                        {"Amount"|gettext}
                    </th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$order->orderitem item=oi}
                <tr class="{cycle values="odd, even"}">
                    <td style="border:1px solid #DEDEDE;">
                        {$oi->quantity}
                    </td>
                    <td style="border:1px solid #DEDEDE;">
                        {$oi->products_model}
                    </td>
                    <td style="border:1px solid #DEDEDE;">
                        {$oi->getProductsName()}
                        {if $oi->opts[0]}
                            {br}
                            {foreach from=$oi->opts item=options}
                                {$oi->getOption($options)}{br}
                            {/foreach}
                        {/if}
                        {$oi->getUserInputFields('br')}
                        {$oi->getExtraData()}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE;">
                        {$oi->products_price|number_format:2}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE;">
                        {$oi->getLineItemTotal()|number_format:2}
                    </td>
                </tr>
            {/foreach}

            </tbody>
        </table>

        <table style="margin-bottom:1em;" class="totals-info" width="100%" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="background:none repeat scroll 0 0 #CDCDCD;">
                    <th colspan=3 style="text-align: left; border:1px solid #DEDEDE;">
                        {"Totals"|gettext}
                    </th>
               </tr>
            </thead>
            <tbody>
                <tr class="{cycle values="odd, even"}">
                    <td style="border:1px solid #DEDEDE;">
                        {"Subtotal"|gettext}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px">
                        {currency_symbol}
                    </td>
                    <td  style="text-align:right; border:1px solid #DEDEDE; border-left:0px;">{$order->subtotal|number_format:2}
                    </td>
                </tr>
                 {if isset($order->order_discounts[0]) && $order->order_discounts[0]->isCartDiscount()}
                 <tr class="{cycle values="odd, even"}">
                    <td style="border:1px solid #DEDEDE;">
                        {"Total Discounts (Code"|gettext}: {$order->order_discounts[0]->coupon_code})
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px">
                        {currency_symbol}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE;  border-left:0px;">-{$order->total_discounts|number_format:2}
                    </td>
                </tr>
                <tr class="{cycle values="odd, even"}">
                    <td style="border:1px solid #DEDEDE;">
                        {"Total"|gettext}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px">
                        {currency_symbol}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE;  border-left:0px;">{$order->total|number_format:2}
                    </td>
                </tr>
                {/if}
                {if !$order->shipping_taxed}
                  <tr class="{cycle values="odd, even"}">
                    <td width="90%" style="border:1px solid #DEDEDE;">
                        {"Tax"|gettext|cat:" - "}
                        {foreach from=$order->taxzones item=zone}
                            {$zone->name} ({$zone->rate}%)
                        {foreachelse}
                            ({"Not Required"|gettext})
                        {/foreach}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px;">
                        {currency_symbol}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE; border-left:0px;">{$order->tax|number_format:2}
                    </td>
                </tr>
                {/if}
                <tr class="{cycle values="odd, even"}">
                    <td style="border:1px solid #DEDEDE;">
                        {if isset($order->order_discounts[0]) && $order->order_discounts[0]->isShippingDiscount()}
                            {"Shipping & Handling (Discount Code"|gettext}: {$order->order_discounts[0]->coupon_code})
                        {else}
                            {"Shipping & Handling"|gettext}
                        {/if}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px;">
                        {currency_symbol}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE;  border-left:0px;">{$order->shipping_total|number_format:2}
                    </td>
                </tr>
                {if $order->surcharge_total != 0}
                    <tr class="{cycle values="odd, even"}">
                        <td style="border:1px solid #DEDEDE;">
                            {"Freight Surcharge"|gettext}
                        </td>
                        <td style="border:1px solid #DEDEDE; border-right:0px;">
                            {currency_symbol}
                        </td>
                        <td style="text-align:right;border:1px solid #DEDEDE;  border-left:0px;">{$order->surcharge_total|number_format:2}
                        </td>
                    </tr>
                {/if}
                {if $order->shipping_taxed}
                  <tr class="{cycle values="odd, even"}">
                    <td width="90%" style="border:1px solid #DEDEDE;">
                        {"Tax"|gettext|cat:" - "}
                        {foreach from=$order->taxzones item=zone}
                            {$zone->name} ({$zone->rate}%)
                        {foreachelse}
                            ({"Not Required"|gettext})
                        {/foreach}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px;">
                        {currency_symbol}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE; border-left:0px;">{$order->tax|number_format:2}
                    </td>
                </tr>
                {/if}
                <tr class="{cycle values="odd, even"}">
                    <td style="border:1px solid #DEDEDE;">
                        {"Order Total"|gettext}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px;">
                        {currency_symbol}
                    </td>
                    <td style="text-align:right;border:1px solid #DEDEDE;  border-left:0px;">{$order->grand_total|number_format:2}
                    </td>
                </tr>
                <tr class="{cycle values="odd, even"}">
                    <td colspan="3" style="word-wrap:break-word; white-space: normal; border:1px solid #DEDEDE;">
                        {if $order->comments != ""}
                            <strong>{'Order Comments'|gettext}:</strong> {$order->comments}
                        {else}
                            <strong>{'Order Comments'|gettext}:</strong> {'NONE supplied'|gettext}.
                        {/if}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="store-footer">
        {ecomconfig var=ecomfooter}
    </div>
</div>
