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

<div id="authcfg">
    <div id="authcfg-tabs" class="yui-navset exp-skin-tabview hide">
        <ul class="yui-nav">
	        <li class="selected"><a href="#tab1"><em>{'Authorize.net'|gettext}<br>{'Settings'|gettext}</em></a></li>
	        <li><a href="#tab2"><em>{'Accepted'|gettext}<br>{'Credit Cards'|gettext}</em></a></li>
	        <li><a href="#tab3"><em>{'Customer'|gettext}<br>{'Confirmations'|gettext}</em></a></li>
	        <!--li><a href="#tab4"><em>Administrator<br>Notifications</em></a></li-->
        </ul>            
        <div class="yui-content">
	        <div id="tab1">
	            {control type="text" name="username" label="API Login ID"|gettext value=$calculator->configdata.username}
	            {control type="text" name="transaction_key" label="Transaction Key"|gettext value=$calculator->configdata.transaction_key}
	            {control type="checkbox" name="testmode" label="Enable Test Mode?"|gettext value=1 checked=$calculator->configdata.testmode}
	            {control type="radiogroup" name="process_mode" label="Processing Mode"|gettext items="Authorize and Capture, Authorize Only"|gettextlist values="0,1" default=$calculator->configdata.process_mode}
	        </div>
	        <div id="tab2">
	            {control type="checkbox" name="accepted_cards[]" label="Master Card" value="MasterCard"|gettext checked=$calculator->configdata.accepted_cards}
	            {control type="checkbox" name="accepted_cards[]" label="Visa" value="VisaCard"|gettext checked=$calculator->configdata.accepted_cards}
	            {control type="checkbox" name="accepted_cards[]" label="American Express" value="AmExCard"|gettext checked=$calculator->configdata.accepted_cards}
	            {control type="checkbox" name="accepted_cards[]" label="Discover Card" value="DiscoverCard"|gettext checked=$calculator->configdata.accepted_cards}
	        </div>
	        <div id="tab3">
	            {control type="checkbox" name="email_customer" label="Send customer an email confirmation?"|gettext value=1 checked=$calculator->configdata.email_customer}
	        </div>
	        <!--div id="tab4">
	            {control type="checkbox" name="email_admin" label="Send a notification that a new order was received?"|gettext value=1 checked=$calculator->configdata.email_admin}
	            {control type="text" name="notification_addy" label="Email addresses to send notifications to (comma separated list of email addresses)"|gettext value=$calculator->configdata.notification_addy}
	        </div-->
        </div>
    </div>
	<div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{script unique="authtabs" yui3mods=1}
{literal}
//    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-tabview', function(Y) {
//        var YAHOO=Y.YUI2;
//        var tabView = new YAHOO.widget.TabView('auth');
//        YAHOO.util.Dom.removeClass("authcfg", 'hide');
//        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
//        YAHOO.util.Dom.setStyle(loading, 'display', 'none');
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
		var tabview = new Y.TabView({srcNode:'#authcfg-tabs'});
		tabview.render();
		Y.one('#authcfg-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
