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


<div id="config" class="module scaffold configure hide exp-skin-tabview">
	{script unique="conf" yuimodules="tabview, element"}
	{literal}
		var tabView = new YAHOO.widget.TabView('tabs');		
        YAHOO.util.Dom.removeClass("config", 'hide');
        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
        YAHOO.util.Dom.setStyle(loading, 'display', 'none');
	{/literal}
	{/script}
	
	{form action=saveconfig}
		<div id="tabs" class="yui-navset">
			<ul class="yui-nav">
			    {foreach from=$views item=tab name=tabs}
			        <li{if $smarty.foreach.tabs.first} class="selected"{/if}>
			            <a href="#tab{$smarty.foreach.tabs.iteration}"><em>{$tab.name}</em></a>
			        </li>
			    {/foreach}
			</ul>            
		    	<div class="yui-content">
		    	    {foreach from=$views item=body name=body}
		    	        <div id="tab{$smarty.foreach.body.iteration}">
		    	            {include file=$body.file}
		    	        </div>
		    	    {/foreach}
			</div>
		</div>
		{control type=buttongroup submit="Save Config" cancel="Cancel"}
	{/form}
</div>
<div class="loadingdiv">Loading</div>
