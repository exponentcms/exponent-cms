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

<p>{'To setup a PayPal Express Checkout account, visit'|gettext} <a href="https://www.paypal.com/webapps/mpp/merchant" target="_blank">https://www.paypal.com/webapps/mpp/merchant</a></p>
<div id="paypal">
    <div id="paypal-tabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>{'PayPal Express Checkout'|gettext}<br>{'Settings'|gettext}</em></a></li>
	        <li><a href="#tab3"><em>{'Customer'|gettext}<br>{'Confirmations'|gettext}</em></a></li>
	        <li><a href="#tab4"><em>{'Administrator'|gettext}<br>{'Notifications'|gettext}</em></a></li>
        </ul>            
        <div class="yui-content">
	        <div id="tab1">
	            {control type="text" name="username" label="API Username"|gettext value=$calculator->configdata.username}
	            {control type="text" name="password" label="API Password"|gettext value=$calculator->configdata.password}
	            {control type="text" name="signature" label="Signature"|gettext value=$calculator->configdata.signature}
	            {control type="radiogroup" name="process_mode" label="Processing Mode"|gettext items="Sale, Authorization, Order"|gettxtlist values="Sale,Authorization,Order" default=$calculator->configdata.process_mode|default:'Sale'}
                <ul>
                    <li><strong>{'Sale'|gettext}</strong> – {'the funds are credited to the merchants account immediately at the end of the checkout flow.'|gettext}</li>
                    <li><strong>{'Authorization'|gettext}</strong> – {'the merchant obtains an authorization (a hold) for the transaction amount and the merchant then captures the funds against this authorization at a later date. Authorizations are valid for up to 3 days. The fund capture can then be done from the PayPal account.'|gettext}</li>
                    <li><strong>{'Order'|gettext}</strong> – {'the merchant does not have a hold on the funds. The merchant has to authorize against the order and then capture the funds.'|gettext}</li>
                </ul>
                <hr>
	            {control type="checkbox" name="testmode" label="Enable Sandbox (Test) Mode?"|gettext value=1 checked=$calculator->configdata.testmode}
                <p>{"To test, you must create a developer account and be logged in to"|gettext} <a href="https://developer.paypal.com/" target="_blank">{"PayPal Developer Central"|gettext}</a>,
                {"then enter the Sandbox API and Payment Card Credentials below."|gettext}
                {"Create both a Buyer In-Store and a Seller Test Accounts."|gettext}
                {control type="text" name="testusername" label="Sandbox API Username"|gettext value=$calculator->configdata.testusername}
 	            {control type="text" name="testpassword" label="Sandbox API Password"|gettext value=$calculator->configdata.testpassword}
 	            {control type="text" name="testsignature" label="Sandbox Signature"|gettext value=$calculator->configdata.testsignature}
                </p>
	        </div>
	        <div id="tab2">
	            {control type="checkbox" name="email_customer" label="Send customer an email confirmation?"|gettext value=1 checked=$calculator->configdata.email_customer}
	        </div>
	        <div id="tab3">
	            {control type="checkbox" name="email_admin" label="Send a notification that a new order was received?"|gettext value=1 checked=$calculator->configdata.email_admin}
	            {control type="text" name="notification_addy" label="Email addresses to send notifications to (comma separated list of email addresses)"|gettext value=$calculator->configdata.notification_addy}
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
//		var tabview = new Y.TabView({srcNode:'#paypal-tabs'});
//		tabview.render();
        Y.expTabs({srcNode: '#paypal-tabs'});
		Y.one('#paypal-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
