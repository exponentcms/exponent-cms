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
 
{css unique="purchase-orders" corecss="tables" link="`$asset_path`css/purchaseorder.css"}

{/css}

<div id="managepurchaseorders" class="module purchaseorder manage">

    <h1>{"Viewing Purchase Order"|gettext}</h1>

	<!--
    <div class="filters">
        {control type="text" name="dynamicfilter" id="dynamicfilter" label="Filter By Order ID"|gettext}
        {control type="text" name="perpage" label="Items per-page"|gettext size=5 value=$perpage|default:50}
        {control type="dropdown" name="status" label="Show"|gettext items="All Orders,Only Open Orders, Only Closed Orders"|gettxtlist values="All Orders,Only Open Orders, Only Closed Orders"}
        {control type="dropdown" name="daterange" label="Within"|gettext items="The last Month,The Last 6 months,The Last Year,All Time"|gettxtlist values="The last Month,The Last 6 months,The Last Year,All Time"}
    </div>
	-->
	<div class="leftcol">
        <div class="module-actions">
            {icon action=manage_vendors class=manage text="Manage Vendors"|gettext}
            {icon action=edit_vendor class=add text="Add a new vendor"|gettext}
        </div>
		<h2>{'Select a Vendor'|gettext}</h2>
		<ul>
            <li {if !$vendor_id}class="current"{/if}><a href="{link action='getPurchaseOrderByJSON' ajax_action=1}">{'All Vendors'|gettext}</a></li>
            {foreach from=$vendors item=vendor}
                <li {if $vendor_id == $vendor->id}class="current"{/if}>
                    <a href="{link action='getPurchaseOrderByJSON' vendor=$vendor->id ajax_action=1}">{$vendor->title}</a>
                </li>
            {/foreach}
		</ul>
	</div>
	
	<div class="rightcol">
        <div class="module-actions">
            {icon action=create class=add text="Create new Purchase Order"|gettext}
        </div>
		<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
			<thead>
				<tr>
					<th>
                        {"Order Number"|gettext}
					</th>
					<th>
                        {"Vendor"|gettext}
					</th>
					<th>
                        {"Date"|gettext}
					</th>
					<th>
                        {"Status"|gettext}
					</th>
				</tr>
			</thead>
			<tbody id="purchaseOrderDynmicData">
				{foreach from=$purchase_orders item=purchase_order key=key name=purchase_order}
                    <tr>
                        <td>
                            {$purchase_order->purchase_order_number}
                        </td>
                        <td>
                            {$purchase_order->vendor->title}
                        </td>
                        <td>
                            {$purchase_order->created_at|format_date}
                        </td>
                        <td>
                            {'ordered'|gettext}
                        </td>
                    </tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>

{script unique="purchase-orders" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','io-base', 'json-parse', function(Y) {
	var vendors = Y.all('.purchaseorder.manage .leftcol ul li a');
	var purchaseOrderTable = Y.one("#purchaseOrderDynmicData");
	var filterVendor = function (e) {
		
		//Altered the default event of the anchor tag
		e.halt();
		 
		//Removed the previous current class
		Y.all('.purchaseorder.manage .leftcol ul li').removeClass('current');
		
		//Add the current class 
		e.currentTarget.ancestor('li').addClass('current');
		
		//Get the url for the request
		var uri =  e.currentTarget.getAttribute('href');
		
		 // Define a function to handle the response data.
		function onSuccess(transactionId, responseObject) {
			var id = id; // Transaction ID.
			var dataJson = responseObject.response; // Response data.
			
			data = '';
			var data = Y.JSON.parse(dataJson);
			var rows = '';
			Y.Array.each(data, function(v) {
				 rows = rows + '<tr><td>' + v.purchase_order_number + '</td><td>' + v.vendor.title + '</td><td>' + v.created_at + '<td>ordered</td>';
			});
			purchaseOrderTable.set("innerHTML", rows);
			responseObject = null;
			
		};

		// Subscribe to event "io:success"
		 Y.on('io:success', onSuccess, Y);

		// Make an HTTP request
		var request = Y.io(uri);
    };
	
	vendors.on('click',filterVendor);
});
{/literal}
{/script}
