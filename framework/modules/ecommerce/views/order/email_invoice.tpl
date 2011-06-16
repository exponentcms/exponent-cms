<div id="invoice" style="color:#000000; font-size:100%; position:relative; text-align: left; margin: 0px; padding: 0px;">   
    <div id="invoice-data">
        <table style="border:0 none; width:100%; color:#000000; margin-bottom: 1em; " border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="background:none repeat scroll 0 0 #CDCDCD;">
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top;">
                    {gettext str="Source Site"}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top;">
                    {gettext str="Order #"}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top;">
                    {gettext str="Order Date"}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top;">
                    {gettext str="Date Shipped"}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #DEDEDE;">
                    {$storeConfig.storename}
                    {$storeConfig.header}  
                    </td>
                    <td style="border:1px solid #DEDEDE;">
                    {$order->invoice_id}
                    </td>
                    <td style="border:1px solid #DEDEDE;">
                    {$order->purchased|date_format:"%A, %B %e, %Y"}
                    </td>
                    <td style="border:1px solid #DEDEDE;">
                    {if $order->shipped}
                        {$order->shipped|date_format:"%A, %B %e, %Y":"Not Shipped Yet"}
                    {else}
                        Not Shipped Yet
                    {/if}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="payment-info" style="margin-bottom:1em;" width="100%" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="background:none repeat scroll 0 0 #CDCDCD;">
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top; width: 30%;">
                    {gettext str="Billing Address"}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top; width: 30%">
                    {gettext str="Shipping Address"}
                    </th>
                    <th style="border:1px solid #DEDEDE; text-align: left; vertical-align: top; width: 39%">
                    {gettext str="Payment Info"}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #DEDEDE; text-align:left; vertical-align:top; padding:0.5em;">
                    <address>
                        <span class="fullname" style="display:block;">{$order->billingmethod[0]->firstname} {if $order->billingmethod[0]->middlename !=''}{$order->billingmethod[0]->middlename} {/if}{$order->billingmethod[0]->lastname}</span>{br}
                        {if $order->billingmethod[0]->organization != ""}<span class="company" style="display:block;">{$order->billingmethod[0]->organization}</span>{br}{/if}
                        {if $order->billingmethod[0]->address1 != ""}<span class="address1" style="display:block;">{$order->billingmethod[0]->address1}</span>{br}{/if}
                        {if $order->billingmethod[0]->address2 != ""}<span class="address2" style="display:block;">{$order->billingmethod[0]->address2}</span>{br}{/if}
                        <span class="citystatzip" style="display:block;">{$order->billingmethod[0]->city}
                        {if $order->billingmethod[0]->state == -2}
                            , {$order->billingmethod[0]->non_us_state}{br}                            
                        {elseif $order->billingmethod[0]->state != ""}
                            , {$order->billingmethod[0]->state|statename:abbv}
                        {/if} 
                        {$order->billingmethod[0]->zip}
                        {if $order->billingmethod[0]->state == -2}
                            {br}{$order->billingmethod[0]->country|countryname}{br}
                        {/if}
                        </span>{br}
                        {if $order->billingmethod[0]->phone != ""}<span class="phone" style="display:block;">{$order->billingmethod[0]->phone}</span>{br}{/if}
                        {if $order->billingmethod[0]->email != ""}<span class="email" style="display:block;">{$order->billingmethod[0]->email}</span>{br}{/if}                        
                    </address>                    
                    </td>
                    <td style="border:1px solid #DEDEDE; text-align:left; vertical-align:top; padding:0.5em;">
                        <address>
                            <span class="fullname" style="display:block;">{$shipping->shippingmethod->firstname} {if $shipping->shippingmethod->middlename !=''}{$shipping->shippingmethod->middlename} {/if}{$shipping->shippingmethod->lastname}</span>{br}
                            {if $shipping->shippingmethod->organization != ""}<span class="company" style="display:block;">{$shipping->shippingmethod->organization}</span>{br}{/if}
                            {if $shipping->shippingmethod->address1 != ""}<span class="address1" style="display:block;">{$shipping->shippingmethod->address1}</span>{br}{/if}
                            {if $shipping->shippingmethod->address2 != ""}<span class="address2" style="display:block;">{$shipping->shippingmethod->address2}</span>{br}{/if}
                            <span class="citystatzip" style="display:block;">{$shipping->shippingmethod->city}{if $shipping->shippingmethod->state != ""}, {$shipping->shippingmethod->state|statename:abbv}{/if} {$shipping->shippingmethod->zip}</span>{br}
                            {if $shipping->shippingmethod->phone != ""}<span class="phone" style="display:block;">{$shipping->shippingmethod->phone}</span>{br}{/if}
                            {if $shipping->shippingmethod->email != ""}<span class="email" style="display:block;">{$shipping->shippingmethod->email}</span>{br}{/if}
                        </address>                       
                        {br}
                        <strong>{gettext str="Shipping Method"}:</strong>{br}
                        {$shipping->shippingmethod->option_title}
                    </td>
                    <td class="div-rows" style="border:1px solid #DEDEDE; text-align:left; vertical-align:top; padding:0.5em;">
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
                    </td>
                </tr>
            </tbody>
        </table>

        {assign var=sm value=$order->orderitem[0]->shippingmethod}
        {if $sm->to != "" || $sm->from != "" || $sm->message != ""}
        <table style="margin-bottom:1em;" class="gift-message" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="background:none repeat scroll 0 0 #CDCDCD;">
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

        <table style="margin-bottom:1em;" class="order-items" border="0" width="100%" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="background:none repeat scroll 0 0 #CDCDCD;">
                    <th style="border:1px solid #DEDEDE;">
                        {gettext str="QTY"}
                    </th>
                    <th style="border:1px solid #DEDEDE;">
                        {gettext str="SKU"}
                    </th>
                    <th style="border:1px solid #DEDEDE;">
                        {gettext str="Description"}
                    </th>                    
                    <th style="text-align:right; border:1px solid #DEDEDE;">
                        {gettext str="Price"}
                    </th>
                    <th style="text-align:right; border:1px solid #DEDEDE;">
                        {gettext str="Amount"}
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
                    {gettext str="Totals"}
                    </th>                    
               </tr>
            </thead>
            <tbody>
                <tr class="even">
                    <td style="border:1px solid #DEDEDE;">
                    {gettext str="Subtotal"}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px">
                    {currency_symbol}
                    </td>
                    <td  style="text-align:right; border:1px solid #DEDEDE; border-left:0px;">{$order->subtotal|number_format:2}
                    </td>
                </tr>
                 {if isset($order->order_discounts[0]) && $order->order_discounts[0]->isCartDiscount()} 
                 <tr class="odd">
                    <td style="border:1px solid #DEDEDE;">
                    {gettext str="Total Discounts (Code: `$order->order_discounts[0]->coupon_code`)"}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE;  border-left:0px;">-{$order->total_discounts|number_format:2}
                    </td>
                </tr>
                <tr class="even">
                    <td style="border:1px solid #DEDEDE;">
                    {gettext str="Total"}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE;  border-left:0px;">{$order->total|number_format:2}
                    </td>
                </tr>   
                 {/if}
                  <tr class="odd">
                    <td width="90%" style="border:1px solid #DEDEDE;">
                    {gettext str="Tax - "}
                    {foreach from=$order->taxzones item=zone}
                        {$zone->name} ({$zone->rate}%)
                    {foreachelse}
                        (Not Required)
                    {/foreach}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px;">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE; border-left:0px;">{$order->tax|number_format:2}
                    </td>
                </tr>   
                <tr class="even">
                    <td style="border:1px solid #DEDEDE;">
                        {if isset($order->order_discounts[0]) && $order->order_discounts[0]->isShippingDiscount()} 
                            {gettext str="Shipping & Handling (Discount Code: `$order->order_discounts[0]->coupon_code`)"}
                        {else}
                            {gettext str="Shipping & Handling"}
                        {/if}                    
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px;">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right; border:1px solid #DEDEDE;  border-left:0px;">{$order->shipping_total|number_format:2}
                    </td>
                </tr>
                {if $order->surcharge_total != 0}
                    <tr class="even">
                        <td style="border:1px solid #DEDEDE;">
                        {gettext str="Freight Surcharge"}
                        </td>
                        <td style="border:1px solid #DEDEDE; border-right:0px;">
                        {currency_symbol}
                        </td>
                        <td style="text-align:right;border:1px solid #DEDEDE;  border-left:0px;">{$order->surcharge_total|number_format:2}
                        </td>
                    </tr>
                {/if}
                <tr class="odd">
                    <td style="border:1px solid #DEDEDE;">
                    {gettext str="Order Total"}
                    </td>
                    <td style="border:1px solid #DEDEDE; border-right:0px;">
                    {currency_symbol}
                    </td>
                    <td style="text-align:right;border:1px solid #DEDEDE;  border-left:0px;">{$order->grand_total|number_format:2}
                    </td>
                </tr>                                                                       
               
            </tbody>
        </table>
    </div>
    <div id="store-footer">
        {$storeConfig.footer}
    </div>
</div>
