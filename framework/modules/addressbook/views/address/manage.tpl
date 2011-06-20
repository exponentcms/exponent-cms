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

<div id="storeconfig" class="module ecomconfig configure hide exp-skin-tabview">
    <h1>Address Configuration</h1>
    {script unique="storeconf" yuimodules="tabview, element"}
    {literal}
        var tabView = new YAHOO.widget.TabView('storetabs');     
        
        var url = location.href.split('#');
        if (url[1]) {
            //We have a hash
            var tabHash = url[1];
            var tabs = tabView.get('tabs');
            for (var i = 0; i < tabs.length; i++) {
                if (tabs[i].get('href') == '#' + tabHash) {
                    tabView.set('activeIndex', i);
                    break;
                }
            }
        }
        
        YAHOO.util.Dom.removeClass("storeconfig", 'hide');
        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
        YAHOO.util.Dom.setStyle(loading, 'display', 'none');
	{/literal}
	{/script}
	
	{form action=manage_update}
		<div id="storetabs" class="yui-navset">
			<ul class="yui-nav">
				<li class="selected"><a href="#tab1"><em>Allowed Geographies</em></a></li>                
			</ul>            
	    	<div class="yui-content">
                <div id="tab1">
                    <h2>Geography Settings</h2>                    
                    Select the Countries and States/Provinces below that you would like to show for users creating billing and shipping addresses:
                    <table style="margin-left: 25px;">
                    {foreach from=$countries item=country}                        
                        <tr>
                            <td colspan="2">{control type="checkbox" name="country[`$country->id`]" label=`$country->name` value=1 checked=$country->active}
                            </td>
                            <td>{control type="radio" name="country_default" label="Default Country?" value=$country->id checked=$country->is_default}</td>
                        </tr>                        
                        <div></div>
                        {foreach from=$regions item=region}
                            {if $region->country_id == $country->id}
                                
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>{control type="checkbox" name="region[`$region->id`]" label=`$region->name` value=1 checked=$region->active}</td>
                                        <td>{control type="text" name="region_rank[`$region->id`]" label="Rank:" size="5" value=$region->rank}</td>                                        
                                    </tr>                                
                                <!-- div>
                                    <div style="margin-left: 25px; width:49%;">{control type="checkbox" name="address_state[`$region->id`]" label=`$region->name` value=1 checked=$config.address_allow_admins_all}</div>
                                    <div style="float: right; width:49%;">{control type="text" name="address_state_rank[x]" label=" " size="10" value=$config.invoice_subject}</div>
                                    <div style="clear:both;"></div>
                                </div -->
                            {/if}
                        {foreachelse}
                            No defined regions in this country.
                        {/foreach}
                    {/foreach}
                    </table>
                </div>
            </div>
        </div>
        {control type=buttongroup submit="Save Address Configuration" cancel="Cancel"}
    {/form}
</div>
<div class="loadingdiv">Loading</div>
