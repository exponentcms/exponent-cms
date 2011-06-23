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

<div id="freeshippingcfg" class="hide exp-skin-tabview">
    {script unique="authtabs" yuimodules="tabview, element"}
    {literal}
        var tabView = new YAHOO.widget.TabView('freeship');
        YAHOO.util.Dom.removeClass("freeshippingcfg", 'hide');
        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
        YAHOO.util.Dom.setStyle(loading, 'display', 'none');
        
    {/literal}
    {/script}    
    <div id="freeship" class="yui-navset">
        <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>Free Shipping Settings</em></a></li>        
        </ul>            
        <div class="yui-content">
            <div id="tab1">
                {control type="text" name="free_shipping_method_default_name" label="Default Name for this Shipping Method" value=$calculator->configdata.free_shipping_method_default_name}
                {control type="text" name="free_shipping_option_default_name" label="Default Name for the Selectable Shipping Option" value=$calculator->configdata.free_shipping_option_default_name} 
            </div>        
        </div>
    </div>
</div>
<div class="loadingdiv">Loading</div>
