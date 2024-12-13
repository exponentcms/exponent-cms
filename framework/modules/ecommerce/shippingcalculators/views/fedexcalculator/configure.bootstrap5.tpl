{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

<div id="fedex">
    <blockquote>
        {'To setup a FEDEX account, visit this page'|gettext} <a href="https://www.fedex.com/login/web/jsp/logon.jsp" target="_blank">https://www.fedex.com/login/web/jsp/logon.jsp</a>
        <ul>
            <li>{'You will also need to obtain a Developer Test Key'|gettext} <a href="https://www.fedex.com/us/developer/web-services/process.html?tab=tab2" target="_blank">https://www.fedex.com/wpor/web/jsp/drclinks.jsp?links=wss/develop.html</a></li>
        </ul>
    </blockquote>
    <div id="fedex-tabs" class="">
        <ul class="nav nav-tabs" role="tablist">
	        <li role="presentation" class="nav-item"><a href="#tab1" class="nav-link active" role="tab" data-bs-toggle="tab"><em>{'General Settings'|gettext}</em></a></li>
	        <li role="presentation" class="nav-item"><a href="#tab2" class="nav-link" role="tab" data-bs-toggle="tab"><em>{'Shipping Methods'|gettext}</em></a></li>
	        <li role="presentation" class="nav-item"><a href="#tab3" class="nav-link" role="tab" data-bs-toggle="tab"><em>{'Shipping Origin'|gettext}</em></a></li>
	        <li role="presentation" class="nav-item"><a href="#tab4" class="nav-link" role="tab" data-bs-toggle="tab"><em>{'Packaging Defaults'|gettext}</em></a></li>
        </ul>
        <div class="tab-content">
	        <div id="tab1" role="tabpanel" class="tab-pane fade show active">
	            {control type="text" name="fedex_account_number" label="FedEx Account Number"|gettext value=$calculator->configdata.fedex_account_number required=1}
	            {control type="text" name="fedex_meter_number" label="Meter Number"|gettext value=$calculator->configdata.fedex_meter_number required=1}
	            {control type="text" name="fedex_key" label="Key"|gettext value=$calculator->configdata.fedex_key required=1}
	            {control type="password" name="fedex_password" label="Password"|gettext value=$calculator->configdata.fedex_password required=1}
	            {*control type="text" name="shipfrom[shipperNumber]" label="Account #" value=$calculator->configdata.shipfrom.shipperNumber*}
	            {control type="checkbox" name="testmode" label="Enable Test Mode"|gettext value=1 checked=$calculator->configdata.testmode}
	        </div>
	        <div id="tab2" role="tabpanel" class="tab-pane fade">
                {foreach $calculator->configdata.shipping_methods as $key=>$method}
                    {$shipping_methods[$method] = true}
                {/foreach}
	            {control type="checkbox" name="shipping_methods[]" label="FedEx Next Day Air - Delivery by 8:30AM"|gettext value="FIRST_OVERNIGHT" checked=$shipping_methods['FIRST_OVERNIGHT']}
	            {control type="checkbox" name="shipping_methods[]" label="FedEx Next Day Air - Delivery by 10:30AM"|gettext value="PRIORITY_OVERNIGHT" checked=$shipping_methods['PRIORITY_OVERNIGHT']}
	            {control type="checkbox" name="shipping_methods[]" label="FedEx Standard Overnight - Delivery by 3PM"|gettext value="STANDARD_OVERNIGHT" checked=$shipping_methods['STANDARD_OVERNIGHT']}
	            {control type="checkbox" name="shipping_methods[]" label="FedEx 2Day - Delivery by 10:30AM" value="FEDEX_2_DAY_AM"|gettext checked=$shipping_methods['FEDEX_2_DAY_AM']}
	            {control type="checkbox" name="shipping_methods[]" label="FedEx 2Day - Delivery by 4:30PM" value="FEDEX_2_DAY"|gettext checked=$shipping_methods['FEDEX_2_DAY']}
	            {control type="checkbox" name="shipping_methods[]" label="FedEx 3Day Express Saver - Delivery by 4:30PM"|gettext value="FEDEX_EXPRESS_SAVER" checked=$shipping_methods['FEDEX_EXPRESS_SAVER']}
	            {control type="checkbox" name="shipping_methods[]" label="FedEx Ground - 1-5 Business Days"|gettext value="FEDEX_GROUND" checked=$shipping_methods['FEDEX_GROUND']}
	        </div>
	        <div id="tab3" role="tabpanel" class="tab-pane fade">
                {ecomconfig var=store assign=store}
   	            {control type="text" name="shipfrom[address1]" label="Address"|gettext value=$calculator->configdata.shipfrom.address1|default:$store.address1 required=1}
   	            {control type="text" name="shipfrom[address2]" label=" " value=$calculator->configdata.shipfrom.address2|default:$store.address2}
                {control type="text" name="shipfrom[address3]" label=" " value=$calculator->configdata.shipfrom.address3}
   	            {control type="text" name="shipfrom[City]" label="City"|gettext value=$calculator->configdata.shipfrom.city|default:$store.city required=1}
                {control type="countryregion" name="shipfrom[address]" label="Country/State"|gettext country_default=$calculator->configdata.shipfrom.country|default:$store.country region_default=$calculator->configdata.shipfrom.state|default:$store.state includeblank="-- Choose a State --"|gettext required=1}
   	            {control type="text" name="shipfrom[PostalCode]" label="Zip Code"|gettext size=10 value=$calculator->configdata.shipfrom.postalCode|default:$store.postalCode required=1}
	        </div>
	        <div id="tab4" role="tabpanel" class="tab-pane fade">
                {control type="text" name="default_height" label="Standard Box Height (inches)"|gettext size=5 value=$calculator->configdata.default_height}
	            {control type="text" name="default_width" label="Standard Box Width (inches)"|gettext size=5 value=$calculator->configdata.default_width}
	            {control type="text" name="default_length" label="Standard Box Length (inches)"|gettext size=5 value=$calculator->configdata.default_length}
	            {control type="text" name="default_max_weight" label="Default Weight for Box (lbs)"|gettext size=5 value=$calculator->configdata.default_max_weight}
	        </div>
        </div>
    </div>
    {loading}
</div>
