{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div id="splitcredit">
    <div id="splitcredit-tabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>{'Split Credit Card'|gettext}<br>{'Settings'|gettext}</em></a></li>
	        <li><a href="#tab2"><em>{'Accepted'|gettext}<br>{'Credit Cards'|gettext}</em></a></li>
        </ul>            
        <div class="yui-content">
	        <div id="tab1">
	            {*control type="checkbox" name="email_admin" label="Send a notification that a new order was received?"|gettext value=1 checked=$calculator->configdata.email_admin*}
	            {control type="text" name="notification_addy" label="Email addresses to send notifications to (comma separated list of email addresses)"|gettext value=$calculator->configdata.notification_addy}
	        </div>
	        <div id="tab2">
	            {control type="checkbox" name="accepted_cards[]" label="Master Card" value="MasterCard"|gettext checked=$calculator->configdata.accepted_cards}
	            {control type="checkbox" name="accepted_cards[]" label="Visa" value="VisaCard"|gettext checked=$calculator->configdata.accepted_cards}
	            {control type="checkbox" name="accepted_cards[]" label="American Express" value="AmExCard"|gettext checked=$calculator->configdata.accepted_cards}
	            {control type="checkbox" name="accepted_cards[]" label="Discover Card" value="DiscoverCard"|gettext checked=$calculator->configdata.accepted_cards}
	        </div>
        </div>
    </div>
	<div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{script unique="authtabs" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#splitcredit-tabs'});
		Y.one('#splitcredit-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
