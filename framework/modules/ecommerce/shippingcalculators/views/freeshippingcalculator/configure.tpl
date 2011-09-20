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

{script unique="editform" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
        var YAHOO=Y.YUI2;

        var tabView = new YAHOO.widget.TabView('freeship');
        Y.one('#freeshippingcfg').removeClass('hide').next().remove();
    });
{/literal}
{/script}
