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

{if $printerfriendly==1}
    {$pf=1}
    {*{if $include_css == true}*}
        {*{css unique="invoice" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/print-invoice.css"}    *}
        {*{/css}*}
    {*{/if}*}
    <style type="text/css">
        {$css}
    </style>
{else}
    {*{css unique="invoice" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/invoice.css"}*}
    {*{/css}*}
{/if}
<style type="text/css">
    .table-striped>tbody>tr:nth-child(even)>td,
    .table-striped>tbody>tr:nth-child(even)>th {
    	background-color: rgb(235, 235, 235);
    }
    #invoice-data > div > div > table > thead,
    #invoice-data > div > div > table,
    #invoice-data > div > div > table.payment-info > tbody > tr > td {
    	border: solid #cacaca 1px;
    }
    .height {
        min-height: 260px;
    }
    #invoice-data > div > div > table > tbody > tr > td.pmt-value {
        text-align: right;
    }
</style>

<div id="invoice row">
    <div class="col-sm-12">
        <div id="store-header">
            <h1>{ecomconfig var=storename} {'Invoice'|gettext}</h1>
            {ecomconfig var=ecomheader}
        </div>
        {if $pf && ecomconfig::getConfig('enable_barcode')}
        <div id="barcode">
            <img style="margin:10px" src="{$smarty.const.PATH_RELATIVE}external/barcode.php?barcode={$order->invoice_id}&amp;width=400&amp;height=50" alt="{'Barcode'|gettext}">
        </div>
        {/if}
    </div>
    <div id="invoice-data" class="col-sm-12">
        <div class="row">
            <div class="col-sm-12">
                <table class="table order-info">
                    <thead>
                        <tr>
                            <th>
                                {"Source Site"|gettext}
                            </th>
                            <th>
                                {"Order #"|gettext}
                            </th>
                            <th>
                                {"Order Date"|gettext}
                            </th>
                            <th>
                                {"Order Type"|gettext}
                            </th>
                            <th>
                                {"Date Shipped"|gettext}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {ecomconfig var=storename}
                            </td>
                            <td>
                                {$order->invoice_id}
                                {permissions}
                                    {if $permissions.edit_invoice_id && !$pf}
                                        <div class="item-permissions">
                                            {icon class="edit" action=edit_invoice_id id=$order->id title='Edit Invoice Number'|gettext}
                                        </div>
                                    {/if}
                                 {/permissions}
                            </td>
                            <td>
                                {$order->purchased|format_date:"%A, %B %e, %Y"}
                            </td>
                            <td>
                                {$order->order_type->title}
                            </td>
                            <td>
                                {if $order->shipped}
                                    {if !$order->shipping_required}
                                        {'No Shipping Required'|gettext}
                                    {else}
                                        {$order->shipped|format_date:"%A, %B %e, %Y":"Not Shipped Yet"}
                                        {if $shipping->shippingmethod->delivery}
                                            {br}{'Estimated Delivery Date'|gettext}: {$shipping->shippingmethod->delivery|date_format}
                                        {/if}
                                    {/if}
                                {else}
                                    {"Not Shipped Yet"|gettext}
                                    {if $shipping->shippingmethod->delivery}
                                        {br} {'Estimated Delivery Date'|gettext}: {$shipping->shippingmethod->delivery|date_format}
                                    {/if}
                                {/if}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-xs-6">
                <div class="panel panel-default height">
                    <div class="panel-heading">
                        {"Billing Address"|gettext}
                    </div>
                    <div class="panel-body">
                        {$order->billingmethod[0]->addresses_id|address}
                        {permissions}
                            <div class="item-permissions item-actions">
                                {if $permissions.edit_address && !$pf}
                                    <div class="item-permissions">
                                        {icon class="edit" action=edit_address id=$order->id type='b' title='Edit Billing Address'|gettext}
                                    </div>
                                {/if}
                            </div>
                        {/permissions}
                    </div>
                </div>
            </div>

            <div class="col-xs-6">
                <div class="panel panel-default height">
                    <div class="panel-heading">
                        {"Shipping Address"|gettext}
                    </div>
                    <div class="panel-body">
                        {if $order->shipping_required}
                            {$shipping->shippingmethod->addresses_id|address}
                            {permissions}
                                <div class="item-permissions item-actions">
                                    {if $permissions.edit_address && !$pf}
                                        <div class="item-permissions">
                                            {icon class="edit" action=edit_address id=$order->id type='s' title='Edit Shipping Address'|gettext}
                                        </div>
                                    {/if}
                                </div>
                            {/permissions}
                            {br}
                            <table style="width: 100%; border: 0px; text-align: left; padding: 0px; margin:0px;">
                                <tr style="border: 0px; padding: 0px; margin:0px;vertical-align: top">
                                    <td style="border: 0px; text-align: left; padding: 0px; margin:0px;">
                                        <strong>{"Shipping Method"|gettext}:</strong>
                                        {$shipping->shippingmethod->option_title}
                                        {permissions}
                                            <div class="item-permissions item-actions">
                                                {if $permissions.edit_shipping_method && !$pf}
                                                    <div class="item-permissions">
                                                        {icon class="edit" action=edit_shipping_method id=$order->id title='Edit Shipping Method'|gettext}
                                                    </div>
                                                {/if}
                                            </div>
                                        {/permissions}
                                    </td>
                                    <td style="border: 0px; text-align: left; padding: 0px; padding-right: 5px; margin:0px;">
                                        {if $shipping->shippingmethod->carrier != ''}
                                        <strong>{"Carrier"|gettext}:</strong>
                                        {$shipping->shippingmethod->carrier}
                                        {/if}
                                    </td>
                                </tr>
                            </table>
                        {/if}
                    </div>
                </div>
            </div>

            {$sm=$order->orderitem[0]->shippingmethod}
            {if $sm->to != "" || $sm->from != "" || $sm->message != ""}
            <div class="col-sm-12">
                <table class="gift-message">
                    <thead>
                        <tr>
                            <th>
                            {"Gift Message"|gettext}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>{'To:'|gettext} </strong>{$sm->to}{br}
                                <strong>{'From'|gettext}: </strong>{$sm->from}{br}
                                <strong>{'Message'|gettext}: </strong>{$sm->message}{br}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {/if}

            <div class="col-sm-12">
                <table class="table table-striped order-items">
                    <thead>
                        <tr>
                            <th>
                                {"QTY"|gettext}
                            </th>
                            <th>
                                {"SKU"|gettext}
                            </th>
                            <th>
                                {"Description"|gettext}
                            </th>
                            {*<th>*}
                                {*{"Location"|gettext}*}
                            {*</th>*}
                            <th>
                                {"Status"|gettext}
                            </th>
                            <th style="text-align:right;">
                                {"Price"|gettext}
                            </th>
                            <th style="text-align:right;">
                                {"Amount"|gettext}
                            </th>
                            {permissions}
                                {if $permissions.edit_order_item && !$pf}
                                    <div class="item-permissions">
                                        <th style="text-align:right;"></th>
                                    </div>
                                {/if}
                           {/permissions}
                        </tr>
                    </thead>
                    <tbody>
                    {foreach from=$order->orderitem item=oi}
                        <tr class="{cycle values="odd, even"}">
                            <td>
                                {$oi->quantity}
                            </td>
                            <td>
                                {if $oi->products_model != ""}{$oi->products_model}{else}N/A{/if}
                            </td>
                            <td>
                                {$oi->getProductsName()}
                                {if $oi->opts[0]}
                                    {br}
                                    {foreach from=$oi->opts item=options}
                                        {$oi->getOption($options)}{br}
                                    {/foreach}
                                {/if}
                                {$oi->getUserInputFields('br')}
                                {*{if $oi->product_type == "product" || $oi->product_type == "childProduct"}*}
                                    {$oi->getExtraData()}
                                {*{else}*}
                                    {*{$oi->getFormattedExtraData('list')}*}
                                {*{/if}*}
                            </td>
                            {*<td>*}
                                {*{$oi->products_warehouse_location}*}
                            {*</td>*}
                            <td>
                                {$oi->products_status}
                            </td>
                            <td style="text-align:right;">
                                {$oi->products_price|number_format:2}
                            </td>
                            <td style="text-align:right;">
                                {$oi->getTotal()|number_format:2}
                            </td>
                            {permissions}
                                {if $permissions.edit_order_item && !$pf}
                                    <div class="item-permissions">
                                        <td style="text-align:right;">
                                            {icon class="edit" action=edit_order_item id=$oi->id orderid=$order->id title='Edit Invoice Item'|gettext}&#160;
                                            {icon class="delete" action=delete_order_item id=$oi->id orderid=$order->id onclick="return confirm('Are you sure you want to delete this item from this order?')" title='Delete Invoice Item'|gettext}
                                        </td>
                                    </div>
                                {/if}
                            {/permissions}
                        </tr>
                    {/foreach}
                     {permissions}
                         {if $permissions.add_order_item && !$pf}
                            <div class="item-permissions">
                            <tr>
                                <td colspan="8"><!--a href="{link action=add_order_item id=$order->id}">[+]</a-->
                                    {capture assign="callbacks"}
                                    {literal}
                                    // the text box for the title
                                    var tagInput = Y.one('#add_new_item_autoc');

                                    // the UL to append to
                                    var tagUL = Y.one('#new_items');

                                    var appendToList = function(e) {
                                        var val = e.result.raw.id;
                                        tagUL.appendChild(createHTML(val));
                                        return true;
                                    }

                                    var removeLI = function(e) {
                                        e.target.set('value', '');
                                        tagUL.get('children').remove();
                                    }

                                    var createHTML = function(val) {
                                        var f = '<form role="form" id=addItem method=post>';
                                            f += '<input type=hidden name=orderid id=orderid value={/literal}{$order->id}{literal}>';
                                            f += '<input type=hidden name=controller id=controller value=order>';
                                            f += '<input type=hidden name=action id=action value=add_order_item>';
                                            f += '<input type=hidden name=product_id id=product_id value=' + val + '>';
                                            f += '<input type=submit class="add {/literal}{expTheme::buttonStyle('green')}{literal}" name=submit value="{/literal}{'Add This Item'|gettext}{literal}">';
                                            f += '</form>';
                                        var newLI = Y.Node.create(f);
                                        return newLI;
                                    }

                                    tagInput.on('click',removeLI);

                                    // format the results coming back in from the query
                                    autocomplete.ac.set('resultFormatter', function(query, results) {
                                        return Y.Array.map(results, function (result) {
                                            var result = result.raw;

                                            var template;
                                            // image
                                            if (result.fileid) {
                                                template = '<pre><img width="30" height="30" class="srch-img" src="'+EXPONENT.PATH_RELATIVE+'thumb.php?id='+result.fileid+'&w=30&h=30&zc=1" />';
                                            } else {
                                                template = '<pre><img width="30" height="30" class="srch-img" src="'+EXPONENT.PATH_RELATIVE+'framework/modules/ecommerce/assets/images/no-image.jpg" />';
                                            }
                                            // title
                                            template += ' <strong class="title">'+result.title+'</strong>';
                                            // model/SKU
                                            if (result.model) template += ' <em class="title">SKU: '+result.model+'</em>';
                                            //template += '<div style="clear:both;">';
                                            template += '</pre>';

                                            return template;
                                        });
                                    })

                                    // what should happen when the user selects an item?
                                    autocomplete.ac.on('select', function (e) {
                                        appendToList(e);
                                    });
                                    {/literal}
                                    {/capture}
                                    {control type="autocomplete" controller="store" action="search" name="add_new_item" label="Add a new item"|gettext placeholder="Search title or SKU to add an item" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" maxresults=30 jsinject=$callbacks}
                                    <div id="new_items">
                                    </div>
                                </td>
                            </tr>
                            </div>
                         {/if}
                     {/permissions}
                    </tbody>
                </table>
            </div>

            <div class="col-xs-6">
                <table class="table table-striped payment-info">
                    <thead>
                        <tr>
                            <th class="payment-info-header" colspan="2">
                                {"Payment Info"|gettext}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {if !$permissions.edit_shipping_method || $pf}
                            <tr><td>
                                {$billinginfo}
                            </td></tr>
                        {else}
                        <tr class="odd">
                            <td class="pmt-label">
                                {"Payment Method"|gettext}
                            </td>
                            <td class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getPaymentMethod($billing->billingmethod)}
                                {else}
                                    {'No Cost'|gettext}
                                {/if}
                            </td>
                        </tr>
                        <tr class="even">
                            <td class="pmt-label">
                                {"Payment Status"|gettext}
                            </td>
                            <td class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getPaymentStatus($billing->billingmethod)}
                                {else}
                                    {'complete'|gettext}
                                {/if}
                            </td>
                        </tr>
                        <tr class="odd">
                            <td class="pmt-label">
                                {"Payment Authorization #"|gettext}
                            </td>
                            <td class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getPaymentAuthorizationNumber($billing->billingmethod)}
                                {/if}
                            </td>
                        </tr>
                        <tr class="even">
                            <td class="pmt-label">
                                {"Payment Reference #"|gettext}
                            </td>
                            <td class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getPaymentReferenceNumber($billing->billingmethod)}
                                {/if}
                            </td>
                        </tr>
                        {if $billing->calculator != null}
                        {$data = $billing->calculator->getAVSAddressVerified($billing->billingmethod)|cat:$billing->calculator->getAVSZipVerified($billing->billingmethod)|cat:$billing->calculator->getCVVMatched($billing->billingmethod)}
                        {if  !empty($data)}
                        <tr class="odd">
                            <td class="pmt-label">
                                {"AVS Address Verified"|gettext}
                            </td>
                            <td class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getAVSAddressVerified($billing->billingmethod)}
                                {/if}
                            </td>
                        </tr>
                        <tr class="even">
                            <td class="pmt-label">
                                {"AVS ZIP Verified"|gettext}
                            </td>
                            <td class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getAVSZipVerified($billing->billingmethod)}
                                {/if}
                            </td>
                        </tr>
                        <tr class="odd">
                            <td class="pmt-label">
                                {"CVV # Matched"|gettext}
                            </td>
                            <td class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getCVVMatched($billing->billingmethod)}
                                {/if}
                            </td>
                        </tr>
                        {/if}
                        {/if}
                        {permissions}
                            {if $permissions.edit_shipping_method && !$pf}
                                <tr><td></td><td>
                                <div class="item-permissions">
                                    {icon class="edit" action=edit_payment_info id=$order->id title='Edit Payment Method'|gettext}
                                </div>
                                </td></tr>
                            {/if}
                        {/permissions}
                        {/if}
                    </tbody>
                </table>
            </div>

            <div class="col-xs-6">
                <table class="table table-striped totals-info">
                    <thead>
                        <tr>
                            {if !$pf}
                            <th>
                            {else}
                            <th  colspan=3>
                            {/if}
                                {"Totals"|gettext}
                            </th>
                            {if !$pf}<th colspan="2"></th>{/if}
                       </tr>
                    </thead>
                    <tbody>
                        <tr class="{cycle values="odd, even"}">
                            <td>
                                {"Subtotal"|gettext}
                            </td>
                            <td style="border-right:0px">
                                {currency_symbol}
                            </td>
                            <td  style="text-align:right; border-left:0px;">{$order->subtotal|number_format:2}
                            </td>
                        </tr>

                         {if (isset($order->order_discounts[0]) && $order->order_discounts[0]->isCartDiscount()) || $order->total_discounts > 0}
                         <tr class="{cycle values="odd, even"}">
                            <td>
                            {if isset($order->order_discounts[0]) && $order->order_discounts[0]->isCartDiscount()}
                                {"Total Cart Discounts (Code"|gettext}: {$order->order_discounts[0]->coupon_code})
                            {else}
                                {"Total Cart Discounts"|gettext}
                            {/if}

                            </td>
                            <td style="border-right:0px">
                                {currency_symbol}
                            </td>
                            <td style="text-align:right; border-left:0px;">-{$order->total_discounts|number_format:2}
                            </td>
                        </tr>
                        <tr class="{cycle values="odd, even"}">
                            <td>
                                {"Total"|gettext}
                            </td>
                            <td style="border-right:0px">
                                {currency_symbol}
                            </td>
                            <td style="text-align:right; border-left:0px;">{$order->total|number_format:2}
                            </td>
                        </tr>
                         {/if}
                         {if !$order->shipping_taxed}
                          <tr class="{cycle values="odd, even"}">
                            <td width="90%">
                                {"Tax"|gettext|cat:" - "}
                            {foreach from=$order->taxzones item=zone}
                                {$zone->name} ({$zone->rate}%)
                            {foreachelse}
                                ({'Not Required'|gettext})
                            {/foreach}
                            </td>
                            <td style="border-right:0px;">
                                {currency_symbol}
                            </td>
                            <td style="text-align:right; border-left:0px;">{$order->tax|number_format:2}
                            </td>
                        </tr>
                        {/if}
                        <tr class="{cycle values="odd, even"}">
                            <td>
                            {if isset($order->order_discounts[0]) && $order->order_discounts[0]->isShippingDiscount()}
                                {"Shipping & Handling (Discount Code"|gettext}: {$order->order_discounts[0]->coupon_code})
                            {else}
                                {"Shipping & Handling"|gettext}
                            {/if}

                            </td>
                            <td style="border-right:0px;">
                                {currency_symbol}
                            </td>
                            <td style="text-align:right;  border-left:0px;">{$order->shipping_total|number_format:2}
                            </td>
                        </tr>
                        {if $order->surcharge_total != 0}
                            <tr class="{cycle values="odd, even"}">
                                <td>
                                    {"Freight Surcharge"|gettext}
                                </td>
                                <td style="border-right:0px;">
                                    {currency_symbol}
                                </td>
                                <td style="text-align:right; border-left:0px;">{$order->surcharge_total|number_format:2}
                                </td>
                            </tr>
                        {/if}
                        {if $order->shipping_taxed}
                         <tr class="{cycle values="odd, even"}">
                           <td width="90%">
                               {"Tax"|gettext|cat:" - "}
                           {foreach from=$order->taxzones item=zone}
                               {$zone->name} ({$zone->rate}%)
                           {foreachelse}
                               ({'Not Required'|gettext})
                           {/foreach}
                           </td>
                           <td style="border-right:0px;">
                               {currency_symbol}
                           </td>
                           <td style="text-align:right; border-left:0px;">{$order->tax|number_format:2}
                           </td>
                        </tr>
                        {/if}
                        <tr class="{cycle values="odd, even"}">
                            <td>
                                {"Order Total"|gettext}
                            </td>
                            <td style="border-right:0px;">
                                {currency_symbol}
                            </td>
                            <td style="text-align:right; border-left:0px;">{$order->grand_total|number_format:2}
                            </td>
                        </tr>
                        {permissions}
                            {if $permissions.edit_totals && !$pf}
                                <div class="item-permissions">
                                    <tr class="{cycle values="odd, even"}">
                                        <td style="text-align:right; border-left:0px;" colspan='3'>
                                            {icon class="edit" action=edit_totals orderid=$order->id title='Edit Totals'|gettext}
                                        </td>
                                    </tr>
                                </div>
                            {/if}
                        {/permissions}
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <div id="store-footer" class="col-sm-12">
        {ecomconfig var=ecomfooter}
    </div>
</div>
