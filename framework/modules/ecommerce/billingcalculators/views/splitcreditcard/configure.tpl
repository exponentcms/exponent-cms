{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
        <li class="selected"><a href="#tab1"><em>Split Credit Card<br>Settings</em></a></li>
        <li><a href="#tab2"><em>Accepted<br>Credit Cards</em></a></li>
        </ul>            
        <div class="yui-content">
        <div id="tab1">
            {*control type="checkbox" name="email_admin" label="Send a notication that a new order was received?" value=1 checked=$calculator->configdata.email_admin*}
            {control type="text" name="notification_addy" label="Email addresses to send notifications to (comma separated list of email addresses)" value=$calculator->configdata.notification_addy}
        </div>
        <div id="tab2">
            {control type="checkbox" name="accepted_cards[]" label="Master Card" value="MasterCard" checked=$calculator->configdata.accepted_cards}
            {control type="checkbox" name="accepted_cards[]" label="Visa" value="VisaCard" checked=$calculator->configdata.accepted_cards}
            {control type="checkbox" name="accepted_cards[]" label="American Express" value="AmExCard" checked=$calculator->configdata.accepted_cards}
            {control type="checkbox" name="accepted_cards[]" label="Discover Card" value="DiscoverCard" checked=$calculator->configdata.accepted_cards}
        </div>
        </div>
    </div>
</div>
<div class="loadingdiv">Loading</div>
