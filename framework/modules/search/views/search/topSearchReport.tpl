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

{css unique="topsearchreport" corecss="tables"}
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

    #columnchart {
        margin:10px 10px 10px 10px;

        height:400px;
    }
{/literal}
{/css}

<div class="module topsearchquery report exp-skin-tabview">
    <div class="info-header">
        <h1>{$moduletitle|default:"Top"|gettext|cat:" `$limit` "|cat:("Search Queries Report"|gettext)}</h1>
    </div>
	<div id="topsearch" class="yui-navset">
		<ul class="yui-nav">
			<li class="selected"><a href="#tab1"><em>{"Top Search"|gettext}</em></a></li>
			<li><a href="#tab2"><em>{"Chart View"|gettext}</em></a></li>
		</ul>    
		<div class="yui-content">
			<div id="tab1">
				<table class="exp-skin-table">
					<thead>
						<tr>
							<th>{"Rank"|gettext}</th>
							<th>{"Term"|gettext}</th>
							<th>{"% of All Searches"|gettext}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$records item=query name=listings}
						<tr class="{cycle values='odd,even'}">
							<td>{counter}</td>
							<td>{$query->query}</td>
							<td>{(($query->cnt / $total)*100)|number_format:2} %</td>
						</tr>
						{foreachelse}
							<td colspan="3">{"No Search Query Data"|gettext}</td>
						{/foreach}
					</tbody>
				</table>
			</div>
			<div id="tab2">
                <div id="columnchart"></div>
            </div>
		</div>
	</div>
</div>

{script unique="topsearch" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', 'charts', 'yui2-yahoo-dom-event','yui2-element','yui2-tabview', function(Y) {
	var YAHOO=Y.YUI2;
	var tabView = new YAHOO.widget.TabView('topsearch');
});

(function() {
    YUI().use('charts', function (Y) 
    { 
        var myDataValues = [ 
			[{/literal}{$records_key}{literal}],
			[{/literal}{$records_values}{literal}]
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
                var msg = "<span style=\"text-decoration:underline\">{/literal}{"Total"|gettext}{literal} " +
                categoryItem.axis.get("labelFunction").apply(this, [categoryItem.value, categoryItem.axis.get("labelFormat")]) + 
                " {/literal}{"Payment"|gettext}{literal}</span><br/><div style=\"margin-top:5px;font-weight:bold\">" + valueItem.axis.get("labelFunction").apply(this, [valueItem.value, {prefix:"%", decimalPlaces:2}]) + "</div>";
                return msg; 
            }
        };
        
		var columnchart   = new Y.Chart({dataProvider:myDataValues, render:"#columnchart", type:"column", tooltip: "myTooltip"});
    });
})();

{/literal}
{/script}
