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

<blockquote>
{'To setup a PayPal Express Checkout account, visit'|gettext} <a href="https://www.paypal.com/webapps/mpp/merchant" target="_blank">https://www.paypal.com/webapps/mpp/merchant</a>
</blockquote>
<div id="paypal">
    <div id="paypal-tabs" class="yui-navset">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>{'PayPal Express Checkout'|gettext}<br>{'Settings'|gettext}</em></a></li>
	        <li><a href="#tab2"><em>{'Customer'|gettext}<br>{'Confirmations'|gettext}</em></a></li>
	        <li><a href="#tab3"><em>{'Administrator'|gettext}<br>{'Notifications'|gettext}</em></a></li>
        </ul>            
        <div class="yui-content">
	        <div id="tab1">
                {control type="checkbox" name="incontext" label="Enable In-Context Checkout?"|gettext value=1 checked=$calculator->configdata.incontext}
	            {control type="text" name="username" label="API Username"|gettext value=$calculator->configdata.username}
	            {control type="password" name="password" label="API Password"|gettext value=$calculator->configdata.password}
	            {control type="text" name="signature" label="Signature"|gettext value=$calculator->configdata.signature}
	            {control type="radiogroup" name="process_mode" label="Processing Mode"|gettext items="Sale, Authorization, Order"|gettxtlist values="Sale,Authorization,Order" default=$calculator->configdata.process_mode|default:'Sale'}
                <ul>
                    <li><strong>{'Sale'|gettext}</strong> – {'the funds are credited to the merchants account immediately at the end of the checkout flow.'|gettext}</li>
                    <li><strong>{'Authorization'|gettext}</strong> – {'the merchant obtains an authorization (a hold) for the transaction amount and the merchant must then capture the funds against this authorization at a later date. Authorization funds are held for up to 3 days and valid for up to 29 days.'|gettext}</li>
                    <li><strong>{'Order'|gettext}</strong> – {'the merchant does not have a hold on the funds. The merchant must later authorize against the order and then capture the funds.'|gettext}</li>
                    <li><strong style="color:red;">{'Note'|gettext}</strong>! {'There is no interface within Exponent to authorize or reauthorize transactions initiated by the Authorization and Order modes.'|gettext} <a href="https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/authcapture/" target="_blank">{'Using PayPal Authorization & Capture'|gettext}</a></li>
                </ul>
                <hr>
	            {control type="checkbox" name="testmode" label="Enable Sandbox (Test) Mode?"|gettext value=1 checked=$calculator->configdata.testmode}
                <p>{"To test, you must create a developer account and be logged in to"|gettext} <a href="https://developer.paypal.com/" target="_blank">{"PayPal Developer Central"|gettext}</a>,
                {"then enter the Sandbox API and Payment Card Credentials below."|gettext}
                {"Create both a Buyer In-Store and a Seller Test Accounts."|gettext}
                {control type="text" name="testusername" label="Sandbox API Username"|gettext value=$calculator->configdata.testusername}
 	            {control type="password" name="testpassword" label="Sandbox API Password"|gettext value=$calculator->configdata.testpassword}
 	            {control type="text" name="testsignature" label="Sandbox Signature"|gettext value=$calculator->configdata.testsignature}
                </p>
	        </div>
	        <div id="tab2">
	            {control type="checkbox" name="email_customer" label="Send customer an email confirmation?"|gettext value=1 checked=$calculator->configdata.email_customer}
	        </div>
	        <div id="tab3">
	            {control type="checkbox" name="email_admin" label="Send a notification that a new order was received?"|gettext value=1 checked=$calculator->configdata.email_admin}
                {control type=text name="notification_addy" label="Email addresses to send notifications to (comma separated list of email addresses)"|gettext value=$calculator->configdata.notification_addy}
	        </div>
        </div>
    </div>
	{*<div class="loadingdiv">{'Loading'|gettext}</div>*}
    {loading}
</div>

{script unique="paypal-`$id`" jquery="jqueryui"}
{literal}
    $('#paypal-tabs').tabs().next().remove();
{/literal}
{/script}

