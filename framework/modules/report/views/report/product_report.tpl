{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css" corecss="tree,button,panel"}

{/css}
{css unique="report-builder" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/report-builder.css"}

{/css}

{form controller="report" action="generateProductReport" id="reportform" name="reportform"}
    <div id="create-prod-report" class="module report build-report yui-skin-sam">
        <div id="report-form" class="exp-ecom-table">
            <table border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th>
                            <h1>{"Build a Product Report"|gettext}</h1>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="even">
                          <td>
                            {control type="dropdown" name="product_type" label="A Product Type Of"|gettext size=4 multiple=true includeblank="--Any--"|gettext items=$product_types}
                        </td>
                    </tr>
                    <tr class="odd">
                          <td>
                            {control type="dropdown" name="product_status" label="A Product Status Of"|gettext includeblank="--Any--"|gettext size=4 multiple=true frommodel=product_status}
                        </td>
                    </tr>
                    <tr class="even">
                        <td>
                        <div>{control type="checkbox" name="uncategorized" flip=true label="Uncategorized Products Only"|gettext value=1}  </div>
                            <div class="control">
                                <a href="#showcats" id="showcats" class="{button_style color=black size=small}">{'Select Categories'|gettext}</a>
                            </div>
                            <div>
                                <div id="catpicker" class="exp-skin-panel hide">
                                    <div class="yui3-widget-hd">{'Select Categories'|gettext}</div>
                                    <div class="yui3-widget-bd">
                                        <div style="overflow-y:scroll;height:300px;padding: 10px">
                                            {control type="tagtree" addable=false id="managecats" name="managecats" controller=storeCategory draggable=false menu=false expandonstart=false checkable=true}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {script unique="pickerpopper" yui3mods=1}
                            {literal}
        //                    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-container', function(Y) {
        //                        var YAHOO=Y.YUI2;
        //                        var panel = new YAHOO.widget.Panel("catpicker", { width:"500px", zIndex:10, visible:false, draggable:false, close:true, context:['showcats','tl','tr'] } );
        //                        panel.render('create-prod-report');
        //                        YAHOO.util.Event.on('showcats', 'click', panel.show, panel, true);
        //                        YAHOO.util.Dom.removeClass('catpicker', 'hide');
        //                    });

                            YUI(EXPONENT.YUI3_CONFIG).use('node','panel','dd','dd-plugin', function(Y) {
                                var panel = new Y.Panel({
                                    srcNode      : '#catpicker',
                                    width        : 500,
                                    visible      : false,
                                    zIndex       : 10,
                                    centered     : false,
                                    render       : '#create-prod-report'
                                }).plug(Y.Plugin.Drag);

                                panel.dd.addHandle('.yui3-widget-hd');
                                var panelContainer = Y.one('#catpicker').get('parentNode');
                                panelContainer.addClass('exp-panel-container');
                                Y.one('#catpicker').removeClass('hide');

                                Y.one('#showcats').on('click',function(e){
                                    e.halt();
                                    panel.show();
                                    panel.set('centered',true);
                                    panel.align('#showcats',[Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.TL]);
                                });

                            });
                            {/literal}
                            {/script}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>{control type="checkbox" name="allproducts" flip=true label="Show All Products Types and Categories"|gettext value=1}</td>
                    </tr>
                    <tr class="even">
                        <td>
                            {control type="dropdown" name="company" label="Product company"|gettext|cat:'... ' includeblank="--Any--"|gettext size=4 multiple=true frommodel=company}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>
                            {control type="dropdown" name="product-range-op" label="A Product ID..."|gettext items="Equal to,Less than,Greater than"|gettxtlist values="e,l,g"}
                            {*{control type="text" name="product-range-num" label=" " value=$record->prn class="collapse prn"}*}
                            {control type="text" name="product-range-num" size=8 value=$record->prn class=" prn"}
                        </td>
                    </tr>
                    <tr class="even">
                         <td>
                            {control type="dropdown" name="product-price-op" label="Product Price..."|gettext items="Equal to,Less than,Greater than"|gettxtlist values="e,l,g"}
                            {*{control type="text" name="product-price-num" label=" " value=$record->ppn class="collapse ppn"}*}
                             {control type="text" name="product-price-num" size=8 value=$record->ppn class=" ppn" filter=money}
                        </td>
                    </tr>
                    <tr class="odd">
                        <td>
                            {control type="text" name="pnam" label="Product Name Like"|gettext value=$record->product}
                        </td>

                    </tr>
                    <tr class="even">
                        <td>
                            {control type="text" name="sku" label="Product SKU Like"|gettext value=$record->sku}
                        </td>

                    </tr>

                    <tr class="odd">
                        <td>
                            {*<a id="submit-report" href="#" onclick="document.reportform.submit(); return false;" class="{button_style}"><strong><em>{'Generate Report'|gettext}</em></strong></a>*}
                            {control type="buttongroup" submit="Generate Report"|gettext}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
{/form}
