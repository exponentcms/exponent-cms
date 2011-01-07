{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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
        <li class="selected"><a href="#tab1"><em>PayPal Express Checkout<br>Settings</em></a></li>
        <li><a href="#tab3"><em>Customer<br>Confirmations</em></a></li>
        <li><a href="#tab4"><em>Administrator<br>Notifications</em></a></li>
        </ul>            
        <div class="yui-content">
        <div id="tab1">
            {control type="text" name="username" label="API Username" value=$calculator->configdata.username}
            {control type="text" name="password" label="API Password" value=$calculator->configdata.password}
            {control type="text" name="signature" label="API Signature" value=$calculator->configdata.signature}
            {control type="radiogroup" name="process_mode" label="Processing Mode" items="Sale, Authorization, Order" values="Sale,Authorization,Order" default=$calculator->configdata.process_mode}
            {control type="checkbox" name="testmode" label="Enable Test Mode? You must be logged in to <a href='https://developer.paypal.com/'>Developer Central</a>" value=1 checked=$calculator->configdata.testmode}
        </div>
        <div id="tab2">
            {control type="checkbox" name="email_customer" label="Send customer an email confirmation?" value=1 checked=$calculator->configdata.email_customer}
        </div>
        <div id="tab3">
            {control type="checkbox" name="email_admin" label="Send a notication that a new order was received?" value=1 checked=$calculator->configdata.email_admin}
            {control type="text" name="notification_addy" label="Email addresses to send notifications to (comma separated list of email addresses)" value=$calculator->configdata.notification_addy}
        </div>
        </div>
    </div>
</div>
<div class="loadingdiv">Loading</div>
