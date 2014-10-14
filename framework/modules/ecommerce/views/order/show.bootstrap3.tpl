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

{css unique="showorder" link="`$asset_path`css/ecom-bs3.css" corecss="tables"}

{/css}

<div class="row order-status">
    <div class="col-lg-12">
        <div class="panel order-stats">
            <div class="row">
                <!-- this order stats header -->
                <div class="col-xs-6 col-sm-3 box-stats color3">
                    <div class="kpi-content">
                        <i class="fa fa-calendar-o"></i>
                        <span class="title">{'Date'|gettext}</span>
                        <span class="value">{$order->purchased|format_date:"%b %e, %Y"}</span>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-3 box-stats color4">
                    <div class="kpi-content">
                        <i class="fa fa-money"></i>
                        <span class="title">{'Total'|gettext}</span>
                        <span class="value">{$order->grand_total|currency}</span>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-3 box-stats color2">
                    <a href="#start_messages">
                        <div class="kpi-content">
                            <i class="fa fa-comments"></i>
                            <span class="title">{'Messages'|gettext}</span>
                            <span class="value">{expSimpleNote::noteCount($order->id, "order")}</span>
                        </div>
                    </a>
                </div>
                <div class="col-xs-6 col-sm-3 box-stats color1">
                    <a href="#start_products">
                        <div class="kpi-content">
                            <i class="fa fa-book"></i>
                            <span class="title">{'Products'|gettext}</span>
                            <span class="value">{count($order->orderitem)}</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- main content column -->
            <div class="col-lg-7">
                <div class="panel">
                    <div class="panel-heading">
                        <!-- invoice # browser -->
                        <i class="fa fa-credit-card"></i>
                        {'Order'|gettext} <span class="badge">#{$order->invoice_id}</span>
                        {permissions}
                            <div class="item-permissions">
                                {if $permissions.edit_invoice_id && !$pf}
                                    {icon class="edit" action=edit_invoice_id id=$order->id title='Edit Invoice Number'|gettext}
                                {/if}
                            </div>
                        {/permissions}
                        <div class="panel-heading-action">
                            <div class="btn-group">
                                <a class="btn btn-default" href="{link action=show invoice=$order->invoice_id -1}" title="{'Previous Invoice'|gettext}">
                                    <i class="fa fa-backward"></i>
                                </a>
                                <a class="btn btn-default" href="{link action=showall}" title="{'All Invoices'|gettext}">
                                    <i class="fa fa-eject"></i>
                                </a>
                                <a class="btn btn-default" href="{link action=show invoice=$order->invoice_id +1}" title="{'Next Invoice'|gettext}">
                                    <i class="fa fa-forward"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- print buttons -->
                    <div class="well well-sm">
                        {printer_friendly_link class="{button_style}" text="Print invoice"|gettext view="show_printable" show=1}
                        {if $smarty.const.HTMLTOPDF_ENGINE != 'none'}
                            <a class="{button_style}" href="{link controller='order' action='getPDF' id=$order->id inum=$order->invoice_number ajax_action=1}">{'Download PDF'|gettext}</a>
                        {/if}
                        {permissions}
                            {if $permissions.manage}
                                {printer_friendly_link class="{button_style}" text="Packing Slip"|gettext view="show_packing" show=1}
                                <a class="{button_style}" href="{link controller='order' action='createReferenceOrder' id=$order->id}">{'Spawn Order'|gettext}</a>
                            {/if}
                        {/permissions}
                    </div>

                    <!-- order status tabs -->
                    <ul id="tabOrder" class="nav nav-tabs">
                        <li class="active">
                            <a href="#status" data-toggle="tab">
                                <i class="fa fa-clock-o"></i>
                                {'Status'|gettext}
                                <span class="badge">
                                    {count($order->order_status_changes)}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#other" data-toggle="tab">
                                <i class="fa fa-users"></i>
                                {'Sales Reps and Referrers'|gettext}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content panel">
                        <div id="status" class="tab-pane fade in active">
                            <div class="table-responsive">
                                <table class="table order-info">
                                    <tbody>
                                    {foreach from=$order->order_status_changes item=change}
                                        <tr style="border-bottom: 1px solid gray;">
                                            <td>
                                                {selectvalue table='order_status' field="title" where="id=`$change->to_status_id`"}                                            </td>
                                            <td>
                                                {$change->getTimestamp()}
                                            </td>
                                            <td>
                                                {$change->getPoster()}
                                            </td>
                                        </tr>
                                    {foreachelse}
                                        <tr>
                                            <td colspan="3">{message text='There is no change history for this order yet.'|gettext}
                                            </td>
                                        </tr>
                                    {/foreach}
                                </table>
                            </div>
                            {permissions}
                            {if $permissions.manage}
                                <div class="table-responsive">
                                    <table class="table order-info">
                                        <thead>
                                            <tr>
                                                <!--th>The current status of this order is: {$order->getStatus()}</th-->
                                                <th>{'Order Type and Order Status'|gettext}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>
                                                {form action=setStatus}
                                                    {control type="hidden" name="id" value=$order->id}
                                                    {control type="dropdown" name="order_type_id" label="Change order type to:"|gettext frommodel=order_type orderby='rank' value=$order->order_type_id orderby=title}
                                                    {control type="dropdown" name="order_status_id" label="Change order status to:"|gettext frommodel='order_status' orderby='rank' value=$order->order_status_id}
                                                    {control type="checkbox" name="email_user" label="Send email to user to notify them of status change?"|gettext value=1}
                                                    {control type="checkbox" name="include_shipping_info" label="Include Shipping Information in email?"|gettext value=1}
                                                    <select class="form-control" id="order_status_messages" name="order_status_messages" size="1">
                                                        <option value="0" selected>{'-- Select a predefined comment --'|gettext}</option>
                                                        {foreach from=$messages item=msg}
                                                            <option value="{$msg->body}">{$msg->body|truncate:80}</option>
                                                        {/foreach}
                                                    </select>
                                                    {control id=msgbox type="textarea" name="comment" label="or enter a Comment"|gettext rows=6 cols=45}
                                                    {control type="checkbox" name="save_message" label="Save this message to use in the future?"|gettext value=1}
                                                    {control type=buttongroup submit="Save change"|gettext}
                                                {/form}
                                            </td></tr>
                                    </table>
                                </div>
                            {/if}
                            {/permissions}
                        </div>
                        <div id="other" class="tab-pane fade">
                            <div class="table-reponsive">
                                <table class="table" border="0" cellspacing="0" cellpadding="0">
                                    <thead>
                                        <tr>
                                            <th>
                                            {'Sales Reps'|gettext}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="odd">
                                            <td>
                                                {form action=update_sales_reps}
                                                    {control type="hidden" name="id" value=$order->id}
                                                    {control type="dropdown" name="sales_rep_1_id" label="Sales Rep 1 (Initial Order)"|gettext includeblank=true items=$sales_reps value=$order->sales_rep_1_id}
                                                    {control type="dropdown" name="sales_rep_2_id" label="Sales Rep 2 (Completed Order)"|gettext includeblank=true items=$sales_reps value=$order->sales_rep_2_id}
                                                    {control type="dropdown" name="sales_rep_3_id" label="Sales Rep 3 (Other)"|gettext includeblank=true items=$sales_reps value=$order->sales_rep_3_id}
                                                    {control type="buttongroup" submit="Update Sales Reps"|gettext}
                                                {/form}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-reponsive">
                                <table class="table" border="0" cellspacing="0" cellpadding="0">
                                    <thead>
                                        <tr>
                                            <th>
                                                {'Original HTTP Referrer'|gettext}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="even">
                                            <td>
                                                {if $order->orig_referrer !=''}
                                                    <p><a href="{$order->orig_referrer}" target="_blank">{$order->orig_referrer}</a></p>        {else}
                                                    <p>{'Direct Traffic'|gettext}</p>
                                                {/if}

                                                {if $order->reference_id != 0}
                                                    <h3>{'Invoice Reference:'|gettext}</h3>
                                                    <p><a href="/order/show/id/{$order->reference_id}">{$order->reference_id}</a></p>
                                                {/if}

                                                {if $order->referencing_ids|@count > 0}
                                                    <h3>{'Spawned Invoices Referencing This Invoice:'|gettext}</h3>
                                                    {foreach from=$order->referencing_ids item=ref_id}
                                                        <p><a href="/order/show/id/{$ref_id}">{$ref_id}</a></p>
                                                    {/foreach}
                                                {/if}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- shipping status tabs -->
                    <ul id="myTab" class="nav nav-tabs">
                        <li class="active">
                            <a href="#shipping" data-toggle="tab">
                                <i class="fa fa-truck"></i>
                                {'Shipping'|gettext}
                            </a>
                        </li>
                        <li>
                            <a href="#tracking" data-toggle="tab">
                                <i class="fa fa-ticket"></i>
                                {'Tracking'|gettext}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content panel">
                        <div id="shipping" class="tab-pane fade in active">
                            <div class="table-responsive">
                                <table class="table" style="width: 100%; border: 0px; text-align: left; padding: 0px; margin:0px;">
                                    <tr style="border: 0px; padding: 0px; margin:0px;">
                                        <td style="border: 0px; text-align: left; padding: 0px; margin:0px;">
                                            <strong>{"Shipping Method"|gettext}</strong>{br}
                                            {$shipping->shippingmethod->option_title}
                                            {permissions}
                                                <div class="item-permissions">
                                                    {if $permissions.edit_shipping_method && !$pf}
                                                        {icon class="edit" action=edit_shipping_method id=$order->id title='Edit Shipping Method'|gettext}
                                                    {/if}
                                                </div>
                                            {/permissions}
                                        </td>
                                        <td style="border: 0px; text-align: left; padding: 0px; padding-right: 5px; margin:0px;">
                                            {if $shipping->shippingmethod->carrier != ''}
                                            <strong>{"Carrier"|gettext}:</strong>{br}
                                            {$shipping->shippingmethod->carrier}
                                            {/if}
                                        </td>
                                    </tr>
                                </table>
                                {if $order->shipped}
                                    {if !$order->shipping_required}
                                        {'No Shipping Required'|gettext}
                                    {else}
                                        {$order->shipped|format_date:"%A, %B %e, %Y":"Not Shipped Yet"}
                                    {/if}
                                {else}
                                    {"Not Shipped Yet"|gettext}
                                {/if}
                            </div>
                        </div>
                        <div id="tracking" class="tab-pane fade">
                            <div class="table-responsive">
                                 <table class="table order-info">
                                    <thead>
                                        <tr>
                                            <th colspan="2">{'Shipping Information'|gettext}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    {if $permissions.manage}
                                        <tr><td colspan="2">
                                        {if !$order->shipping_required}
                                            {'No Shipping Required'|gettext}
                                        {else}
                                            {form action=update_shipping}
                                                {control type="hidden" name="id" value=$order->id}
                                                {control type="text" name="shipping_tracking_number" label="Tracking #"|gettext value=$order->shipping_tracking_number}
                                                {control type="datetimecontrol" name="shipped" showtime=false label="Date Shipped"|gettext value=$order->shipped}
                                                {control type="buttongroup" submit="Save Shipping Info"|gettext}
                                            {/form}
                                        {/if}
                                        </td>
                                        </tr>
                                    {else}
                                        <tr><td>
                                            {'Tracking #'|gettext}:</td><td>{$order->shipping_tracking_number}{br}
                                        </td></tr>
                                        <tr><td>
                                            {'Date Shipped'|gettext}:</td><td>
                                            {if $order->shipped != 0}
                                                {$order->shipped|format_date}
                                            {else}
                                                {'This order has not been shipped yet'|gettext}
                                            {/if}
                                        </td></tr>
                                    {/if}
                                 </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <!-- payment status -->
                    <div class="panel-heading">
                        <i class="fa fa-money"></i>
                        {'Payment'|gettext}
                        <span class="badge">
                            {count($order->billingmethod[0]->billingtransaction)}
                        </span>
                    </div>
                    <div>
                        <div class="odd">
                            <span class="pmt-label">
                                {"Payment Method"|gettext}
                            </span>
                            <span class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getPaymentMethod($billing->billingmethod)}
                                {else}
                                    {'No Cost'|gettext}
                                {/if}
                            </span>
                        </div>
                        <div class="even">
                            <span class="pmt-label">
                                {"Payment Status"|gettext}
                            </span>
                            <span class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getPaymentStatus($billing->billingmethod)}
                                {else}
                                    {'complete'|gettext}
                                {/if}
                            </span>
                        </div>
                        <div class="odd">
                            <span class="pmt-label">
                                {"Payment Authorization #"|gettext}
                            </span>
                            <span class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getPaymentAuthorizationNumber($billing->billingmethod)}
                                {/if}
                            </span>
                        </div>
                        <div class="even">
                            <span class="pmt-label">
                                {"Payment Reference #"|gettext}
                            </span>
                            <span class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getPaymentReferenceNumber($billing->billingmethod->billing_options)}
                                {/if}
                            </span>
                        </div>
                        {if $billing->calculator != null}
                        {$data = $billing->calculator->getAVSAddressVerified($billing->billingmethod)|cat:$billing->calculator->getAVSZipVerified($billing->billingmethod)|cat:$billing->calculator->getCVVMatched($billing->billingmethod)|cat:$billing->calculator->getCVVMatched($billing->billingmethod)}
                        {if  !empty($data)}
                        <div class="odd">
                            <span class="pmt-label">
                                {"AVS Address Verified"|gettext}
                            </span>
                            <span class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getAVSAddressVerified($billing->billingmethod)}
                                {/if}
                            </span>
                        </div>
                        <div class="even">
                            <span class="pmt-label">
                                {"AVS ZIP Verified"|gettext}
                            </span>
                            <span class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getAVSZipVerified($billing->billingmethod)}
                                {/if}
                            </span>
                        </div>
                        <div class="odd">
                            <span class="pmt-label">
                                {"CVV # Matched"|gettext}
                            </span>
                            <span class="pmt-value">
                                {if $billing->calculator != null}
                                    {$billing->calculator->getCVVMatched($billing->billingmethod)}
                                {/if}
                            </span>
                        </div>
                        {/if}
                        {/if}
                        {permissions}
                            {if $permissions.edit_shipping_method && !$pf}
                                <div class="item-permissions">
                                    {icon class="edit" action=edit_payment_info id=$order->id title='Edit Payment Method'|gettext}
                                </div>
                            {/if}
                        {/permissions}
                    </div>
                    <h4>{'Billing Information'|gettext}</h4>
                    <div class="table-responsive">
                        <table class="table order-info">
                            <tbody>
                            {foreach from=$order->billingmethod[0]->billingtransaction item=bt name=foo}
                                <tr style="border-bottom: 1px solid gray;">
                                    <td>
                                        {$bt->transaction_state}
                                    </td>
                                    <td>
                                        {$bt->billing_cost|currency}
                                    </td>
                                    <td>
                                        {$bt->getTimestamp()}
                                    </td>
                                    <td>
                                        {$bt->getPoster()}
                                    </td>
                                    <td>
                                        {if $bt->transaction_state == "authorized"}
                                            {if $bt->captureEnabled() == true}
                                                {form action=captureAuthorization}
                                                    {control type="hidden" name="id" value=$order->id}
                                                    {control type="text" name="capture_amt" label="Amount to Capture"|gettext value=$order->grand_total}
                                                    {control type="buttongroup" submit="Capture Transaction"|gettext}
                                                {/form}
                                            {/if}
                                            {if $bt->voidEnabled() == true}
                                                {form action=voidAuthorization}
                                                    {control type="hidden" name="id" value=$order->id}
                                                    {control type="buttongroup" submit="Void Authorization"|gettext}
                                                {/form}
                                            {/if}
                                        {/if}
                                        {if $bt->transaction_state == "complete"}
                                            {if $billing->calculator != null && $bt->creditEnabled() == true}
                                                {form action=creditTransaction}
                                                    {control type="hidden" name="id" value=$order->id}
                                                    {control type="text" name="capture_amt" label="Amount to Refund"|gettext value=$order->grand_total}
                                                    {control type="buttongroup" submit="Credit"|gettext}
                                                {/form}
                                            {/if}
                                        {/if}
                                    </td>
                                </tr>
                            {foreachelse}
                                <tr>
                                    <td colspan="5">{message text='There is no payment history for this order yet.'|gettext}
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2nd column -->
            <div class="col-lg-5">
                <!-- customer information -->
                {$customer = $order_user->customerInfo()}
                <div class="panel">
                    <div class="panel-heading">
                        <i class="fa fa-user"></i>
                        {'Customer'|gettext}
                        <span class="badge">
                            <a href={link controller=users action=viewuser id=$order_user->id} title="{'View Customer'|gettext}" >{$order_user->id|username:'system'}</a>
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <dl class="well well-sm list-detail">
                                <dt>
                                    {'Email'|gettext}
                                </dt>
                                <dd>
                                    <a href="mailto:{$order_user->email}" title="{'Email customer'|gettext}"><i class="fa fa-envelope-o"></i> {$order_user->email}</a>
                                </dd>
                                <dt>
                                    {'Account Registered'|gettext}
                                </dt>
                                <dd class="text-muted">
                                    <i class="fa fa-calendar-o"></i> {$order_user->created_on|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}
                                </dd>
                                <dt>
                                    {'Last Visit'|gettext}
                                </dt>
                                <dd class="text-muted">
                                    <i class="fa fa-calendar-o"></i> {$order_user->last_login|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}
                                </dd>
                                <dt>
                                    {'Valid Orders Placed'|gettext}
                                </dt>
                                <dd>
                                    <span class="badge alert-info">
                                        {$customer->total_orders}
                                    </span>
                                </dd>
                                <dt>
                                    {'Total Spent since registration'|gettext}
                                </dt>
                                <dd>
                                    <span class="badge alert-success">
                                        {$customer->total_spent|currency}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="row">
                        <ul id="tabAddresses" class="nav nav-tabs">
                            <li class="active">
                                <a href="#addressShipping" data-toggle="tab">
                                    <i class="fa fa-truck"></i>
                                    {'Shipping Address'|gettext}
                                </a>
                            </li>
                            <li>
                                <a href="#addressInvoice" data-toggle="tab">
                                    <i class="fa fa-file-text"></i>
                                    {'Invoice Address'|gettext}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content panel">
                            <div id="addressShipping" class="tab-pane fade in active">
                                <div class="well well-sm">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <!-- shipping address -->
                                            {$shipping->shippingmethod->addresses_id|address}
                                            {permissions}
                                                <div class="item-permissions">
                                                    {if $permissions.edit_address && !$pf}
                                                        {icon class="edit" action=edit_address id=$order->id type='s' title='Edit Shipping Address'|gettext}
                                                    {/if}
                                                </div>
                                            {/permissions}
                                        </div>
                                        <div class="col-sm-6">
                                            {google_map unique="shipping" address=$shipping->shippingmethod->addresses_id}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="addressInvoice" class="tab-pane fade">
                                <div class="well well-sm">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <!-- billing address -->
                                            {$order->billingmethod[0]->addresses_id|address}
                                            {permissions}
                                                <div class="item-permissions">
                                                    {if $permissions.edit_address && !$pf}
                                                        {icon class="edit" action=edit_address id=$order->id type='b' title='Edit Billing Address'|gettext}
                                                    {/if}
                                                </div>
                                            {/permissions}
                                        </div>
                                        <div class="col-sm-6">
                                            {google_map unique="invoice" address=$order->billingmethod[0]->addresses_id}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                <!-- messages/notes -->
                    <div id="start_messages" class="panel-heading">
                        <i class="fa fa-envelope"></i> {'Messages'|gettext}
                        <span class="badge">
                            {expSimpleNote::noteCount($order->id, "order")}
                        </span>
                        {$unapproved = expSimpleNote::noteCount($order->id, "order", true)}
                        {if $unapproved}
                            <span class="badge alert-danger">
                                <a href="{link controller=expSimpleNote action=manage content_type="order" content_id=$order->id tab=1}" title="{'Manage Unapproved Messages'|gettext}">{$unapproved}</a>
                            </span>
                        {/if}
                    </div>
                    <div class="well">
                        <h4>{"Email the Customer about this order"|gettext}</h4>
                        <div class="table-responsive">
                            <table class="table order-info">
                                <thead>
                                    <tr>
                                        <th>{'Email'|gettext}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                        {permissions}
                                        {if $permissions.manage}
                                            {form action=emailCustomer}
                                                {control type="hidden" name="id" value=$order->id}
                                                {control type=text name="to_addresses" size="100" label="To (comma separate multiple):"|gettext value="`$to_addresses`"}
                                                {control type=text name="email_subject" size="100" label="Email Subject:"|gettext value="`$email_subject`"}
                                                {br}
                                                <select class="form-control" id="order_status_messages" name="order_status_messages" size="1">
                                                    <option value="0" selected>{'-- Select a predefined message --'|gettext}</option>
                                                    {foreach from=$messages item=msg}
                                                        <option value="{$msg->body|escape:"all"}">{$msg->body|truncate:80}</option>
                                                    {/foreach}
                                                </select>
                                                {control id=email_message type="editor" name="email_message" label="or enter a Message"|gettext height=250 toolbar=basic tb_collapsed=1}
                                                {control type="checkbox" name="save_message" label="Save this message to use in the future?"|gettext value=1}
                                                {control type="checkbox" name="include_invoice" label="Attach invoice to this email?"|gettext value=1}
                                                {control type=radiogroup columns=1 name="from_address" label="Select From Address"|gettext items=$from_addresses default=$from_default flip=false}
                                                {control type=text name="other_from_address" label="Other From Address"|gettext value=''}
                                                {control type=buttongroup submit="Email Customer"|gettext}
                                            {/form}
                                        {/if}
                                        {/permissions}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        {simplenote content_type="order" content_id=$order->id require_login="1" require_approval="0" require_notification="0" tab="notes" title="Notes on this order"|gettext}
                    </div>
                </div>
            </div>
        </div>
        <div id="start_products" class="row">
            <!-- invoice/totals -->
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <i class="fa fa-shopping-cart"></i>
                        {'Products'|gettext}
                        <span class="badge">
                            {count($order->orderitem)}
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table order-items" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>
                                    </th>
                                    <th>
                                        {"Product"|gettext}
                                    </th>
                                    <th>
                                        {"SKU"|gettext}
                                    </th>
                                    <th>
                                        {"Location"|gettext}
                                    </th>
                                    <th>
                                        {"Status"|gettext}
                                    </th>
                                    <th style="text-align:right;">
                                        {"Price"|gettext}
                                    </th>
                                    <th>
                                        {"QTY"|gettext}
                                    </th>
                                    <th style="text-align:right;">
                                        {"Amount"|gettext}
                                    </th>
                                    {permissions}
                                        <div class="item-permissions">
                                            {if $permissions.edit_order_item && !$pf}
                                                <th style="text-align:right;"></th>
                                            {/if}
                                        </div>
                                   {/permissions}
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$order->orderitem item=oi}
                                <tr class="{cycle values="odd, even"}">
                                    <td>
                                        {prod_images record=$oi->product display='single' width=48}
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
                                    <td>
                                        {if $oi->products_model != ""}{$oi->products_model}{else}N/A{/if}
                                    </td>
                                    <td>
                                        {$oi->products_warehouse_location}
                                    </td>
                                    <td>
                                        {$oi->products_status}
                                    </td>
                                    <td style="text-align:right;">
                                        {$oi->products_price|currency}
                                    </td>
                                    <td>
                                        {$oi->quantity}
                                    </td>
                                    <td style="text-align:right;">
                                        {$oi->getTotal()|currency}
                                    </td>
                                    {permissions}
                                        <div class="item-permissions">
                                            {if $permissions.edit_order_item && !$pf}
                                                <td style="text-align:right;">
                                                    {icon class="edit" action=edit_order_item id=$oi->id orderid=$order->id title='Edit Invoice Item'|gettext}&#160;
                                                    {icon class="delete" action=delete_order_item id=$oi->id orderid=$order->id onclick="return confirm('Are you sure you want to delete this item from this order?')" title='Delete Invoice Item'|gettext}
                                                </td>
                                            {/if}
                                        </div>
                                    {/permissions}
                                </tr>
                            {/foreach}
                             {permissions}
                                <div class="item-permissions">
                                {if $permissions.add_order_item && !$pf}
                                    <tr>
                                        <td colspan="8"><!--a href="{link action=add_order_item id=$order->id}">[+]</a-->
                                        {capture assign="callbacks"}
                                        {literal}

                                        // the text box for the title
                                        var tagInput = Y.one('#add_new_item');

                                        // the UL to append to
                                        var tagUL = Y.one('#new_items');

                                        // the Add Link
                                        var tagAddToList = Y.one('#addToRelProdList');


                                        var onRequestData = function( oSelf , sQuery , oRequest) {
                                            tagInput.setStyles({'border':'1px solid green','background':'#fff url('+EXPONENT.PATH_RELATIVE+'framework/core/forms/controls/assets/autocomplete/loader.gif) no-repeat 100% 50%'});
                                        }

                                        var onRGetDataBack = function( oSelf , sQuery , oRequest) {
                                            tagInput.setStyles({'border':'1px solid #000','backgroundImage':'none'});
                                        }

                                        var appendToList = function(e,args) {
                                            tagUL.appendChild(createHTML(args[2]));
                                            return true;
                                        }

                                        var removeLI = function(e) {
                                            var t = e.target;
                                            if (t.test('a')) tagUL.removeChild(t.get('parentNode'));
                                        }

                                        var createHTML = function(val) {
                                            var f = '<form role="form" id=addItem method=post>';
                                                f += '<input type=hidden name=orderid id=orderid value={/literal}{$order->id}{literal}>';
                                                f += '<input type=hidden name=module id=module value=order>';
                                                f += '<input type=hidden name=action id=action value=add_order_item>';
                                                f += '<input type=hidden name=product_id id=product_id value=' + val.id + '>';
                                                f += '<input type=submit class="add {/literal}{expTheme::buttonStyle()}{literal}" name=submit value="Add This Item">';
                                                f += '</form>';
                                            var newLI = Y.Node.create(f);
                                            return newLI;
                                        }

                                        //tagAddToList.on('click',appendToList);
                                        tagUL.on('click',removeLI);

                                        // makes formatResult work mo betta
                                        oAC.resultTypeList = false;

                                        //AC.useShadow = true;
                                        //oAC.autoHighlight  = true;
                                        //oAC.typeAhead = true;

                                        oAC.maxResultsDisplayed   = 30;

                                        // when we start typing...?
                                        oAC.dataRequestEvent.subscribe(onRequestData);
                                        oAC.dataReturnEvent.subscribe(onRGetDataBack);

                                        // format the results coming back in from the query
                                        oAC.formatResult = function(oResultData, sQuery, sResultMatch) {
                                            return '(' + oResultData.model + ') ' + oResultData.title;
                                        }

                                        // what should happen when the user selects an item?
                                        oAC.itemSelectEvent.subscribe(appendToList);

                                        {/literal}
                                        {/capture}
                                        {control type="autocomplete" controller="store" action="search" name="add_new_item" label="Add a new item"|gettext value="Search title or SKU to add an item" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" jsinject=$callbacks}
                                        <div id="new_items">
                                        </div>
                                        </td>
                                    </tr>
                                {/if}
                                </div>
                             {/permissions}
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-xs-6">
                            warning
                        </div>

                        <div class="col-xs-6">
                            <div class="panel panel-total">
                                <div class="table-responsive">
                                    <table class="table totals-info" border="0" cellspacing="0" cellpadding="0">
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
                                                <div class="item-permissions">
                                                    {if $permissions.edit_totals && !$pf}
                                                        <tr class="{cycle values="odd, even"}">
                                                            <td style="text-align:right; border-left:0px;" colspan='3'>
                                                                {icon class="edit" action=edit_totals orderid=$order->id title='Edit Totals'|gettext}
                                                            </td>
                                                        </tr>
                                                    {/if}
                                                </div>
                                            {/permissions}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}

{/literal}
{/script}
