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
{script unique="payment-summary" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', 'charts', 'yui2-yahoo-dom-event','yui2-element','yui2-tabview', function(Y) {
	var YAHOO=Y.YUI2;
	var tabView = new YAHOO.widget.TabView('payments');
	
});

(function() {
    YUI().use('charts', function (Y) 
    { 
        var myDataValues = [ 
			[{/literal}{$payments_key}{literal}],
			[{/literal}{$payment_values}{literal}]
        ];
		
		var myTooltip = {
            styles: { 
                backgroundColor: "#333",
                color: "#eee",
                borderColor: "#fff",
                textAlign: "center"
            },
            markerLabelFunction: function(categoryItem, valueItem, itemIndex, series, seriesIndex)
            {
                var msg = "<span style=\"text-decoration:underline\">Total " + 
                categoryItem.axis.get("labelFunction").apply(this, [categoryItem.value, categoryItem.axis.get("labelFormat")]) + 
                " Payment</span><br/><div style=\"margin-top:5px;font-weight:bold\">" + valueItem.axis.get("labelFunction").apply(this, [valueItem.value, {prefix:"$", decimalPlaces:2}]) + "</div>";
                return msg; 
            }
        };
        
        //var areachart     = new Y.Chart({dataProvider:myDataValues, render:"#areachart", type:"area", tooltip: "myTooltip"});
		//var barchart      = new Y.Chart({dataProvider:myDataValues, render:"#barchart", type:"bar", tooltip: "myTooltip"});
		var columnchart   = new Y.Chart({dataProvider:myDataValues, render:"#columnchart", type:"column", tooltip: "myTooltip"});
		//var combochart    = new Y.Chart({dataProvider:myDataValues, render:"#combochart", type:"combo", tooltip: "myTooltip"});
		//var linechart     = new Y.Chart({dataProvider:myDataValues, render:"#linechart", type:"line", tooltip: "myTooltip"});
		//var piechart     = new Y.Chart({dataProvider:myDataValues, render:"#piechart", type:"pie", tooltip: "myTooltip"});
    });
})();
{/literal}
{/script}
	
<div id="payment-summary" class="module administration configure-site exp-skin-tabview">
    
    <h1>Payment Summary</h1>

	<div id="payments" class="yui-navset">
		<ul class="yui-nav">
		<li class="selected"><a href="#tab1"><em>{gettext str="Payment Summary"}</em></a></li>
        <li><a href="#tab2"><em>{gettext str="Column Chart"}</em></a></li>
		<!--li><a href="#tab2"><em>{gettext str="Area Chart"}</em></a></li>
		<li><a href="#tab3"><em>{gettext str="Bar Chart"}</em></a></li>
		<li><a href="#tab4"><em>{gettext str="Column Chart"}</em></a></li>
		<li><a href="#tab5"><em>{gettext str="Combo Chart"}</em></a></li>
		<li><a href="#tab6"><em>{gettext str="Line Chart"}</em></a></li>
		<li><a href="#tab7"><em>{gettext str="Pie Chart"}</em></a></li-->
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
                <div id="columnchart"></div>
            </div>
			<!--div id="tab2">
				<div id="areachart"></div>
			</div>
			<div id="tab3">
				<div id="barchart"></div>
			</div>
			
			<div id="tab5">
				<div id="combochart"></div>
			</div>
			<div id="tab6">
				<div id="linechart"></div>
			</div>
			<div id="tab7">
				<div id="piechart"></div>
			</div-->
		</div>
	</div>
</div>

<div id="tax-summary" class="module administration configure-site exp-skin-tabview">    
    <h1>Tax Summary</h1>  
    <div class="exp-ecom-table exp-skin-table">
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr class="{cycle values='odd,even'}">
                    <th colspan="2">
                        <h1>{gettext str="Tax Summary"}</h1>
                    </th>
                </tr>
            </thead>
            <tbody>                  
                <tr>
                    <th>{$tax_type}</th>
                    <td>{currency_symbol}{$tax_total|number_format:2}</td>
                </tr>               
                
            </tbody>
        </table>                
    </div>
</div>


<style type="text/css">
{literal}
.fullbody #centercol {
    width: 422px;     
}

.exp-skin-table {
    width:400px;
}
.exp-skin-table tbody tr.odd th,
.exp-skin-table tbody tr.odd td {
    border-color:#EBE5D9;
    background:#F7F4EE;
}

#areachart, #barchart, #columnchart, #combochart, #linechart, #piechart {
    margin:10px 10px 10px 10px;
    width:90%;
    max-width: 400px;
    height:400px;
}

{/literal}
</style>