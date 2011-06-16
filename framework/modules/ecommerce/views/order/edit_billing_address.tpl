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
    {form action=save_payment_info}
        {control type="hidden" name="id" value=$orderid}    
        <div id="auth" class="yui-navset">
            <ul class="yui-nav">
            <li class="selected"><a href="#tab1"><em>Edit Payment Info</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="tab1">
                    {foreach from=$opts item=field key=key}
                        {control type="text" name="result[`$key`]" label=$key value=$field}
                    {/foreach}
                    
                    {control type="buttongroup" submit="Save Payment Info" cancel="Cancel"}
                
                </div>
            </div>
        </div>
    {/form}
</div>
<div class="loadingdiv">Loading</div>