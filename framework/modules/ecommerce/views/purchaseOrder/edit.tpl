{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
 
{css unique="purchase-orders" corecss="tables" link="`$asset_path`css/purchaseorder.css"}

{/css}



<div id="editpurchaseorder" class="module purchaseorder edit">

    <h1>{if $record->id}{"Editing"|gettext}{else}Creating{/if} {"Purchase Order"|gettext}</h1>
    <form id="create_po">
        {control type="dropdown" name="vendor" id="vendorSelect" label="Select a Vendor"|gettext includeblank="--Select a Vendor--"|gettext frommodel=vendor key=id display=title value=$purchase_order->vendor_id}
		
    </form>
    <div id="vendorinfo">
        <h2>{"Select a Vendor above"|gettext}</h2>
    </div>
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>
                {"Your Item #"|gettext}
                </th>
                <th>
                {"Your #"|gettext}
                </th>
                <th>
                {"Description"|gettext}
                </th>
                <th>
                {"Qty"|gettext}
                </th>
                <th>
                {"Rate"|gettext}
                </th>
                <th>
                {"Cust. Order"|gettext}
                </th>
                <th>
                {"Orig. PO"|gettext}
                </th>
                <th>
                {"ETA"|gettext}
                </th>
                <th>
                {"add/remove"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="controlrow">
                <form id="controlRowValues">
                <td>
                    <input type="text" name="vid" value="">
                </td>
                <td>
                    <input type="text" name="product" value="">
                </td>
                <td>
                    <textarea name="productdescription">
                    </textarea>
                </td>
                <td>
                    <input type="text" name="qty" value="" size="5">
                </td>
                <td>
                    <input type="text" name="rate" value="" size="5">
                </td>
                <td>
                    <input type="text" name="custorder" value="" size="5">
                </td>
                <td>
                    <input type="text" name="origpo" value="" size="5">
                </td>
                <td>
                    <input type="text" name="eta" value="" size="5">
                </td>
                <td>
                    <a href="#" id="addPOItem">[ {"add"|gettext} ]</a>
                </td>
            </form>    
            </tr>
        </tbody>
    </table>
</div>


{script unique="purchase-orders" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','event','io', function(Y) {
    var vendorSelect = Y.one('#vendorSelect');
    var addRow = Y.one('#addPOItem');
    var ctrlRow = Y.one('.controlrow');
    var vBody = Y.one('#vendorinfo');
    var vform = Y.one('#create_po');
    var poiForm = Y.one('#controlRowValues');
    var baseURL = EXPONENT.URL_FULL+"index.php?ajax_action=1&controller=purchaseOrder&action=edit&"
    var rc = 'odd';

    var selectVendor = function (e){
        var cfg = {
            method: 'POST',
            form: {
                id: vform,
                useDisabled: true
            },
            on: {
                start: function(){
                    vBody.setContent('<div class="loadingdiv">Adding Vendor Information<div>');
                },
                complete: function(id, o, args){
                    var data = o.responseText; // Response data.
                    vBody.setContent(data);
                }
                //end: Dispatch.end
            }
        };
        
        var uri = baseURL + "view=vendorinfo";

        var request = Y.io(uri, cfg);
    };
    
    var rowColor = function () {
        rc = (rc == 'odd') ? 'even' : 'odd';
        return rc;
    }

    var rowIOComplete = function(id, o, args) {
        var data = o.responseText; // Response data.
        ctrlRow.get('parentNode').one('tr.load').removeClass('load').addClass(rowColor()).setContent(data)
            .one('a.remove').on('click',function(e){
                e.halt();
                e.currentTarget.ancestor('tr').remove();
            });
        
        //var id = id; // Transaction ID.
        //var args = args[1]; // 'ipsum'.
    };


    var addItemRow = function (e){
        e.halt();
        
        var cfg = {
            method: 'POST',
            form: {
                id: poiForm,
                useDisabled: true
            },
            on: {
                start: function(){
                    ctrlRow.get('parentNode').insert('<tr class="load"><td colspan="9"><div class="loadingdiv">Adding Purchase Order Item<div></td></tr>',ctrlRow)
                },
                complete: rowIOComplete
                //end: Dispatch.end
            }
        };
        
        var uri = baseURL + "view=porow";

        var request = Y.io(uri, cfg);
        
        //console.debug(Y.one('#controlRowValues'));
        //ctrlRow.get('parentNode').insert('<tr><td colspan="9">testing</td></tr>',ctrlRow);
    };

    // Y.on('io:start', function(){
    //     
    // });

    //Y.on('io:complete', rowIOComplete);

    vendorSelect.on('change',selectVendor);
    addRow.on('click',addItemRow);
});
{/literal}
{/script}