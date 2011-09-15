{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css"}

{/css}
{css unique="report-builder" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/report-builder.css"}

{/css}
<div id="payment-summary" class="module administration configure-site exp-skin-tabview hide">
    
    <h1>Dashboard</h1>
    
    {script unique="payment-summary" yui3mods=1}
    {literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-element','yui2-tabview', function(Y) {
        var YAHOO=Y.YUI2;
        var tabView = new YAHOO.widget.TabView('demo');
        Y.one('#payment-summary').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
    {/literal}
    {/script}

        <div id="demo" class="yui-navset">
            <ul class="yui-nav">
            <li class="selected"><a href="#tab1"><em>{gettext str="Payment Summary"}</em></a></li>
            <li><a href="#tab2"><em>{gettext str="Chart Report"}</em></a></li>
        
            </ul>            
            <div class="yui-content">
                <div id="tab1">
                    <div class="exp-ecom-table exp-skin-table">
						<table border="0" cellspacing="0" cellpadding="0">
							<thead>
								<tr class="{cycle values='odd,even'}">
									<th colspan="2">
										<h1>{gettext str="Payment Summary"}</h1>
									</th>
								</tr>
							</thead>
							<tbody>  
								{foreach from=$payment_summary key=key item=item}
									<tr>
										<th>{$key}</th>
										<td>{currency_symbol}{$item|number_format:2}</td>
									</tr>
								
								{/foreach}
							</tbody>
						</table>
					</div>
                </div>
                <div id="tab2">

                </div>
              
            </div>
        </div>
      
</div>
<div class="loadingdiv">Loading</div>

<style type="text/css">
{literal}
.exp-skin-table tbody tr.odd th,
.exp-skin-table tbody tr.odd td {
    border-color:#EBE5D9;
    background:#F7F4EE;
}
{/literal}
</style>
