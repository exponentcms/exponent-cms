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

<div id="freeshippingcfg">
    <div id="freeship-tabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>{'General Settings'|gettext}</em></a></li>
        </ul>            
        <div class="yui-content">
            <div id="tab1">
                <blockquote>{'Offering Free Shipping means every order ships for Free'|gettext}</blockquote>
                {control type="text" name="shipping_service_name" label="Default Name for Shipping Service"|gettext value=$calculator->configdata.shipping_service_name|default:'Free'|gettext}
                {control type="text" name="free_shipping_method_default_name" label="Default Name for Shipping Method"|gettext value=$calculator->configdata.free_shipping_method_default_name}
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
        Y.expTabs({srcNode: '#freeship-tabs'});
		Y.one('#freeship-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
