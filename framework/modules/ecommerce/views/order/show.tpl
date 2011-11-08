{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

{* css unique="showorder" link="`$asset_path`css/ecom.css" corecss="tables"}

 {/css*}

{css unique="showorder" link="`$asset_path`css/ecom.css" corecss="tables"}

{/css}

<div id="order" class="module order show hide exp-skin-tabview">
    
    <div id="ordertabs" class="yui-navset">
        <ul class="yui-nav">
            <li class="selected"><a href="#invoice"><em>Receipt</em></a></li>
            <li><a href="#ordhistory"><em>Order History</em></a></li>
            <li><a href="#shipinfo"><em>Shipping Information</em></a></li>
            <li><a href="#billinfo"><em>Billing Information</em></a></li>
            {permissions}
                {if $permissions.manage == 1}
                    <li><a href="#addinfo"><em>Additional Information</em></a></li>
                    <li><a href="#notes"><em>Notes & Communications</em></a></li>
                {/if}
            {/permissions}                                                           
        </ul>   
                 
        <div class="yui-content exp-ecom-table">
            <div id="invoice">
                <div id="buttons">
                    {printer_friendly_link class="awesome `$smarty.const.BTN_SIZE` `$smarty.const.BTN_COLOR`" text="Print this invoice" view="show_printable"} 
                    {if $smarty.const.HTMLTOPDF_PATH && $smarty.const.HTMLTOPDF_PATH_TMP}                                        
                        <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link controller='order' action='getPDF' id=$order->id inum=$order->invoice_number}">Download PDF</a>
                    {/if}
                    {permissions}
                        {if $permissions.manage == 1}
                            {printer_friendly_link class="awesome `$smarty.const.BTN_SIZE` `$smarty.const.BTN_COLOR`" text="Print Packing Slip" view="show_packing"}
                            <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link controller='order' action='createReferenceOrder' id=$order->id}">Spawn Reference Order</a>
                        {/if}
                    {/permissions} 
                </div>               
                {include file="`$smarty.const.BASE`framework/modules/ecommerce/views/order/invoice.tpl"}
            </div>
            <div id="ordhistory">
                <h2>Order History</h2>                
                {permissions}
                {if $permissions.manage == 1}
                    <table class="order-info">
                        <thead>
                            <tr>
                                <!--th>The current status of this order is: {$order->getStatus()}</th-->  
                                <th>Order Type and Order Status</th>  
                            </tr> 
                        </thead>
                        <tbody>
                            <tr><td>                                
                                {form action=setStatus}
                                    {control type="hidden" name="id" value=$order->id}
                                    {control type="dropdown" name="order_type_id" label="Change order type to:" frommodel=order_type orderby='rank' value=$order->order_type_id orderby=title}
                                    {control type="dropdown" name="order_status_id" label="Change order status to:" frommodel='order_status' orderby='rank' value=$order->order_status_id}
                                    {control type="checkbox" name="email_user" label="Send email to user to notify them of status change?" value=1}
                                    {control type="checkbox" name="include_shipping_info" label="Include Shipping Information in email?" value=1}
                                    <select id="order_status_messages" name="order_status_messages" size="1">
                                        <option value="0" selected>-- Select a predefined message --</option>
                                        {foreach from=$messages item=msg}
                                            <option value="{$msg->body}">{$msg->body|truncate:80}</option>
                                        {/foreach}
                                    </select>
                                    {control id=msgbox type="textarea" name="comment" label="Comment" rows=6 cols=45}
                                    {control type="checkbox" name="save_message" label="Save this message to use in the future?" value=1}
                                    {control type=buttongroup submit="Save change"|gettext}
                                {/form}                 
                            </td></tr>
                    </table>
                {/if}
                {/permissions}
                
                <table class="order-info">
                    <thead>
                        <tr>
                            <th>Status Change History</th>  
                        </tr> 
                    </thead>
                    <tbody>     
                    {foreach from=$order->order_status_changes item=change}
                    <tr style="border-bottom: 1px solid gray;"><td>
                    <strong>
                    Status was changed from {selectvalue table='order_status' field="title" where="id=$change->from_status_id"}
                    to {selectvalue table='order_status' field="title" where="id=$change->to_status_id"} on {$change->getTimestamp()} by {$change->getPoster()}
                    </strong>
                    {if $change->comment != ''}                        
                        <div style="border: 1px solid gray; margin-left: 10px; margin-top: 5px;">
                        <h4>Comment:</h4>{$change->comment}
                        </div>
                    {/if}
                    </td></tr>
                {foreachelse}
                    <tr>
                        <td>There is no change history for this order yet.
                        </td>
                    </tr> 
                {/foreach}                                                
                </table>
            </div>
            <div id="shipinfo">
                <h2>{"Shipping and Tracking"|gettext}</h2>
                
                 <table class="order-info">
                    <thead>
                        <tr>
                            <th colspan="2">Shipping Information</th>  
                        </tr> 
                    </thead>
                    <tbody>                         
                    {if $permissions.manage == 1}
                        <tr><td colspan="2">
                        {form action=update_shipping}
                            {control type="hidden" name="id" value=$order->id}
                            {control type="text" name="shipping_tracking_number" label="Tracking #" value=$order->shipping_tracking_number}
                            {control type="datetimecontrol" name="shipped" showtime=false label="Date Shipped" value=$order->shipped}
                            {control type="buttongroup" submit="Save Shipping Info"|gettext}
                        {/form}
                        </td>
                        </tr>
                    {else}
                        <tr><td> 
                            Tracking #:</td><td>{$order->shipping_tracking_number}{br}
                        </td></tr> 
                        <tr><td> 
                            Date Shipped:</td><td>{if $order->shipped != 0}{$order->shipped|format_date:$smarty.const.DISPLAY_DATE_FORMAT}{else}This order has not been shipped yet{/if}
                        </td></tr>
                    {/if}
                 </table>
            </div>
            <div id="billinfo">
                <h2>Billing Information</h2>
                {* edebug var=$order->billingmethod[0] *}
                {foreach from=$order->billingmethod[0]->billingtransaction item=bt name=foo}
                    <table class="order-info">
                    <thead>
                        <tr>
                            <th colspan="2">Transaction state: {$bt->transaction_state}.</th>  
                        </tr> 
                    </thead>
                    <tbody>     
                    <tr>
                        <td>Ref #: {$bt->getRefNum()}
                        </td>
                    </tr> 
                    <tr>
                        <td>Amount: {currency_symbol}{$bt->billing_cost|number_format:2}
                        </td>
                    </tr>
                    {if $permissions.manage == 1}
                        <tr>
                            <td>By: {$bt->getPoster()} on {$bt->getTimestamp()}
                            </td>
                        </tr> 
                    {/if}
                    {if $permissions.manage == 1 && $smarty.foreach.foo.first}
                        <tr>
                            <td>
                            {if $bt->transaction_state == "authorized"}
                                {if $bt->captureEnabled() == true}
                                    {form action=captureAuthorization}
                                        {control type="hidden" name="id" value=$order->id}
                                        {control type="text" name="capture_amt" label="Amount to Capture" value=$order->grand_total}
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
                                {if $bt->creditEnabled() == true}
                                    {form action=creditTransaction}
                                        {control type="hidden" name="id" value=$order->id}
                                        {control type="text" name="capture_amt" label="Amount to Refund" value=$order->grand_total}
                                        {control type="buttongroup" submit="Credit "|gettext}
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
        {if $permissions.manage == 1}
            <div id="addinfo">              
                <h2>Sales Reps and Referrers</h2>
                <table border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th>
                            Sales Reps
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="odd">
                            <td>
                                {form action=update_sales_reps}
                                    {control type="hidden" name="id" value=$order->id}
                                    {control type="dropdown" name="sales_rep_1_id" label="Sales Rep 1 (Initial Order)" includeblank=true items=$sales_reps value=$order->sales_rep_1_id}
                                    {control type="dropdown" name="sales_rep_2_id" label="Sales Rep 2 (Completed Order)" includeblank=true items=$sales_reps value=$order->sales_rep_2_id}
                                    {control type="dropdown" name="sales_rep_3_id" label="Sales Rep 3 (Other)" includeblank=true items=$sales_reps value=$order->sales_rep_3_id}
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
                                Original HTTP Referrer
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="even">
                            <td>
                                {if $order->orig_referrer !=''}
                                    <p><a href="{$order->orig_referrer}" target="_blank">{$order->orig_referrer}</a></p>        {else}
                                    <p>Direct Traffic</p>
                                {/if}

                                {if $order->reference_id != 0}
                                    <h3>Invoice Reference:</h3>
                                    <p><a href="/order/show/id/{$order->reference_id}">{$order->reference_id}</a></p>
                                {/if}

                                {if $order->referencing_ids|@count > 0}
                                    <h3>Spawned Invoices Referencing This Invoice:</h3>                    
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
                            <th>Email</th>  
                        </tr> 
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                            {permissions}
                            {if $permissions.manage == 1}
                                {form action=emailCustomer}
                                    {control type="hidden" name="id" value=$order->id}
                                    <select id="order_status_messages" name="order_status_messages" size="1">
                                        <option value="0" selected>-- Select a predefined message --</option>
                                        {foreach from=$messages item=msg}
                                            <option value="{$msg->body|escape:"all"}">{$msg->body|truncate:80}</option>
                                        {/foreach}
                                    </select>
                                    {control type=text name="to_addresses" size="100" label="To (comma seperate multiple):" value="`$to_addresses`"}                                    
                                    {control type=text name="email_subject" size="100" label="Email Subject:" value="`$email_subject`"}
                                    {control id=email_message type="editor" name="email_message" height=250}
                                    {control type="checkbox" name="save_message" label="Save this message to use in the future?" value=1}
                                    {control type="checkbox" name="include_invoice" label="Attach invoice to this email?" value=1}
                                    {control type=radiogroup columns=1 name="from_address" label="Select From Address" items=$from_addresses default=$from_default flip=false}        
                                    {control type=text name="other_from_address" label="Other From Address" value=''}
                                    {control type=buttongroup submit="Email Customer"|gettext}
                                {/form}
                            {/if}
                            {/permissions}                        
                        </td>
                    </tr>
                </table>     
                <hr>
                <h2>{"Notes on this order"|gettext}</h2>
                {simplenote content_type="order" content_id=$order->id require_login="1" require_approval="0" require_notification="0" tab="notes"}
            </div>
        {/if}
    {/permissions}
        </div>
    </div>
</div>
<div class="loadingdiv">{'Loading Order'|gettext}</div>

{script unique="msgbox"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','event','yui2-tabview','yui2-element', function(Y) {
    var YAHOO=Y.YUI2;

    var selects = Y.all('#order_status_messages option');
    selects.on('click',function(e){
        EXPONENT.editoremail_message.setData(e.target.get('value'));
    });

    var tabView = new YAHOO.widget.TabView('auth');
    //Y.one('#authcfg').removeClass('hide').next().remove();
    

    var tabView2 = new YAHOO.widget.TabView('ordertabs');

    var url = location.href.split('#');
    if (url[1]) {
        //We have a hash
        var tabHash = url[1];
        var tabs = tabView.get('tabs');
        for (var i = 0; i < tabs.length; i++) {
            if (tabs[i].get('href') == '#' + tabHash) {
                tabView.set('activeIndex', i);
                break;
            }
        }
    }

    YAHOO.util.Dom.removeClass("order", 'hide');
    var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
    YAHOO.util.Dom.setStyle(loading, 'display', 'none');
	});
{/literal}
{/script}
