{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div id="ups">
    <blockquote>
        {'To setup an easypost account, visit this page'|gettext} <a href="https://www.easypost.com/signup" target="_blank">https://www.easypost.com/signup</a>
        <ul>
            <li>{'After registering you\'ll be logged in with the account keys displayed'|gettext}</li>
        </ul>
    </blockquote>
    <div id="ups-tabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>{'General Settings'|gettext}</em></a></li>
	        <li><a href="#tab2"><em>{'Shipping Carriers'|gettext}</em></a></li>
	        <li><a href="#tab3"><em>{'Shipping Origin'|gettext}</em></a></li>
	        <li><a href="#tab4"><em>{'Packaging Defaults'|gettext}</em></a></li>
        </ul>            
        <div class="yui-content">
	        <div id="tab1">
                {control type="text" name="apikey" label="Live API Key"|gettext value=$calculator->configdata.apikey required=1}
                {control type="text" name="testkey" label="Test API Key"|gettext value=$calculator->configdata.testkey|default:'eFCh4a4TS3YoTivTVE0avQ' required=1}
	            {control type="checkbox" name="testmode" label="Enable Test Mode"|gettext value=1 checked=$calculator->configdata.testmode}
                {control type="text" name="rate" label="Shipping & Handling Charge"|gettext size=5 filter=money value=$calculator->configdata.rate|default:'0.03' description='This is added to the cost of the shipping per package'|gettext}
	        </div>
	        <div id="tab2">
                {control type="checkbox" name="shipping_carriers[]" label="USPS"|gettext value="USPS" checked=$calculator->configdata.shipping_carriers|default:1}
                {control type="checkbox" name="shipping_carriers[]" label="FedEx"|gettext value="FedEx" checked=$calculator->configdata.shipping_carriers|default:1}
                {control type="checkbox" name="shipping_carriers[]" label="UPS"|gettext value="UPS" checked=$calculator->configdata.shipping_carriers|default:1}
	        </div>
	        <div id="tab3">
                {ecomconfig var=store assign=store}
                {ecomconfig var=storename assign=storename}
                {control type="text" name="shipfrom[company]" label="Company Name"|gettext value=$calculator->configdata.shipfrom.company|default:$storename required=1}
                {control type=tel name="shipfrom[phone]" label="Phone Number"|gettext value=$calculator->configdata.shipfrom.phone|default:$store.phone required=1}
   	            {control type="text" name="shipfrom[street1]" label="Address"|gettext value=$calculator->configdata.shipfrom.street1|default:$store.address1 required=1}
   	            {control type="text" name="shipfrom[street2]" label=" " value=$calculator->configdata.shipfrom.street2|default:$store.address2}
   	            {control type="text" name="shipfrom[city]" label="City"|gettext value=$calculator->configdata.shipfrom.city|default:$store.city required=1}
                {control type="countryregion" name="shipfrom[address]" label="Country/State"|gettext country_default=$calculator->configdata.shipfrom.country|default:$store.country region_default=$calculator->configdata.shipfrom.state|default:$store.state includeblank="-- Choose a State --"|gettext required=1}
   	            {control type="text" name="shipfrom[zip]" label="Zip Code"|gettext size=10 value=$calculator->configdata.shipfrom.zip|default:$store.postalCode required=1}
	        </div>
	        <div id="tab4">
	            {control type="text" name="default_width" label="Standard Box Width (inches)"|gettext size=5 value=$calculator->configdata.default_width}
	            {control type="text" name="default_length" label="Standard Box Length (inches)"|gettext size=5 value=$calculator->configdata.default_length}
	            {control type="text" name="default_height" label="Standard Box Height (inches)"|gettext size=5 value=$calculator->configdata.default_height}
	            {control type="text" name="default_max_weight" label="Default Weight for Box (lbs)"|gettext size=5 value=$calculator->configdata.default_max_weight}
	        </div>
        </div>
    </div>
	{*<div class="loadingdiv">{'Loading'|gettext}</div>*}
    {loading}
</div>

{script unique="editform" yui3mods="exptabs"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.expTabs({srcNode: '#ups-tabs'});
		Y.one('#ups-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
