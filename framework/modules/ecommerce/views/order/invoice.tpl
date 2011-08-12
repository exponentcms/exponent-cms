{if $printerfriendly==1}
    {assign var=pf value=1}
    {css unique="invoice" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/print-invoice.css"}

    {/css}
{else}
    {css unique="invoice" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/invoice.css"}
    {/css}
{/if}

<div id="invoice">
    <div id="store-header">
        <h1>{$storeConfig.storename}</h1>
        {$storeConfig.header}
    </div>
    {if $pf && $storeConfig.enable_barcode}
    <div id="barcode">
        <img style="margin:10px" src="{$smarty.const.URL_FULL}external/barcode.php?barcode={$order->invoice_id}&amp;width=400&amp;height=50" alt="">
    </div>
    {/if}
    <div id="invoice-data">
        <table class="order-info" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>
                    {gettext str="Source Site"}
                    </th>
                    <th>
                    {gettext str="Order #"}
                    </th>
                    <th>
                    {gettext str="Order Date"}
                    </th>
                    <th>
                    {gettext str="Order Type"}
                    </th>
                    <th>
                    {gettext str="Date Shipped"}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                    {$storeConfig.storename}
                    </td>
                    <td>
                    {$order->invoice_id}
                    {permissions}
                        <div class="item-permissions">
                            {if $permissions.edit_invoice_id == 1 && !$pf}                                                                                        
                                {br}    
                                <a href="{link action=edit_invoice_id id=$order->id}">[-edit-]</a>
                            {/if} 
                        </div>
                     {/permissions}
                    </td>
                    <td>
                    {$order->purchased|date_format:"%A, %B %e, %Y"}
                    </td>
                    <td>
                    {$order->order_type->title}
                    </td>
                    <td>
                    {if $order->shipped}
                        {$order->shipped|date_format:"%A, %B %e, %Y":"Not Shipped Yet"}
                    {else}
                        Not Shipped Yet
                    {/if}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="payment-info" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="billing-header" style="width:27%;">
                    {gettext str="Billing Address"}
                    </th>
                    <th class="shipping-header" style="width:27%;">
                    {gettext str="Shipping Address"}
                    </th>
                    <th class="payment-info-header" style="width:46%;">
                    {gettext str="Payment Info"}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width:27%;">
                    {$order->billingmethod[0]->addresses_id|address}
                    {permissions}
                    <div class="item-permissions">
                        {if $permissions.edit_address == 1 && !$pf}                                                                                        
                        {br}    
                        <a href="{link action=edit_address id=$order->id type='b'}">[-edit-]</a>
                        {/if} 
                    </div>
                    {/permissions}
                    </td>
                    <td style="width:27%;">
                        {$shipping->shippingmethod->addresses_id|address}
                        {permissions}
                            <div class="item-permissions">
                                {if $permissions.edit_address == 1 && !$pf}                                                                                        
                                    {br}    
                                    <a href="{link action=edit_address id=$order->id type='s'}">[-edit-]</a>
                                    {br} 
                                {/if}
                            </div>
                        {/permissions}   
                        {br}
                        <table style="width: 100%; border: 0px; text-align: left; padding: 0px; margin:0px;">
                        <tr style="border: 0px; padding: 0px; margin:0px;">
                        <td style="border: 0px; text-align: left; padding: 0px; margin:0px;">
                            <strong>{gettext str="Shipping Method"}:</strong>{br}
                        {$shipping->shippingmethod->option_title}
                        {permissions}
                            <div class="item-permissions">
                                {if $permissions.edit_shipping_method == 1 && !$pf}                                                                                        
                                    {br}    
                                    <a href="{link action=edit_shipping_method id=$order->id}">[-edit-]</a>
                                {/if} 
                            </div>
                        {/permissions}                            
                        </td>                        
                        <td style="border: 0px; text-align: left; padding: 0px; padding-right: 5px; margin:0px;">
                            {if $shipping->shippingmethod->carrier != ''}           
                            <strong>{gettext str="Carrier"}:</strong>{br}
                            {$shipping->shippingmethod->carrier}         
                            {/if}               
                        </td>
                        </tr>
                        </table>                     
                    </td>
                    <td class="div-rows" style="width:46%;">
                        <div class="odd">
                            <span class="pmt-label">
                            {gettext str="Payment Method"}
                            </span>
                            <span class="pmt-value">
                            {$billing->calculator->getPaymentMethod($billing->billingmethod)}
                            </span>
                        </div>
                        <div class="even">
                            <span class="pmt-label">
                            {gettext str="Payment Status"}
                            </span>
                            <span class="pmt-value">
                            {$billing->calculator->getPaymentStatus($billing->billingmethod)}
                            </span>
                        </div>
                        <div class="odd">
                            <span class="pmt-label">
                            {gettext str="Payment Authorization #"}
                            </span>
                            <span class="pmt-value">
                            {$billing->calculator->getPaymentAuthorizationNumber($billing->billingmethod)}
                            </span>
                        </div>
                        <div class="even">
                            <span class="pmt-label">
                            {gettext str="Payment Reference #"}
                            </span>
                            <span class="pmt-value">
                            {$billing->calculator->getPaymentReferenceNumber($billing->billingmethod->billing_options)}
                            </span>
                        </div>
                        <div class="odd">
                            <span class="pmt-label">
                            {gettext str="AVS Address Verified"}
                            </span>
                            <span class="pmt-value">
                            {$billing->calculator->getAVSAddressVerified($billing->billingmethod)}
                            </span>
                        </div>
                        <div class="even">
                            <span class="pmt-label">
                            {gettext str="AVS ZIP Verified"}
                            </span>
                            <span class="pmt-value">
                            {$billing->calculator->getAVSZipVerified($billing->billingmethod)}
                            </span>
                        </div>
                        <div class="odd">
                            <span class="pmt-label">
                            {gettext str="CVV # Matched"}
                            </span>
                            <span class="pmt-value">
                            {$billing->calculator->getCVVMatched($billing->billingmethod)}
                            </span>
                        </div>
                         {permissions}
                                {if $permissions.edit_shipping_method == 1 && !$pf}{br} 
                            <div class="item-permissions">
                                <a href="{link action=edit_payment_info id=$order->id}">[-edit-]</a>
                                {br}{br}   
                            </div>
                                {/if}                            
                        {/permissions}                                  
                    </td>
                </tr>
            </tbody>
        </table>

        {assign var=sm value=$order->orderitem[0]->shippingmethod}
        {if $sm->to != "" || $sm->from != "" || $sm->message != ""}
        <table class="gift-message" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>
                    {gettext str="Gift Message"}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>To: </strong>{$sm->to}{br}
                        <strong>From: </strong>{$sm->from}{br}
                        <strong>Message: </strong>{$sm->message}{br}
                    </td>
                </tr>
            </tbody>
        </table>
        {/if}

        <table class="order-items" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>
                        {gettext str="QTY"}
                    </th>
                    <th>
                        {gettext str="SKU"}
                    </th>
                    <th>
                        {gettext str="Description"}
                    </th>
                    <th>
                        {gettext str="Location"}
                    </th>
                    <th>
                        {gettext str="Status"}
                    </th>
                    <th style="text-align:right;">
                        {gettext str="Price"}
                    </th>
                    <th style="text-align:right;">
                        {gettext str="Amount"}
                    </th>
                    {permissions}
                        <div class="item-permissions">
                            {if $permissions.edit_order_item == 1 && !$pf}                                                                                                             
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
                        {$oi->quantity}
                    </td>
                    <td>
                        {$oi->products_model}
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
                        {$oi->getExtraData()}
                    </td>
                    <td>
                        {$oi->products_warehouse_location}
                    </td>
                    <td>
                        {$oi->products_status}
                    </td>
                    <td style="text-align:right;">
                        {$oi->products_price|number_format:2}
                    </td>
                    <td style="text-align:right;">
                        {$oi->getLineItemTotal()|number_format:2}
                    </td>
                    {permissions}
                        <div class="item-permissions">
                            {if $permissions.edit_order_item == 1 && !$pf}                                                                                                             
                                <td style="text-align:right;"><a href="{link action=edit_order_item id=$oi->id orderid=$order->id}">[-edit-]</a>&nbsp;<a href="{link action=delete_order_item id=$oi->id orderid=$order->id}" onclick="return confirm('Are you sure you want to delete this item from this order?')">[X]</a></td>     
                            {/if}
                        </div>
                    {/permissions}
                </tr>
            {/foreach}
             {permissions}
                <div class="item-permissions">
                {if $permissions.add_order_item == 1 && !$pf} 
                    <tr>
                        <td colspan="8" style='text-align: right;'><!--a href="{link action=add_order_item id=$order->id}">[+]</a-->
                        {capture assign="callbacks"}
                        {literal}                       
                        
                        // the text box for the title
                        var tagInput = Y.one('#add_new_item');

                        // the UL to append to
                        var tagUL = Y.one('#new_items');

                        // the Add Link
                        var tagAddToList = Y.one('#addToRelProdList');


                        var onRequestData = function( oSelf , sQuery , oRequest) {
                            tagInput.setStyles({'border':'1px solid green','background':'#fff url('+EXPONENT.PATH_RELATIVE+'framework/core/subsystems-1/forms/controls/assets/autocomplete/loader.gif) no-repeat 100% 50%'});
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
                            var f = '<form id=addItem method=post>';
                                f += '<input type=hidden name=orderid id=orderid value={/literal}{$order->id}{literal}>';
                                f += '<input type=hidden name=module id=module value=order>';
                                f += '<input type=hidden name=action id=action value=add_order_item>';
                                f += '<input type=hidden name=product_id id=product_id value=' + val.id + '>';
                                f += '<input type=submit name=submit value="Add This Item">';
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
                        {control type="autocomplete" controller="store" action="search" name="add_new_item" label="Add a new item" value="Search title or SKU to add an item" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" jsinject=$callbacks}
                        <div id="new_items">                        
                        </div>
                        </td>
                    </tr>
                {/if}
                </div>
             {/permissions}
            </tbody>
        </table>

        <table class="totals-info" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    {if !$pf}                         
                    <th>
                    {else}
                    <th  colspan=3>
                    {/if} 
                    {gettext str="Totals"}
                    </th>
                    {if !$pf}<th colspan="2"></th>{/if}                              
               </tr>
            </thead>
            <tbody>
                <tr class="even">
                    <td>
                    {gettext str="Subtotal"}
                    </td>
                    <td style="border-right:0px">
                    {currency_symbol}
                    </td>
                    <td  style="text-align:right; border-left:0px;">{$order->subtotal|number_format:2}
                    </td>
                </tr>
                
                 {if (isset($order->order_discounts[0]) && $order->order_discounts[0]->isCartDiscount()) || $order->total_discounts > 0} 
                 <tr class="odd">
                    <td>
                    {if isset($order->order_discounts[0]) && $order->order_discounts[0]->isCartDiscount()}
                        {gettext str="Total Cart Discounts (Code: `$order->order_discounts[0]->coupon_code`)"}
                    {else}
                        {gettext str="Total Cart Discounts"}
                    {/if}
                    
                    </td>
                    <td style="border-right:0px">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right; border-left:0px;">-{$order->total_discounts|number_format:2}
                    </td>
                </tr>
                <tr class="even">
                    <td>
                    {gettext str="Total"}
                    </td>
                    <td style="border-right:0px">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right; border-left:0px;">{$order->total|number_format:2}
                    </td>
                </tr>   
                 {/if}
                  <tr class="odd">
                    <td width="90%">
                    {gettext str="Tax - "}
                    {foreach from=$order->taxzones item=zone}
                        {$zone->name} ({$zone->rate}%)
                    {foreachelse}
                        (Not Required)
                    {/foreach}
                    </td>
                    <td style="border-right:0px;">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right; border-left:0px;">{$order->tax|number_format:2}
                    </td>
                </tr>   
                <tr class="even">
                    <td>
                    {if isset($order->order_discounts[0]) && $order->order_discounts[0]->isShippingDiscount()} 
                        {gettext str="Shipping & Handling (Discount Code: `$order->order_discounts[0]->coupon_code`)"}
                    {else}
                        {gettext str="Shipping & Handling"}
                    {/if}
                    
                    </td>
                    <td style="border-right:0px;">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right;  border-left:0px;">{$order->shipping_total|number_format:2}
                    </td>
                </tr>
                {if $order->surcharge_total != 0}
                    <tr class="even">
                        <td>
                        {gettext str="Freight Surcharge"}
                        </td>
                        <td style="border-right:0px;">
                        {currency_symbol}
                        </td>
                        <td style="text-align:right; border-left:0px;">{$order->surcharge_total|number_format:2}
                        </td>
                    </tr>
                {/if}
                <tr class="odd">
                    <td>
                    {gettext str="Order Total"}
                    </td>
                    <td style="border-right:0px;">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right; border-left:0px;">{$order->grand_total|number_format:2}
                    </td>
                </tr>                                                                        
                {permissions}
                    <div class="item-permissions">
                        {if $permissions.edit_totals == 1 && !$pf}                                                                                                             
                            <tr class="even">                   
                                <td style="text-align:right; border-left:0px;" colspan='3'><a href="{link action=edit_totals orderid=$order->id}">[edit totals]</a>
                                </td>
                            </tr>
                        {/if}
                    </div>
                {/permissions}
            </tbody>
        </table>
    </div>
    <div id="store-footer">
        {$storeConfig.footer}
    </div>    
    
</div>
