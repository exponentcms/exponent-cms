{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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

{css unique="topsearchreport" corecss="tables,admin-global"}
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

<div class="module topsearchquery report">
    <div class="info-header">
        <h2>{$moduletitle|default:"Top"|gettext|cat:" `$limit` "|cat:"Search Queries Report"|gettext}</h2>
    </div>
    {permissions}
        <div class="module-actions">
            {if $permissions.manage}
                {icon class=delete action=delete_search_queries text="Delete Past Queries"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete all past search queries?"|gettext)|cat:"');"}
            {/if}
        </div>
    {/permissions}
	<div id="topsearch" class="yui-navset exp-skin-tabview hide">
		<ul class="yui-nav">
			<li class="selected"><a href="#tab1">{"Top Search"|gettext}</a></li>
			<li><a href="#tab2">{"Chart View"|gettext}</a></li>
		</ul>
		<div id="pane" class="yui-content">
			<div id="tab1">
				<table class="exp-skin-table">
					<thead>
						<tr>
							<th>{"Rank"|gettext}</th>
							<th>{"Term"|gettext}</th>
							<th>% {"of All Searches"|gettext}</th>
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
    {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
    {loading}
</div>

{script unique="topsearch" yui3mods="charts,exptabs"}
{literal}
EXPONENT.YUI3_CONFIG.modules.exptabs = {
    fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
    requires: ['history','tabview','event-custom']
};

var renderIntoTabview,
    handlerArray = [];

YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var tabhistory = Y.expTabs({srcNode: '#topsearch'});
    Y.one('#topsearch').removeClass('hide');
    Y.one('.loadingdiv').remove();

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
        markerLabelFunction: function(categoryItem, valueItem, itemIndex, series, seriesIndex) {
            var msg = document.createElement("div"),
                underlinedTextBlock = document.createElement("span"),
                boldTextBlock = document.createElement("div");
            underlinedTextBlock.style.textDecoration = "underline";
            boldTextBlock.style.marginTop = "5px";
            boldTextBlock.style.fontWeight = "bold";
            underlinedTextBlock.appendChild(document.createTextNode("{/literal}{"Term"|gettext}{literal}: " +
                                            categoryItem.axis.get("labelFunction").apply(this, [categoryItem.value, categoryItem.axis.get("labelFormat")])));
            boldTextBlock.appendChild(document.createTextNode(valueItem.axis.get("labelFunction").apply(this, [valueItem.value, {prefix:"%", decimalPlaces:2}])));
            msg.appendChild(underlinedTextBlock);
            msg.appendChild(document.createElement("br"));
            msg.appendChild(boldTextBlock);
            return msg;
        }
    };

    columnchart = new Y.Chart({
        dataProvider: myDataValues,
    //            render: "#columnchart",
        type: "column",
        tooltip: myTooltip
    });

    Y.Global.on("exptab:switch", function(e){
        if (tabhistory.tabs.indexOf(e.currentTarget)==1) {
            columnchart.render('#columnchart');
        };
    });

    if (tabhistory.history.get('tab')==1) {
        columnchart.render('#columnchart');
    };

    // renderIntoTabview(columnchart, "#columnchart", 1);
});
{/literal}
{/script}
