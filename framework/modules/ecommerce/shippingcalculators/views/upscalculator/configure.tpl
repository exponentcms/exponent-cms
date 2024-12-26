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

<div id="ups">
    <blockquote>
        {'To setup a UPS account, visit this page'|gettext} <a href="https://www.ups.com/upsdeveloperkit" target="_blank">https://www.ups.com/upsdeveloperkit</a>
        <ul>
            <li>{'Follow the \'How to Get Started\' steps to Register, Log-in, and Request an Access Key'|gettext}</li>
        </ul>
    </blockquote>
    <div id="ups-tabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>{'General Settings'|gettext}</em></a></li>
	        <li><a href="#tab2"><em>{'Shipping Methods'|gettext}</em></a></li>
	        <li><a href="#tab3"><em>{'Shipping Origin'|gettext}</em></a></li>
	        <li><a href="#tab4"><em>{'Packaging Defaults'|gettext}</em></a></li>
        </ul>
        <div class="yui-content">
	        <div id="tab1">
	            {control type="text" name="username" label="UPS Username"|gettext value=$calculator->configdata.username required=1}
                {control type="password" name="password" label="Password"|gettext value=$calculator->configdata.password required=1}
                {control type="text" name="shipfrom[shipperNumber]" label="Account #"|gettext value=$calculator->configdata.shipfrom.shipperNumber required=1}
	            {control type="text" name="accessnumber" label="Access Key"|gettext value=$calculator->configdata.accessnumber required=1}
	            {control type="checkbox" name="testmode" label="Enable Test Mode"|gettext value=1 checked=$calculator->configdata.testmode}
	        </div>
	        <div id="tab2">
                {control type="checkbox" name="negotiated_rate" label="Use Negotiated Rate"|gettext value=1 checked=$calculator->configdata.negotiated_rate description="You have contracted a discounted rate"|gettext}
                {foreach $calculator->configdata.shipping_methods as $key=>$method}
                    {$shipping_methods[$method] = true}
                {/foreach}
                {group label="Methods"|gettext}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Next Day Air"|gettext value="01" checked=$shipping_methods['01']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Second Day Air"|gettext value="02" checked=$shipping_methods['02']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Ground"|gettext value="03" checked=$shipping_methods['03']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Worldwide Express"|gettext value="07" checked=$shipping_methods['07']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Worldwide Expedited"|gettext value="08" checked=$shipping_methods['08']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Standard"|gettext value="11" checked=$shipping_methods['11']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Three-Day Select"|gettext value="12" checked=$shipping_methods['12']}
                    {control type="checkbox" name="shipping_methods[]" label="Next Day Air Saver"|gettext value="13" checked=$shipping_methods['13']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Next Day Air Early AM"|gettext value="14" checked=$shipping_methods['14']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Worldwide Express Plus"|gettext value="54" checked=$shipping_methods['54']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Second Day Air AM"|gettext value="59" checked=$shipping_methods['59']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Saver"|gettext value="65" checked=$shipping_methods['65']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Access Point Economy"|gettext value="65" checked=$shipping_methods['70']}
                    {control type="checkbox" name="shipping_methods[]" label="UPS Sure Post"|gettext value="65" checked=$shipping_methods['93']}
                {/group}
	        </div>
	        <div id="tab3">
                {ecomconfig var=store assign=store}
                {ecomconfig var=storename assign=storename}
                {control type="text" name="shipfrom[name]" label="Company Name"|gettext value=$calculator->configdata.shipfrom.name|default:$storename}
                {control type=tel name="shipfrom[phone]" label="Phone Number"|gettext value=$calculator->configdata.shipfrom.phone|default:$store.phone}
   	            {control type="text" name="shipfrom[address1]" label="Address"|gettext value=$calculator->configdata.shipfrom.address1|default:$store.address1 required=1}
   	            {control type="text" name="shipfrom[address2]" label=" " value=$calculator->configdata.shipfrom.address2|default:$store.address2}
                {control type="text" name="shipfrom[address3]" label=" " value=$calculator->configdata.shipfrom.address3}
   	            {control type="text" name="shipfrom[city]" label="City"|gettext value=$calculator->configdata.shipfrom.city|default:$store.city required=1}
                {control type="countryregion" name="shipfrom[address]" label="Country/State"|gettext country_default=$calculator->configdata.shipfrom.country|default:$store.country region_default=$calculator->configdata.shipfrom.state|default:$store.state includeblank="-- Choose a State --"|gettext required=1}
   	            {control type="text" name="shipfrom[postalCode]" label="Zip Code"|gettext size=10 value=$calculator->configdata.shipfrom.postalCode|default:$store.postalCode required=1}
	        </div>
	        <div id="tab4">
                {control type="text" name="default_height" label="Standard Box Height (inches)"|gettext size=5 value=$calculator->configdata.default_height}
	            {control type="text" name="default_width" label="Standard Box Width (inches)"|gettext size=5 value=$calculator->configdata.default_width}
	            {control type="text" name="default_length" label="Standard Box Length (inches)"|gettext size=5 value=$calculator->configdata.default_length}
	            {control type="text" name="default_max_weight" label="Default Weight for Box (lbs)"|gettext size=5 value=$calculator->configdata.default_max_weight}
	        </div>
        </div>
    </div>
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
