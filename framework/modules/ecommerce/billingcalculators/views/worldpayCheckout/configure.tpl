{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<p>{'To setup a Worldpay account, visit'|gettext} <a href="http://www.worldpay.com/products/index.php?c=WW" target="_blank">http://www.worldpay.com/products/index.php?c=WW</a></p>
<div id="authcfg">
    <div id="authcfg-tabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>{'Wordpay Checkout'|gettext}<br>{'Settings'|gettext}</em></a></li>
	        <li><a href="#tab3"><em>{'Customer'|gettext}<br>{'Confirmations'|gettext}</em></a></li>
	        <li><a href="#tab4"><em>{'Administrator'|gettext}<br>{'Notifications'|gettext}}</em></a></li>
        </ul>            
        <div class="yui-content">
	        <div id="tab1">
	            {control type="text" name="username" label="API Username"|gettext value=$calculator->configdata.username}
	            {control type="text" name="password" label="API Password"|gettext value=$calculator->configdata.password}
	            {control type="text" name="installationid" label="Installation ID"|gettext value=$calculator->configdata.installationid}
				{control type="dropdown" name="authCurrency" label="Choose currency"|gettext includeblank="-- Select currency --"|gettext default=$calculator->configdata.authCurrency items="GBP (Pounds Sterling),USD (US Dollar)" values="GBP, USD"}
	            {control type="checkbox" name="testmode" label="Enable Test Mode?"|gettext value=1 checked=$calculator->configdata.testmode}
	        </div>
	        <div id="tab2">
	            {control type="checkbox" name="email_customer" label="Send customer an email confirmation?"|gettext value=1 checked=$calculator->configdata.email_customer}
	        </div>
	        <div id="tab3">
	            {control type="checkbox" name="email_admin" label="Send a notication that a new order was received?"|gettext value=1 checked=$calculator->configdata.email_admin}
	            {control type="text" name="notification_addy" label="Email addresses to send notifications to (comma separated list of email addresses)"|gettext value=$calculator->configdata.notification_addy}
	        </div>
        </div>
    </div>
	<div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{script unique="authtabs" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
		var tabview = new Y.TabView({srcNode:'#authcfg-tabs'});
		tabview.render();
		Y.one('#authcfg-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
