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

<div id="authcfg">
    {form action=save_payment_info}
        {control type="hidden" name="id" value=$orderid}    
        <div id="authcfg-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
            <li class="selected"><a href="#tab1"><em>{'Edit Payment Info'|gettext}</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="tab1">
                    {foreach from=$opts item=field key=key}
                        {control type="text" name="result[`$key`]" label=$key value=$field}
                    {/foreach}
                    {control type="buttongroup" submit="Save Payment Info"|gettext cancel="Cancel"|gettext}
                </div>
            </div>
        </div>
	    <div class="loadingdiv">{'Loading'|gettext}</div>
    {/form}
</div>

{script unique="editform" yui3mods=1}
{literal}
//    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
//        var YAHOO=Y.YUI2;
//        var tabView = new YAHOO.widget.TabView('auth');
//        Y.one('#authcfg').removeClass('hide').next().remove();
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#authcfg-tabs'});
	    tabview.render();
		Y.one('#authcfg-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
