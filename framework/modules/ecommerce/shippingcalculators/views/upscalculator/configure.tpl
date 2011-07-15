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

<div id="authcfg" class="hide exp-skin-tabview">
    {script unique="authtabs" yuimodules="tabview, element"}
    {literal}
        var tabView = new YAHOO.widget.TabView('auth');
        YAHOO.util.Dom.removeClass("authcfg", 'hide');
        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
        YAHOO.util.Dom.setStyle(loading, 'display', 'none');
        
    {/literal}
    {/script}
    
    <div id="auth" class="yui-navset">
        <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>UPS Settings</em></a></li>
        <li><a href="#tab2"><em>Shipping Methods</em></a></li>
        <li><a href="#tab3"><em>My Info</em></a></li>
        <li><a href="#tab4"><em>Shipping Defaults</em></a></li>
        </ul>            
        <div class="yui-content">
        <div id="tab1">
            {control type="text" name="username" label="UPS Username" value=$calculator->configdata.username}
            {control type="text" name="accessnumber" label="Access Number" value=$calculator->configdata.accessnumber}
            {control type="text" name="password" label="Password" value=$calculator->configdata.password}
            {control type="text" name="shipfrom[shipperNumber]" label="Account #" value=$calculator->configdata.shipfrom.shipperNumber}
            {control type="checkbox" name="testmode" label="Enable Test Mode" value=1 checked=$calculator->configdata.testmode}
        </div>
        <div id="tab2">
            {control type="checkbox" name="shipping_methods[]" label="UPS Next Day Air" value="01" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Second Day Air" value="02" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Ground" value="03" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Worldwide Express" value="07" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Worldwide Expedited" value="08" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Standard" value="11" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Three-Day Select" value="12" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="Next Day Air Saver" value="13" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Next Day Air Early AM" value="14" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Worldwide Express Plus" value="54" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Second Day Air AM" value="59" checked=$calculator->configdata.shipping_methods}
            {control type="checkbox" name="shipping_methods[]" label="UPS Saver" value="65" checked=$calculator->configdata.shipping_methods}
        </div>
        <div id="tab3">
            {control type="text" name="shipfrom[name]" label="Company Name" value=$calculator->configdata.shipfrom.name}
            {control type="text" name="shipfrom[phone]" label="Phone Number" value=$calculator->configdata.shipfrom.phone}
            {control type="text" name="shipfrom[address1]" label="Address" value=$calculator->configdata.shipfrom.address1}
            {control type="text" name="shipfrom[address2]" label=" " value=$calculator->configdata.shipfrom.address2}
            {control type="text" name="shipfrom[address3]" label=" " value=$calculator->configdata.shipfrom.address3}
            {control type="text" name="shipfrom[city]" label="City" value=$calculator->configdata.shipfrom.city}
            {control type="state" name="shipfrom[region]" label="State" value=$calculator->configdata.shipfrom.region}
            {control type="text" name="shipfrom[postalCode]" label="Zip Code" size=10 value=$calculator->configdata.shipfrom.postalCode}            
        </div>
        <div id="tab4">
            {control type="text" name="default_width" label="Standard Box Width (inches)" size=5 value=$calculator->configdata.default_width}
            {control type="text" name="default_length" label="Standard Box length (inches)" size=5 value=$calculator->configdata.default_length}
            {control type="text" name="default_height" label="Standard Box Heigth (inches)" size=5 value=$calculator->configdata.default_height}
            {control type="text" name="default_max_weight" label="Default Weight for Box (lbs)" size=5 value=$calculator->configdata.default_max_weight}
        </div>
        </div>
    </div>
</div>
<div class="loadingdiv">Loading</div>
