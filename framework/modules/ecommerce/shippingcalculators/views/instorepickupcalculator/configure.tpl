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

<div id="authcfg">
    <div id="authcfg-tabs" class="yui-navset yui3-skin-sam hide">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>General</em></a></li>
        </ul>            
        <div class="yui-content">
	        <div id="tab1">
	            {control type="text" name="rate" label="In Store Pickup Charge" size=5 filter=money value=$calculator->configdata.rate}
	        </div>
        </div>
    </div>
	<div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{script unique="editform" yui3mods=1}
{literal}
//    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
//        var YAHOO=Y.YUI2;
//        var tabView = new YAHOO.widget.TabView('auth');
//        Y.one('#authcfg').removeClass('hide').next().remove();
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#authcfg-tabs'});
	    tabview.render();
		Y.one('#authcfg-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
