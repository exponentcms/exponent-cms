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

{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css"}

{/css}
{css unique="report-builder" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/report-builder.css" corecss="button"}

{/css}

<div class="module report build-report">
    <div id="report-form" class="exp-ecom-table">
        {form controller="report" action="generateOrderReport" id="reportform" name="reportform"}
            <table border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th>
                            <h1>{"Build an Order Report"|gettext}</h1>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="even">
                          <td>
                            {control type="dropdown" name="order_type" label="An Order Type Of..."|gettext size=4 multiple=true items=$order_type default=-1}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>
                            {control type="checkbox" name="include_purchased_date" label="Include Purchase Date"|gettext flip=true}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>
                            {control type="calendar" name="pstartdate" label="Purchased Between"|gettext default_date=$prev_month default_hour='12' default_min='00' default_ampm='AM'}
                        </td>
                    </tr>
                    <tr class="odd">
                         <td>
                            {control type="calendar" name="penddate" label="And"|gettext default_date=$now_date default_hour=$now_hour default_min=$now_min default_ampm=$now_ampm}
                        </td>
                    </tr>
                     <tr class="even">
                          <td>
                            {control type="dropdown" name="order_status" label="A CURRENT Order Status Of..."|gettext size=4 multiple=true items=$order_status default=-1}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>
                            {control type="checkbox" name="include_status_date" label="Include Status Changed Date"|gettext flip=true}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>
                            {control type="calendar" name="sstartdate" label="Status Changed Between"|gettext default_date=$prev_month default_hour='12' default_min='00' default_ampm='AM'}
                        </td>
                    </tr>
                    <tr class="odd">
                         <td>
                            {control type="calendar" name="senddate" label="And"|gettext default_date=$now_date default_hour=$now_hour default_min=$now_min default_ampm=$now_ampm}
                        </td>
                    </tr>
                    <tr class="even">
                          <td>
                            {control type="dropdown" name="order_status_changed" label="Status Was Changed To..."|gettext size=4 multiple=true items=$order_status default=-1}
                        </td>
                    </tr>
                    <!--tr class="even">
                          <td>
                            {* control type="dropdown" name="order_type" label="An Order Type Of..."|gettext size=4 multiple=true items=$order_type default=-1 *}
                        </td>
                    </tr-->
                    <tr class="odd">
                        <td>
                            {control type="dropdown" name="order-range-op" label="An Order ID..."|gettext items="Equal to,Less than,Greater than"|gettxtlist values="e,l,g"}
                            {*{control type="text" name="order-range-num" value=$record->orn class="collapse orn"}*}
                            {control type="text" name="order-range-num" size=8 value=$record->orn class="orn"}
                        </td>
                    </tr>
                     <tr class="even">
                         <td>
                            {control type="dropdown" name="order-price-op" label="An Order Value..."|gettext items="Equal to,Less than,Greater than"|gettxtlist values="e,l,g"}
                            {*{control type="text" name="order-price-num" value=$record->opn class="collapse orn"}*}
                             {control type="text" name="order-price-num" size=8 value=$record->opn class="orn" filter=money}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>
                            {control type="text" name="pnam" label="Contains A Product Name Like"|gettext value=$record->product}
                        </td>

                    </tr>
                    <tr class="even">
                        <td>
                            {control type="text" name="sku" label="Contains A SKU Like"|gettext value=$record->sku}
                        </td>

                    </tr>
                    <tr class="odd">
                        <td>
                            {control type="checkbox" name="uidata" label="Has Items With User Input Data"|gettext value=$record->uidata flip=true}
                        </td>

                    </tr>
                    <tr class="even">
                        <td>
                            {control type="dropdown" name="product_status" label="Contains A Product with a Status Of"|gettext includeblank="--Any--"|gettext size=4 multiple=true frommodel=product_status}
                        </td>
                    </tr>
                    <tr class="odd">
                         <td>
                            {control type="dropdown" name="discounts" label="Using Discount Code(s)"|gettext size=4 multiple=true items=$discounts default="-1" include_blank="true"}
                        </td>
                    </tr>
                    <tr class="even">
                          <td>
                            {control type="text" name="blshpname" label="A Billing or Shipping Name Containing"|gettext value=$record->blshpname}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>
                            {*{control type="text" name="email" label="An Email Address Containing"|gettext value=$record->email}*}
                            {control type=email name="email" label="An Email Address Containing"|gettext value=$record->email}
                        </td>
                    </tr>
                    <tr class="even">
                        <td>
                            {control type=radiogroup label='By Zipcode:'|gettext columns=2 name="bl-sp-zip" items="Billing,or Shipping:"|gettxtlist values="b,s"  default=$record->bl-sp-zip|default:"s"}
                            {*{control type="text" name="zip" size=7 value=$record->zip class="collapse"}*}
                            {control type="text" name="zip" size=7 value=$record->zip class=""}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>
                            {control type=radiogroup label='By State:'|gettext columns=2 name="bl-sp-state" items="Billing,or Shipping:"|gettxtlist values="b,s" default=$record->bl-sp-zip|default:"s"}
                            {*control type="dropdown" name="state" size=4 multiple=true items=$states class="collapse" include_blank=true*}
                            {*{control type="state" name="state" all_us_territories=true exclude="6,8,10,17,30,46,50" size=4 multiple=true class="collapse" includeblank=true}*}
                            {control type="state" name="state" all_us_territories=true exclude="6,8,10,17,30,46,50" size=4 multiple=true class="" includeblank=true}
                        </td>
                    </tr>

                    <tr class="even">
                        <td>
                            {control type="dropdown" name="payment_method" label="A Payment Method of"|gettext multiple=true size=4 items=$payment_methods}
                        </td>
                    </tr>
                     <tr class="odd">
                        <td>
                            {control type="text" name="referrer" label="Referrer Like"|gettext value=$record->referrer}
                        </td>
                    </tr>
                    <tr class="even">
                        <td>
                            <a id="submit-report" href="#" onclick="document.reportform.submit(); return false;" class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}"><strong><em>Generate Report</em></strong></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        {/form}
    </div>
</div>
