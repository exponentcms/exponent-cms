{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{css unique="invoice" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/invoice.css"}

{/css}
    
<div>
    <h1>{"Create Reference Order"|gettext}</h1>
    <div id="invoice">
        {form id=order_item_form name=order_item_form action=save_reference_order}
            {control type=hidden name=original_orderid value=$order->id}
            {"Select the order type, order status, item message, which items to roll to a backorder, and order totals from below."|gettext}{br}  {br}

            {control type="dropdown" name="order_type_id" label="Order Type:"|gettext frommodel='order_type'}
            {control type="dropdown" name="order_status_id" label="Order Status:"|gettext frommodel='order_status' orderby='rank'}
            {br}
            <table class="order-items" border="0" cellspacing="0" cellpadding="0">
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

                        <th style="text-align:right;">
                            {"Price"|gettext}
                        </th>

                        <th style="text-align:left;" nowrap="">{'Include Item?'|gettext}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$order->orderitem item=oi}
                        <tr style="vertical-align: middle;" class="{cycle values="odd, even"}">
                            <td>
                                {control size=4 type=text name="quantity[`$oi->id`]" label="" value=$oi->quantity}
                            </td>
                            <td>
                                {$oi->products_model}
                            </td>
                            <td>
                                {control type=textarea name="products_name[`$oi->id`]" cols=40 rows=2 label="" value="`$oi->products_name` [ACTION] FROM ORIGINAL ORDER #`$order->invoice_id`"}
                                {if $oi->opts[0]}
                                    {br}
                                    {foreach from=$oi->opts item=options}
                                        {$oi->getOption($options)}{br}
                                    {/foreach}
                                {/if}
                                {$oi->getUserInputFields('br')}
                                {$oi->getExtraData()}
                            </td>

                            <td style="text-align:right;">
                                {control size=4 type=text name="products_price[`$oi->id`]" label="" value=$oi->products_price}
                            </td>

                            <td>
                                {control type="checkbox" name="oi[`$oi->id`]" label=" " value=1 checked=0}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            {"You may manually update the order totals now, as well as edit them anytime from the newly created order."|gettext} {br}
            {br}

            <table width='60%'>
                <thead>
                    <tr>
                        <th colspan="2">{"Order Totals"|gettext}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>
                        {control type=text name=subtotal label="Subtotal"|gettext value=$order->subtotal} </td><td>* {'This is the total of all order items.'|gettext}
                    </td></tr>
                    <tr><td>
                        {control type=text name=total_discounts label="Total Discounts"|gettext value=$order->total_discounts}</td><td> * {"Total discounts you want reflected on this order."|gettext}
                    </td></tr>
                    <tr><td>
                        {control type=text name=tax label="Tax"|gettext value=$order->tax}</td><td>* {'Total of tax for this order'|gettext}
                    </td></tr>
                    <tr><td>
                        {control type=text name=shipping_total label="Shipping Total"|gettext value=$order->shipping_total}</td><td> * {"Total of shipping for this order."|gettext}
                    </td></tr>
                    <tr><td>
                        {control type=text name=surcharge_total label="Surcharge Total"|gettext value=$order->surcharge_total} </td><td>* {"Total of per-product shipping surcharges."|gettext}
                    </td></tr>
                    <tr><td>
                        {"You may enter the grand total manually, or select the checkbox below to auto calculate the grand total based on the other fields."|gettext}
                        {control type=text name=grand_total label="Grand Total"|gettext value=$order->grand_total}</td><td> * {"Grand total of the order"|gettext}
                    </td></tr>
                    <tr><td colspan="2">
                        {control type=checkbox label='Auto calculate grand total?'|gettext flip=true name=autocalc value=1 checked=true}
                    </td></tr>
                </tbody>
            </table>
            {* control id=submit_order_item_form name=submit_order_item_form type=buttongroup submit="Save Reference Order" cancel="Cancel"*}
            <div id="submit_order_item_formControl" class="control buttongroup"><input id="submit_order_item_form" class="submit button" type="submit" value="Save Reference Order" /><input class="cancel button" type="button" value="Cancel" onclick="history.back(1);" /></div>
        {/form}
    </div>
</div>

{script unique="children-submit"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
        Y.one('#submit_order_item_form').on('click',function(e){
            e.halt();
            var frm = Y.one('#order_item_form');
            var chcks = frm.all('input[type="checkbox"]');

            bxchkd=0;
            var msg = ""

            chcks.each(function(bx,key){
                if (bx.get('name') != 'autocalc' && bx.get('checked')) {
                    bxchkd++;
                };
            });

            if (bxchkd==0) {
                alert('{/literal}{"You may not create an order with no items selected. Select the checkbox in the \'Include Item?\' column above to include at least one item on your reference order. You may edit that item anytime, or remove the included item after you add an additional one."|gettext}{literal}')
                //var pRet = confirm('Are you sure you want to continue with no items selected?\nClick Cancel below and select the checkbox in the "Include Item?" column above to include an item on your reference order, or click OK to continue with no items.');
                //if(pRet==true)
                //{
                //    frm.submit();
                //}
            } else {
                frm.submit();
            };

        });

    });
{/literal}
{/script}