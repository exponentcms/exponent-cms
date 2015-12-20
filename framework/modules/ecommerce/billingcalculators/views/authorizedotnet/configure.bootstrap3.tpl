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
{'To setup a Authorize.Net account, visit'|gettext} <a href="http://www.authorize.net/" target="_blank">http://www.authorize.net/</a>
</blockquote>
<div id="authorizenet">
    <div id="authorizenet-tabs" class="">
        <ul class="nav nav-tabs" role="tablist">
	        <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'Authorize.net'|gettext}<br>{'Settings'|gettext}</em></a></li>
	        <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Accepted'|gettext}<br>{'Credit Cards'|gettext}</em></a></li>
	        <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{'Customer'|gettext}<br>{'Confirmations'|gettext}</em></a></li>
	        <!--li><a href="#tab4"><em>Administrator<br>Notifications</em></a></li-->
        </ul>            
        <div class="tab-content">
	        <div id="tab1" role="tabpanel" class="tab-pane fade in active">
	            {control type="text" name="username" label="API Login ID"|gettext value=$calculator->configdata.username}
	            {control type="text" name="transaction_key" label="Transaction Key"|gettext value=$calculator->configdata.transaction_key}
	            {control type="checkbox" name="testmode" label="Enable Test Mode?"|gettext value=1 checked=$calculator->configdata.testmode}
	            {control type="radiogroup" name="process_mode" label="Processing Mode"|gettext items="Authorize and Capture, Authorize Only"|gettxtlist values="0,1" default=$calculator->configdata.process_mode|default:0}
	        </div>
	        <div id="tab2" role="tabpanel" class="tab-pane fade">
	            {control type="checkbox" name="accepted_cards[]" label="Master Card" value="MasterCard"|gettext checked=$calculator->configdata.accepted_cards}
	            {control type="checkbox" name="accepted_cards[]" label="Visa" value="VisaCard"|gettext checked=$calculator->configdata.accepted_cards}
	            {control type="checkbox" name="accepted_cards[]" label="American Express" value="AmExCard"|gettext checked=$calculator->configdata.accepted_cards}
	            {control type="checkbox" name="accepted_cards[]" label="Discover Card" value="DiscoverCard"|gettext checked=$calculator->configdata.accepted_cards}
	        </div>
	        <div id="tab3" role="tabpanel" class="tab-pane fade">
	            {control type="checkbox" name="email_customer" label="Send customer an email confirmation?"|gettext value=1 checked=$calculator->configdata.email_customer}
	        </div>
	        <!--div id="tab4">
	            {control type="checkbox" name="email_admin" label="Send a notification that a new order was received?"|gettext value=1 checked=$calculator->configdata.email_admin}
	            {control type=text name="notification_addy" label="Email addresses to send notifications to (comma separated list of email addresses)"|gettext value=$calculator->configdata.notification_addy}
	        </div-->
        </div>
    </div>
	{*<div class="loadingdiv">{'Loading'|gettext}</div>*}
	{loading}
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}