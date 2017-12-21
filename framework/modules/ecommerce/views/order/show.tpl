{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

{css unique="showorder" link="`$asset_path`css/ecom.css" corecss="tables"}

{/css}

<div id="order" class="module order show">
    <div id="ordertabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
            <li class="selected"><a href="#invoice"><em>{'Receipt'|gettext}</em></a></li>
            <li><a href="#ordhistory"><em>{'Order History'|gettext}</em></a></li>
            <li><a href="#shipinfo"><em>{'Shipping Information'|gettext}</em></a></li>
            <li><a href="#billinfo"><em>{'Billing Information'|gettext}</em></a></li>
            {permissions}
                {if $permissions.manage}
                    <li><a href="#addinfo"><em>{'Additional Information'|gettext}</em></a></li>
                    <li><a href="#notes"><em>{'Notes & Communications'|gettext}</em></a></li>
                {/if}
            {/permissions}
        </ul>

        <div class="yui-content exp-ecom-table">
            <div id="invoice">
                <div id="buttons">
                    {printer_friendly_link class="{button_style}" text="Print this invoice"|gettext view="show_printable" show=1}
                    {if $smarty.const.HTMLTOPDF_ENGINE != 'none'}
                        <a class="{button_style}" href="{link controller='order' action='getPDF' id=$order->id inum=$order->invoice_number ajax_action=1}">{'Download PDF'|gettext}</a>
                    {/if}
                    {permissions}
                        {if $permissions.manage}
                            <a class="{button_style}" href="{link controller='order' action='createReferenceOrder' id=$order->id}">{'Spawn Reference Order'|gettext}</a>
                        {/if}
                    {/permissions}
                </div>
                {exp_include file="invoice.tpl"}
            </div>
            <div id="ordhistory">
                <h2>{'Order History'|gettext}</h2>
                {permissions}
                {if $permissions.manage}
                    <table class="order-info">
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
                {/if}
                {/permissions}

                <table class="order-info">
                    <thead>
                        <tr>
                            <th>{'Status Change History'|gettext}</th>
                        </tr>
                    </thead>
                    <tbody>
                    {foreach from=$order->order_status_changes item=change}
                        <tr style="border-bottom: 1px solid gray;"><td>
                        <strong>
                        {'Status was changed'|gettext} {if $change->from_status_id}{'from'|gettext} {selectvalue table='order_status' field="title" where="id=`$change->from_status_id`"}{/if}
                        {'to'|gettext} {selectvalue table='order_status' field="title" where="id=`$change->to_status_id`"} {'on'|gettext} {$change->getTimestamp()} {'by'|gettext} {$change->getPoster()}
                        </strong>
                        {if $change->comment != ''}
                            <div style="border: 1px solid gray; margin-left: 10px; margin-top: 5px;">
                            <h4>{'Comment'|gettext}:</h4>{$change->comment}
                            </div>
                        {/if}
                        </td></tr>
                    {foreachelse}
                        <tr>
                            <td>{message text='There is no change history for this order yet.'|gettext}
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
            <div id="shipinfo">
                <div id="buttons">
                    {permissions}
                        {if $permissions.manage && $order->shipping_required}
                            {printer_friendly_link class="{button_style}" text="Print Packing Slip"|gettext view="show_packing" show=1}
                        {/if}
                    {/permissions}
                </div>
                <h2>{"Shipping and Tracking"|gettext}</h2>
                 <table class="order-info">
                    <thead>
                        <tr>
                            <th colspan="2">{'Shipping Information'|gettext}</th>
                        </tr>
                    </thead>
                    <tbody>
                    {if $permissions.manage}
                    {if $order->shipping_required}
                        <tr>
                            <td>
                                {if $shipping->shippingmethod->carrier != ''}
                                <strong>{"Carrier"|gettext}:</strong>{br}
                                {$shipping->shippingmethod->carrier}
                                {/if}
                            </td>
                            <td>
                                <strong>{"Shipping Method"|gettext}:</strong>{br}
                                {$shipping->shippingmethod->option_title}
                                {permissions}
                                    <div class="item-permissions">
                                        {if $permissions.edit_shipping_method && !$pf}
                                            {icon class="edit" action=edit_shipping_method id=$order->id title='Edit Shipping Method'|gettext}
                                        {/if}
                                    </div>
                                {/permissions}
                            </td>
                        </tr>
                        {/if}
                        <tr><td>
                            {if !$order->shipping_required}
                                {'No Shipping Required'|gettext}
                            {else}
                                {form action=update_shipping}
                                    {control type="hidden" name="id" value=$order->id}
                                    {control type="text" name="shipping_tracking_number" label="Tracking #"|gettext value=$order->shipping_tracking_number}
                                    {control type="datetimecontrol" name="shipped" showtime=false label="Date Shipped"|gettext value=$order->shipped}
                                    {control type="buttongroup" submit="Update Shipping Info"|gettext}
                                {/form}
                            {/if}
                            </td>
                            <td>
                                {if $order->shipping_required}
                                    <h4>{'Packages'|gettext}</h4>
                                    <ol>
                                        {foreach $order->shippingmethods as $sm}
                                            {$sm->attachCalculator()}
                                            <li>
                                                <div>
                                                    {$sm->carrier} - {$sm->option_title}
                                                    {if empty($sm->shipping_options.shipment_status) || $sm->shipping_options.shipment_status == 'cancelled'}
                                                        {if $sm->shipping_options.shipment_status == 'cancelled'}{$text='Re-Create Package'|gettext}{else}{$text='Create Package'|gettext}{/if}
                                                        {icon class=add action=edit_parcel id=$sm->id text=$text title='Enter details about the items in the package'|gettext}
                                                    {elseif $sm->shipping_options.shipment_status == 'created' && $sm->calculator->labelsEnabled()}
                                                        {icon class=edit action=edit_label id=$sm->id text='Purchase Label'|gettext}
                                                    {elseif $sm->shipping_options.shipment_status == 'purchased' && $sm->calculator->labelsEnabled()}
                                                        {icon class=downloadfile action=download_label id=$sm->id text='Print Label'|gettext}
                                                        {icon class=delete action=delete_label id=$sm->id text='Cancel Label'|gettext}
                                                        {'Tracking'|gettext}: {$sm->shipping_options.shipment_tracking_number}
                                                        {if $sm->calculator->pickupEnabled() && $sm->carrier != 'USPS'}
                                                            {if ($sm->shipping_options.pickup_status != 'purchased')} {* FIXME *}
                                                                {icon class=add action=edit_pickup id=$sm->id text='Request Pickup'|gettext}
                                                            {elseif $sm->shipping_options.pickup_status == 'purchased'}
                                                                {icon class=delete action=delete_pickup id=$sm->id text='Cancel Pickup'|gettext}
                                                            {/if}
                                                        {/if}
                                                    {/if}
                                                    {if $sm->calculator != null}
                                                        {$msg = $sm->calculator->getPackageDetails($sm)}
                                                        {if $msg}
                                                            {pop id="pkg_details`$sm->id`" text="Package Details"|gettext title="Package Details"|gettext buttons="Close"|gettext}
                                                                {$msg}
                                                            {/pop}
                                                        {/if}
                                                    {/if}
                                                </div>
                                            </li>
                                        {/foreach}
                                    </ol>
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
                    </tbody>
                 </table>
            </div>
            <div id="billinfo">
                <h2>{'Billing Information'|gettext}</h2>
                {* edebug var=$order->billingmethod[0] *}
                {foreach from=$order->billingmethod[0]->billingtransaction item=bt name=foo}
                    <table class="order-info">
                    <thead>
                        <tr>
                            <th colspan="2">{'Transaction state:'|gettext} {$bt->transaction_state}</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{'Ref #:'|gettext} {if $billing->calculator != null}{$bt->getRefNum()}{/if}
                        </td>
                    </tr>
                    <tr>
                        <td>{'Amount:'|gettext} {$bt->billing_cost|currency}
                        </td>
                    </tr>
                    {if $permissions.manage}
                        <tr>
                            <td>{'By:'|gettext} {$bt->getPoster()} {'on'|gettext} {$bt->getTimestamp()}
                            </td>
                        </tr>
                    {/if}
                    {if $permissions.manage && $smarty.foreach.foo.first}
                        <tr>
                            <td>
                            {* fixme this is where we'd do reAuthorize() or authorize() *}
                            {if $bt->transaction_state == "authorized" || ($bt->billing_options->pending_reason == "authorization" && $bt->transaction_state == "error")}
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
                            {if $bt->transaction_state == "complete" || $bt->transaction_state == "paid"}
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
                    {/if}
                    </tbody>
                    </table>
                {/foreach}
            </div>
    {permissions}
        {if $permissions.manage}
            <div id="addinfo">
                <h2>{'Sales Reps and Referrers'|gettext}</h2>
                <table border="0" cellspacing="0" cellpadding="0">
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
                <table border="0" cellspacing="0" cellpadding="0">
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
            <div id="notes">
                <h2>{"Email the Customer about this order"|gettext}</h2>
                <table class="order-info">
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
                                    {control id=email_message type="editor" name="email_message" label="or enter a Message"|gettext height=250}
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
                {simplenote content_type="order" content_id=$order->id require_login="1" require_approval="0" require_notification="0" tab="notes" title="Notes on this order"|gettext}
            </div>
        {/if}
    {/permissions}
        </div>
    </div>
    {*<div class="loadingdiv">{'Loading Order'|gettext}</div>*}
    {loading title='Loading Order'|gettext}
</div>

{script unique="msgbox" yui3mods="exptabs"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.expTabs({srcNode: '#ordertabs'});
        Y.one('#ordertabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
