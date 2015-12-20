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

<div id="flatrate">
    <div id="flatrate-tabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>{'General Settings'|gettext}</em></a></li>
        </ul>            
        <div class="yui-content">
	        <div id="tab1">
                <blockquote>{'Apply a single shipping rate to every order regardless of size or total cost'|gettext}</blockquote>
	            {control type="text" name="rate" label="Flat Rate Shipping & Handling Charge"|gettext size=5 filter=money value=$calculator->configdata.rate|default:0 description='This single rate applies to the entire order'|gettext}
	            {*{control type="textarea" name="out_of_zone_message" label="Message to Out-of-Zone Buyers"|gettext size=15 value=$calculator->configdata.out_of_zone_message}*}
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
        Y.expTabs({srcNode: '#flatrate-tabs'});
		Y.one('#flatrate-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
