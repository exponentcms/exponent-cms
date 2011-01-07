{if $smarty.get.printerfriendly==1}
    {assign var=pf value=1}
    {css unique="invoice" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/print-invoice.css"}

    {/css}
{else}
    {css unique="invoice" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/invoice.css"}

    {/css}
{/if}
{* eDebug var=$order *}
<div id="invoice">
    <div id="store-header">
        <h1>{$storeConfig.storename}</h1>
        {$storeConfig.header}
    </div>
    {if $pf}
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
                    {gettext str="Invoice #"}
                    </th>
                    <th>
                    {gettext str="Order Date"}
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
                    </td>
                    <td>
                    {$order->purchased|date_format:"%A, %B %e, %Y @ %r"}
                    </td>
                    <td>
                    {if $order->shipped}
                        {$order->shipped|date_format:"%A, %B %e, %Y @ %r":"Not Shipped Yet"}
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
                    <th>
                    {gettext str="Billing Address"}
                    </th>
                    <th>
                    {gettext str="Payment Info"}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                    <address>
                        <span class="fullname">{$order->billingmethod[0]->firstname} {$order->billingmethod[0]->lastname}</span>
                        {if $order->billingmethod[0]->organization != ""}<span class="company">{$order->billingmethod[0]->organization}</span>{/if}
                        {if $order->billingmethod[0]->address1 != ""}<span class="address1">{$order->billingmethod[0]->address1}</span>{/if}
                        {if $order->billingmethod[0]->address2 != ""}<span class="address2">{$order->billingmethod[0]->address2}</span>{/if}
                        <span class="citystatzip">{$order->billingmethod[0]->city}{if $order->billingmethod[0]->state != ""}, {$order->billingmethod[0]->state|statename:abbv}{/if} {$order->billingmethod[0]->zip}</span>
                        {if $order->billingmethod[0]->phone != ""}<span class="phone">{$order->billingmethod[0]->phone}</span>{/if}
                        {if $order->billingmethod[0]->email != ""}<span class="email">{$order->billingmethod[0]->email}</span>{/if}
                    </address>
                    </td>
                    <td class="div-rows">
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
                            {$billing->calculator->getPaymentReferenceNumber($billing->billingmethod)}
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

        <table class="shipping-info" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>
                    {gettext str="Shipping Address"}
                    </th>
                    <th>
                    {gettext str="Shipping Method"}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                    <address>
                        <span class="fullname">{$shipping->shippingmethod->firstname} {$shipping->shippingmethod->lastname}</span>
                        {if $shipping->shippingmethod->organization != ""}<span class="company">{$shipping->shippingmethod->organization}</span>{/if}
                        {if $shipping->shippingmethod->address1 != ""}<span class="address1">{$shipping->shippingmethod->address1}</span>{/if}
                        {if $shipping->shippingmethod->address2 != ""}<span class="address2">{$shipping->shippingmethod->address2}</span>{/if}
                        <span class="citystatzip">{$shipping->shippingmethod->city}{if $shipping->shippingmethod->state != ""}, {$shipping->shippingmethod->state|statename:abbv}{/if} {$shipping->shippingmethod->zip}</span>
                        {if $shipping->shippingmethod->phone != ""}<span class="phone">{$shipping->shippingmethod->phone}</span>{/if}
                        {if $shipping->shippingmethod->email != ""}<span class="email">{$shipping->shippingmethod->email}</span>{/if}
                    </address>
                    </td>
                    <td>
                        {$shipping->shippingmethod->option_title}
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
                        {$oi->products_name}
                        {if $oi->opts[0]}                             
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
                        {$oi->products_price_adjusted|number_format:2}
                    </td>
                    <td style="text-align:right;">
                        {$oi->getLineItemTotal()|number_format:2}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>

        <table class="totals-info" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan=3>
                        {gettext str="Totals"}
                    </th>
               </tr>
            </thead>
            <tbody>
                <tr class="even">
                    <td>
                    {gettext str="Subtotal"}
                    </td>
                    <td>
                    {currency_symbol}
                    </td
                    <td style="text-align:right;">{$order->subtotal|number_format:2}
                    </td>
                </tr>
                 {if $order->total_discounts > 0} 
                 <tr class="odd">
                    <td>
                    {gettext str="Total Discounts"}
                    </td>
                    <td>
                    {currency_symbol}
                    </td
                    <td style="text-align:right;">-{$order->total_discounts|number_format:2}
                    </td>
                </tr>
                <tr class="even">
                    <td>
                    {gettext str="Total"}
                    </td>
                    <td>
                    {currency_symbol}
                    </td
                    <td style="text-align:right;">{$order->total|number_format:2}
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
                    <td>
                    {currency_symbol}
                    </td
                    <td style="text-align:right;">{$order->tax|number_format:2}
                    </td>
                </tr>   
                <tr class="even">
                    <td>
                    {gettext str="Shipping & Handling"}
                    </td>
                    <td>
                    {currency_symbol}
                    </td
                    <td style="text-align:right;">{$order->shipping_total|number_format:2}
                    </td>
                </tr>
                {if $order->surcharge_total != 0}
                    <tr class="even">
                        <td>
                        {gettext str="Freight Surcharge"}
                        </td>
                        <td>
                        {currency_symbol}
                        </td
                        <td style="text-align:right;">{$order->surcharge_total|number_format:2}
                        </td>
                    </tr>
                {/if}
                <tr class="odd">
                    <td>
                    {gettext str="Order Total"}
                    </td>
                    <td>
                    {currency_symbol}
                    </td
                    <td style="text-align:right;">{$order->grand_total|number_format:2}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="store-footer">
        {$storeConfig.footer}
    </div>
    {* eDebug var=$billing->calculator->showOptions($billing->billingmethod->billing_options)}
    {eDebug var=$billing->billingmethod *} 
    
</div>
