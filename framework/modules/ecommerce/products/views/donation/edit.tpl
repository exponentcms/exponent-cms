{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<div id="editproduct" class="module store edit hide exp-skin-tabview">

    {if $record->id != ""}
        <h1>Edit Information for {$record->product_name}</h1>
    {else}
        <h1>New {$record->product_name}</h1>
    {/if}


    {form action=update}
        {control type="hidden" name="id" value=$record->id}
        {control type="hidden" name="product_type" value=$record->product_type}
        
        <div id="demo" class="yui-navset">
            <ul class="yui-nav">
            <li class="selected"><a href="#tab1"><em>General Info</em></a></li>
            <li><a href="#tab2"><em>Pricing</em></a></li>
            <li><a href="#tab3"><em>Files & Images</em></a></li>
            <!--li><a href="#tab4"><em>Quantity Info</em></a></li-->
            <!--li><a href="#tab5"><em>Shipping Info</em></a></li-->
            <li><a href="#tab6"><em>Categories</em></a></li>
            </ul>            
            <div class="yui-content">
            <div id="tab1">
                {control type="text" name="model" label="Model #" value=$record->model}
                {control type="text" name="title" label="Title" value=$record->title}
                {control type="textarea" name="summary" label="Product Summary" rows=3 cols=45 value=$record->summary}
                {control type="editor" name="body" label="Product Description" height=250 value=$record->body}          
            </div>
            <div id="tab2">
                {control type="text" name="base_price" label="Minimum dollar increment" value=$record->base_price filter=money}
            </div>
            <div id="tab3">
                {control type=files name=files subtype=images value=$record->expFile}
            </div>
            <!--div id="tab4">
                {control type="text" name="quantity" label="Quantity" value=$record->quantity}
                {control type="text" name="minimum_order_quantity" label="Minimum order quantity" value=$record->minimum_order_quantity}
                {control type="checkbox" checked=1 name="allow_partial" label="Allow partial quantities?" value=$record->allow_partial}
                {control type="checkbox" name="is_available" label="Is this product available?" value=$record->is_available}
                {control type="text" name="availability_note" label="Note to display when product is not available" value=$record->availability_note}
                {control type="radiogroup" name="availability_type" label="Quantity Display" 
                    items="Unavailable if out of stock.,Available but shown as backordered if out of stock.,Always available even if out of stock.,Show as \"Call for Price\"." 
                    values="0,1,2,3"
                }
            </div-->
            <!--div id="tab5">
                {control type="checkbox" name="no_shipping" label="This item doesn't require shipping" value=$record->no_shipping}
                {control type="text" name="weight" label="Item Weight" value=$record->weight}
            </div-->
            <div id="tab6">
                {control type="tagtree" id="managecats" name="managecats" model="storeCategory" draggable=false checkable=true values=$record->storeCategory}
            </div>
            </div>
        </div>
        {control type="buttongroup" submit="Save Product" cancel="Cancel"}
    {/form}
</div>
<div class="loadingdiv">Loading</div>


{script unique="authtabs" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-tabview', function(Y) {
        var YAHOO=Y.YUI2;
        var tabView = new YAHOO.widget.TabView('demo');
        YAHOO.util.Dom.removeClass("editproduct", 'hide');
        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
        YAHOO.util.Dom.setStyle(loading, 'display', 'none');
    });
{/literal}
{/script}
