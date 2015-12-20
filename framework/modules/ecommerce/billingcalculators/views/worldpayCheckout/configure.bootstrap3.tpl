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
{'To setup a Worldpay account, visit'|gettext} <a href="http://www.worldpay.com/products/index.php?c=WW" target="_blank">http://www.worldpay.com/products/index.php?c=WW</a>
</blockquote>
<div id="worldpay">
    <div id="worldpay-tabs" class="">
        <ul class="nav nav-tabs" role="tablist">
	        <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'Wordpay Checkout'|gettext}<br>{'Settings'|gettext}</em></a></li>
	        <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Customer'|gettext}<br>{'Confirmations'|gettext}</em></a></li>
	        <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{'Administrator'|gettext}<br>{'Notifications'|gettext}}</em></a></li>
        </ul>            
        <div class="tab-content">
	        <div id="tab1" role="tabpanel" class="tab-pane fade in active">
	            {control type="text" name="username" label="API Username"|gettext value=$calculator->configdata.username}
	            {control type="password" name="password" label="API Password"|gettext value=$calculator->configdata.password}
	            {control type="text" name="installationid" label="Installation ID"|gettext value=$calculator->configdata.installationid}
				{*{control type="dropdown" name="authCurrency" label="Choose currency"|gettext includeblank="-- Select currency --"|gettext default=$calculator->configdata.authCurrency items="GBP (Pounds Sterling),USD (US Dollar)"|gettxtlist values="GBP, USD"}*}
	            {control type="checkbox" name="testmode" label="Enable Test Mode?"|gettext value=1 checked=$calculator->configdata.testmode}
	        </div>
	        <div id="tab2" role="tabpanel" class="tab-pane fade">
	            {control type="checkbox" name="email_customer" label="Send customer an email confirmation?"|gettext value=1 checked=$calculator->configdata.email_customer}
	        </div>
	        <div id="tab3" role="tabpanel" class="tab-pane fade">
	            {control type="checkbox" name="email_admin" label="Send a notification that a new order was received?"|gettext value=1 checked=$calculator->configdata.email_admin}
                {control type=text name="notification_addy" label="Email addresses to send notifications to (comma separated list of email addresses)"|gettext value=$calculator->configdata.notification_addy}
	        </div>
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