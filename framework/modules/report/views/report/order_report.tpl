{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css"}

{/css}
{css unique="report-builder" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/report-builder.css"}

{/css}

<div class="module report build-report">
    <div id="report-form" class="exp-ecom-table">
    {form controller="report" action="generate_report" id="reportform" name="reportform"}
    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>
                    <h1>{gettext str="Build a Report"}</h1>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="odd">                
                <td>
                    {control type="calendar" name="startdate" label="Between"}
                </td>
            </tr>
            <tr class="even">
                 <td colspan="2">
                    {control type="calendar" name="enddate" label="And"}
                </td>
            </tr>
            <tr class="odd">
                  <td>
                    {control type="dropdown" name="order_status" label="An Order Status Of" size=4 multiple=true items=$order_status}    
                </td>
            </tr>
            <tr class="even">
                  <td>
                    {control type="dropdown" name="order_type" label="An Order Type Of" size=4 multiple=true items=$order_type}    
                </td>
            </tr>
            <tr class="odd">
                <td>
                    {control type="dropdown" name="order-range-op" label="An Order ID..." items="Equal to,Less than,Greater than" values="e,l,g"}
                    {control type="text" name="order-range-num" label=" " value=$record->orn class="collapse orn"}
                </td>
            </tr>
             <tr class="even">
                 <td>
                    {control type="dropdown" name="order-price-op" label="An Order Value..." items="Equal to,Less than,Greater than" values="e,l,g"}
                    {control type="text" name="order-price-num" label=" " value=$record->opn class="collapse orn"}
                </td> 
            </tr>
            <tr class="odd">
                <td>
                    {control type="text" name="pnam" label="Containg A Product Name Like" value=$record->product}
                </td>
                
            </tr>
            <tr class="even">
                <td>
                    {control type="text" name="sku" label="Containg A SKU Like" value=$record->sku}
                </td>
                
            </tr>
            <tr class="odd">
                 <td>
                    {control type="dropdown" name="discounts" label="Using Discount Code(s)" size=4 multiple=true items=$discounts default="-1" include_blank="true"}    
                </td> 
            </tr>
            <tr class="even">
                  <td>
                    {control type="text" name="blshpname" label="A Billing or Shipping Name Containing" value=$record->blshpname}
                </td>
            </tr>
            <tr class="odd">
                <td>
                    {control type="text" name="email" label="An Email Address Containing" value=$record->email}
                </td>
            </tr>
            <tr class="even">
                <td>
                    {control type=radiogroup columns=2 name="bl-sp-zip" label=" " items="By Billing,or Shipping Zipcode:" values="b,s"  default=$record->bl-sp-zip|default:"s"}
                    {control type="text" name="zip" label=" " size=7 value=$record->zip class="collapse"}
                </td>
            </tr>
            <tr class="odd">
                <td>
                    {control type=radiogroup columns=2 name="bl-sp-state" label=" " items="By Billing,or Shipping State:" values="b,s"  default=$record->bl-sp-zip|default:"s"}
                    {*control type="dropdown" name="state" label=" " size=4 multiple=true items=$states class="collapse" include_blank=true*} 
                    {control type="state" name="state" label=" " all_us_territories=true size=4 multiple=true class="collapse" includeblank=true} 
                </td>
            </tr>
           
            <tr class="even">
                <td>
                    {control type="dropdown" name="payment_method" label="A Payment Method of" multiple=true size=4 items=$payment_methods}
                </td>
            </tr>
             <tr class="odd">
                <td>
                    {control type="text" name="referrer" label="Referrer Like" value=$record->referrer}
                </td>
            </tr>
            <tr class="even">
                <td>
                    <a id="submit-report" href="#" onclick="document.reportform.submit(); return false;" class="btn"><strong><em>Generate Report</em></strong></a>
                </td>
            </tr>
        </tbody>
    </table>
    {/form}
    </div>
</div>
