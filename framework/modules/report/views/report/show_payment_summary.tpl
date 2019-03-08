{*
 * Copyright (c) 2004-2019 OIC Group, Inc.
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

{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css" corecss="tables,admin-global"}

{/css}

{css unique="report-builder" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/report-builder.css"}

{/css}

{css unique="show-payment"}
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
{/css}

<div id="payment-summary" class="module report show-payment-summary">
    <h1>{'Payment Summary'|gettext}</h1>
	<div id="payments" class="yui-navset exp-skin-tabview hide">
		<ul class="yui-nav">
            <li class="selected"><a href="#tab1">{"Payment Summary"|gettext}</a></li>
            <li><a href="#tab2">{"Column Chart"|gettext}</a></li>
            <li><a href="#tab3">{"Area Chart"|gettext}</a></li>
            <li><a href="#tab4">{"Bar Chart"|gettext}</a></li>
            <li><a href="#tab5">{"Combo Chart"|gettext}</a></li>
            <li><a href="#tab6">{"Line Chart"|gettext}</a></li>
            <li><a href="#tab7">{"Pie Chart"|gettext}</a></li>
		</ul>
		<div class="yui-content">
			<div id="tab1" class="exp-ecom-table exp-skin-table">
                <table border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th colspan="2">
                                <h1>{"Payment Summary"|gettext}</h1>
                            </th>
                        </tr>
                        <tr>
                            <th>
                                {"Type"|gettext}
                            </th>
                            <th>
                                {"Amount"|gettext}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$payment_summary key=key item=item}
                            <tr class="{cycle values='odd,even'}">
                                <td>{$key}</td>
                                <td>{$item|currency}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
			</div>
            <div id="tab2">
                <div id="columnchart"></div>
            </div>
			<div id="tab3">
				<div id="areachart"></div>
			</div>
			<div id="tab4">
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
			</div>
		</div>
	</div>
</div>

<div id="tax-summary" class="module administration configure-site">
    <h1>{'Tax Summary'|gettext}</h1>
    <div class="exp-ecom-table exp-skin-table">
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="2">
                        <h1>{"Tax Summary"|gettext}</h1>
                    </th>
                </tr>
                <tr>
                    <th>
                        {"Tax Zone"|gettext}
                    </th>
                    <th>
                        {"Amount"|gettext}
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach $taxes as $tax}
                    <tr class="{cycle values='odd,even'}">
                        <td>{$tax.format}</td>
                        <td>{$tax.total|currency}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {loading}
</div>

{script unique="payment-summary" yui3mods="charts,exptabs"}
{literal}
EXPONENT.YUI3_CONFIG.modules.exptabs = {
    fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
    requires: ['history','tabview','event-custom']
};

var renderIntoTabview,
    handlerArray = [];

YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    mytab = Y.expTabs({srcNode: '#payments'});
    var tabhistory = Y.expTabs({srcNode: '#payments'});
    Y.one('#payments').removeClass('hide');
    Y.one('.loadingdiv').remove();

    renderIntoTabview = function(chart, node, index) {
        if(mytab.item(index) === mytab.get("selection")) {
            chart.render(node);
        } else {
            handlerArray[index] = mytab.item(index).after("tab:selectedChange", function(e) {
                if(mytab.item(index) === mytab.get("selection")) {
                    chart.render(node);
                    handlerArray[index].detach();
                }
            });
        }
    }

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
        markerLabelFunction: function(categoryItem, valueItem, itemIndex, series, seriesIndex) {
            var msg = document.createElement("div"),
                underlinedTextBlock = document.createElement("span"),
                boldTextBlock = document.createElement("div");
            underlinedTextBlock.style.textDecoration = "underline";
            boldTextBlock.style.marginTop = "5px";
            boldTextBlock.style.fontWeight = "bold";
            underlinedTextBlock.appendChild(document.createTextNode("{/literal}{"Total"|gettext}{literal}: " +
                                            categoryItem.axis.get("labelFunction").apply(this, [categoryItem.value, categoryItem.axis.get("labelFormat")])));
            boldTextBlock.appendChild(document.createTextNode(valueItem.axis.get("labelFunction").apply(this, [valueItem.value, {prefix:'{/literal}{currency_symbol}{literal}', decimalPlaces:2}])));
            msg.appendChild(underlinedTextBlock);
            msg.appendChild(document.createElement("br"));
            msg.appendChild(boldTextBlock);
            return msg;
        }
    };

    var columnchart   = new Y.Chart({
        dataProvider:myDataValues,
    //        render:"#columnchart",
        type:"column",
        tooltip: myTooltip
    });
    var areachart     = new Y.Chart({dataProvider:myDataValues, type:"area", tooltip: myTooltip});
    var barchart      = new Y.Chart({dataProvider:myDataValues, type:"bar", tooltip: myTooltip});
    var combochart    = new Y.Chart({dataProvider:myDataValues, type:"combo", tooltip: myTooltip});
    var linechart     = new Y.Chart({dataProvider:myDataValues, type:"line", tooltip: myTooltip});
    var piechart      = new Y.Chart({dataProvider:myDataValues, type:"pie", tooltip: myTooltip});

    Y.Global.on("exptab:switch", function(e){
        if (tabhistory.tabs.indexOf(e.currentTarget)==1) {
            columnchart.render('#columnchart');
        };
        if (tabhistory.tabs.indexOf(e.currentTarget)==2) {
            areachart.render('#areachart');
        };
        if (tabhistory.tabs.indexOf(e.currentTarget)==3) {
            barchart.render('#barchart');
        };
        if (tabhistory.tabs.indexOf(e.currentTarget)==4) {
            combochart.render('#combochart');
        };
        if (tabhistory.tabs.indexOf(e.currentTarget)==5) {
            linechart.render('#linechart');
        };
        if (tabhistory.tabs.indexOf(e.currentTarget)==6) {
            piechart.render('#piechart');
        };
    });

//    renderIntoTabview(columnchart, "#columnchart", 1);
    if (tabhistory.history.get('tab')==1) {
        columnchart.render('#columnchart');
    };
//    renderIntoTabview(areachart, "#areachart", 2);
    if (tabhistory.history.get('tab')==2) {
        areachart.render('#areachart');
    };
//    renderIntoTabview(barchart, "#barchart", 3);
    if (tabhistory.history.get('tab')==3) {
        barchart.render('#barchart');
    };
//    renderIntoTabview(combochart, "#combochart", 4);
    if (tabhistory.history.get('tab')==4) {
        combochart.render('#combochart');
    };
//    renderIntoTabview(linechart, "#linechart", 5);
    if (tabhistory.history.get('tab')==5) {
        linechart.render('#linechart');
    };
//    renderIntoTabview(piechart, "#piechart", 6);
    if (tabhistory.history.get('tab')==6) {
        piechart.render('#piechart');
    };

});
{/literal}
{/script}
