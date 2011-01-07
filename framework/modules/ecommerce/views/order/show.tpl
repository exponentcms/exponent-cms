{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
<div id="order" class="module order show hide exp-skin-tabview">
    {script unique="order" yuimodules="tabview, element"}
    {literal}
        var tabView = new YAHOO.widget.TabView('ordertabs');
        
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
    {/literal}
    {/script}
    
    <div id="ordertabs" class="yui-navset">
        <ul class="yui-nav">
            <li class="selected"><a href="#invoice"><em>Invoice</em></a></li>
            <li><a href="#ordhistory"><em>Order History</em></a></li>
            <li><a href="#shipinfo"><em>Shipping Information</em></a></li>
            <li><a href="#billinfo"><em>Billing Information</em></a></li>
            {permissions level=$smarty.const.UILEVEL_NORMAL}
                {if $permissions.manage == 1}
                    <li><a href="#addinfo"><em>Additional Information</em></a></li>
                    <li><a href="#notes"><em>Notes</em></a></li>
                {/if}
            {/permissions}
        </ul>   
                 
        <div class="yui-content">
            <div id="invoice">
                <div id="buttons">
                    {printer_friendly_link class="exp-ecom-link" text="<strong><em>Print this invoice</em></strong>" view="show_printable"} 
                    {permissions level=$smarty.const.UILEVEL_NORMAL}
                        {if $permissions.manage == 1}
                            {printer_friendly_link class="exp-ecom-link" text="<strong><em>Print Packing Slip</em></strong>" view="show_packing"}
                        {/if}
                    {/permissions} 
                </div>
                {include file="invoice.tpl"}
            </div>
            <div id="ordhistory">
                <h2>Order History</h2>
                <h3>The status of this order is: {$order->getStatus()}</h3>
                {permissions level=$smarty.const.UILEVEL_NORMAL}
                {if $permissions.manage == 1}
                    {form action=setStatus}
                        {control type="hidden" name="id" value=$order->id}
                        {control type="dropdown" name="order_status_id" label="Change order status to:" frommodel='order_status' orderby='rank' value=$order->order_status_id}
                        {control type="checkbox" name="email_user" label="Send email to user to notify them of status change?" value=1}
                        {control type="checkbox" name="include_shipping_info" label="Include Shipping Information in email?" value=1}
                        <select id="order_status_messages" name="order_status_messages" size="1" onchange="populate_msgbox();">
                            <option value="0" selected>-- Select a predefined message --</option>
                            {foreach from=$messages item=msg}
                                <option value="{$msg->body}">{$msg->body|truncate:80}</option>
                            {/foreach}
                        </select>
                        {control id=msgbox type="textarea" name="comment" label="Comment" rows=6 cols=45}
                        {control type="checkbox" name="save_message" label="Save this message to use in the future?" value=1}
                        {control type=buttongroup submit="Save change"}
                    {/form}
                {/if}
                {/permissions}
                
                <h3>History</h3>
                {foreach from=$order->order_status_changes item=change}
                    <strong>
                    Status was changed from {selectValue table='order_status' field="title" where="id=`$change->from_status_id`"} 
                    to {selectValue table='order_status' field="title" where="id=`$change->to_status_id`"} on {$change->created_at|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}
                    </strong>
                    <h4>Comments</h4>
                    {$change->comment}{br}{br}
                {foreachelse}
                    There is no change history for this order yet.
                {/foreach}

            </div>
            <div id="shipinfo">
                <h2>Shipping Information</h2>
                {if $permissions.manage == 1}
                    {form action=update_shipping}
                        {control type="hidden" name="id" value=$order->id}
                        {control type="text" name="shipping_tracking_number" label="Tracking #" value=$order->shipping_tracking_number}
                        {control type="datetimecontrol" name="shipped" showtime=false label="Date Shipped" value=$order->shipped}
                        {control type="buttongroup" submit="Save Shipping Info"}
                    {/form}
                {else}
                    Tracking #: {$order->shipping_tracking_number}{br}
                    Date Shipped: {if $order->shipped != 0}{$order->shipped|format_date:$smarty.const.DISPLAY_DATE_FORMAT}{else}This order has not been shipped yet{/if}
                {/if}
            </div>
            <div id="billinfo">
                <h2>Billing Information</h2>
                {* eDebug var=$order->billingmethod[0] *}
                {foreach from=$order->billingmethod[0]->billingtransaction item=bt name=foo}
                    <table class="order-info">
                    <thead>
                        <tr>
                        <th colspan="2">Transaction state: {$bt->transaction_state}.  Ref #: {$bt->getRefNum()}</th>  
                        </tr>
                        
                    </thead>
                    <tbody>      
                    {if $permissions.manage == 1 && $smarty.foreach.foo.first}
                        <tr>
                        <td>
                        {if $bt->transaction_state == "authorized"}
                            {form action=captureAuthorization}
                                {control type="hidden" name="id" value=$order->id}
                                {control type="text" name="capture_amt" label="Amount to Capture" value=$order->grand_total}
                                {control type="buttongroup" submit="Capture Transaction"}
                            {/form}
                            {form action=voidAuthorization}
                                {control type="hidden" name="id" value=$order->id}
                                {control type="buttongroup" submit="Void Authorization"}
                            {/form}
                        {/if}
                        </td>
                        <td>
                        {if $bt->transaction_state == "complete"}
                            {form action=creditTransaction}
                                {control type="hidden" name="id" value=$order->id}
                                {control type="text" name="capture_amt" label="Amount to Refund" value=$order->grand_total}
                                {control type="buttongroup" submit="Credit "}
                            {/form}
                        {/if}
                        </td>
                        </tr>
                        
                    {/if}
                    
                    </tbody>  
                    </table>
                {/foreach}
            </div>
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.manage == 1}
            <div id="addinfo">
                <h2>Additional Information</h2>
                
                <h3>Order Type</h3>
                <p>This order is a {$order_type} order</p>
                {if $permissions.manage == 1}
                    {form action=set_order_type}
                        {control type="hidden" name="id" value=$order->id}
                        {control type="dropdown" name="order_type_id" label="Change order type to:" frommodel='order_type' orderby='rank' value=$order->order_type_id}
                        {control type=buttongroup submit="Save change"}
                    {/form}
                {/if}
                
                {br}{br}
                
                <h3>Origional Referrer</h3>
                <p>{$order->orig_referrer}</p>
            </div>
            <div id="notes">
                <h2>Notes</h2>
                {simplenote content_type="order" content_id=$order->id require_login="1" require_approval="0" require_notification="0" tab="notes"}
            </div>
        {/if}
    {/permissions}
        </div>
    </div>
</div>
<div class="loadingdiv">Loading Order</div>

{script unique="msgbox"}
{literal}
    function populate_msgbox() {
        var dd = YAHOO.util.Dom.get('order_status_messages');
        var msgbox = YAHOO.util.Dom.get('comment');
        var idx = dd.selectedIndex;
        
        if (dd.options[idx].value == 0) {
            msgbox.value = '';
        } else {
            msgbox.value = dd.options[idx].value;
        }
        
    }
{/literal}
{/script}
