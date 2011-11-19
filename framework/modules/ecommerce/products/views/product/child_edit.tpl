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
        <h1>{"Edit Information for"|gettext} {$modelname}</h1>
    {else}
        <h1>{"New"|gettext} {$modelname}</h1>
    {/if}
    
    {form action=update}
        {control type="hidden" name="id" value=$record->id}
        <div id="demo" class="yui-navset">
            <ul class="yui-nav">
            <li class="selected"><a href="#general"><em>{"General"|gettext}</em></a></li>
            <li><a href="#pricing"><em>{"Pricing, Tax & Discounts"|gettext}</em></a></li>
            <li><a href="#images"><em>{"Images & Files"|gettext}</em></a></li>
            <li><a href="#quantity"><em>{"Quantity"|gettext}</em></a></li>
            <li><a href="#shipping"><em>{"Shipping"|gettext}</em></a></li>
            <li><a href="#categories"><em>{"Categories"|gettext}</em></a></li>
            <li><a href="#options"><em>{"Options"|gettext}</em></a></li>
            <li><a href="#uifld"><em>{"User Input Fields"|gettext}</em></a></li>
            <li><a href="#active"><em>{"Active & Status Settings"|gettext}</em></a></li>
            <li><a href="#notes"><em>{"Notes"|gettext}</em></a></li>
            <li><a href="#xtrafields"><em>{"Extra Fields"|gettext}</em></a></li>
            <li><a href="#misc"><em>{"Misc"|gettext}</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="general">
                    {"Parent Product:"|gettext} <a href="{link controller='store' action='edit' id=$record->parent_id}">{$parent->title}</a>
                    {control type="text" name="child_rank" label="Rank"|gettext value=$record->child_rank}
                    {control type="hidden" name="parent_id" value=$record->parent_id}  
                    {control type="hidden" name="product_type" value='childProduct'}  
                    {control type="text" name="model" label="Model # / SKU"|gettext value=$record->model}
                    {control type="text" class="title" name="title" label="Product Name"|gettext value=$record->title}
                    {control type="dropdown" name="companies_id" label="Manufacturer"|gettext includeblank=true frommodel=company value=$record->companies_id}<a href='{link controller="company" action="manage"}'>Manage Manufacturers</a>{br}
                    {control type="textarea" name="summary" label="Product Summary"|gettext rows=3 cols=45 value=$record->summary}
                    {control type="editor" name="body" label="Product Description"|gettext height=250 value=$record->body}
                    
                </div>
                <div id="pricing">
                    <fieldset>
                    <h2>{"General Pricing"|gettext}</h2>
                        <table>
                        <tr>
                            <td>{control type="text" name="base_price" label="Base Price"|gettext value=$record->base_price filter=money}</td>
                            <td>{control type="text" name="special_price" label="Special Price"|gettext value=$record->special_price filter=money}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{control type="checkbox" name="use_special_price" label="Use Special Price"|gettext value=1 checked=$record->use_special_price}</td>
                        </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                    <h2>{"Quantity Discounts"|gettext}</h2>
                        <p>
                            {"Quantity discounts are discounts that get applied when a customer purchases a certain amount of this product."|gettext}&nbsp;&nbsp;
                            {"You can configure how the discounts work by setting the discount rules below."|gettext}
                        </p>
                        <table class="qty-discount">
                        <tr>
                            <td>{"If a customer purchases more than"|gettext} </td>
                            <!--td>{control type="dropdown" name="quantity_discount_num_items_mod" label=" " items=$record->quantity_discount_items_modifiers value=$record->quantity_discount_num_items}</td-->
                            <td>{control type="text" name="quantity_discount_num_items" label=" " value=$record->quantity_discount_num_items size=3 filter=integer}</td>
                            <td>items, than discount the price by</td>
                            <td>{control type="text" name="quantity_discount_amount" label=" " value=$record->quantity_discount_amount size=3 filter=decimal}
                            <td>{control type="dropdown" name="quantity_discount_amount_mod" label=" " items=$record->quantity_discount_amount_modifiers value=$record->quantity_discount_amount_mod}</td>
                        </tr>
                        <tr>
                            <td colspan="6">{control type="checkbox" name="quantity_discount_apply" label="Only apply discount to the items over the discount limit"|gettext value=1 checked=$record->quantity_discount_apply}</td>
                        </tr>
                        </table>
                    </fieldset>                 
                    <h2>{"Tax Class"|gettext}</h2>
                    {control type="dropdown" name="tax_class_id" label="" frommodel=taxclass key=id display=name includeblank="-- No Tax Required --"|gettext value=$record->tax_class_id}
                </div>
                <div id="images">
                    <div id="imagefunctionality">
                        {"The image alt tag will be created dynamically by the system, however you may supply a custom one here:"|gettext}
                        {control type="text" name="image_alt_tag" label="Image Alt Tag"|gettext value=$record->image_alt_tag}
                        {control type=radiogroup columns=2 name="main_image_functionality" label="Main Image Functionality"|gettext items="Single Image,Image with Swatches"|gettext values="si,iws"  default=$record->main_image_functionality|default:"si"}
                        <div id="si-div" class="imngfuncbody">
                            <h3>{"Single Image"|gettext}</h3>
                            <h4>{"Main Image"|gettext}</h4>
                            {control type=files name=mainimages label="Product Images"|gettext subtype="mainimage" value=$record->expFile}
                            <h4>{"Thumbnail for Main Image"|gettext}</h4>
                            <p>{"If no image is provided to use as a thumbnail, one will be generated from the main image."|gettext}</p>
                            {control type=files name=mainthumb label="Product Images"|gettext subtype="mainthumbnail" value=$record->expFile}
                        </div>
                        <div id="iws-div" class="imngfuncbody" style="display:none;">
                            <table border="0" cellspacing="0" cellpadding="1" width="100%">
                                <tr>
                                    <th width="50%">{"Image"|gettext}</th>
                                    <th width="50%">{"Color/Pattern Swatch"|gettext}</th>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">
                                        {control type=files name=imagesforswatches label="Images"|gettext subtype="imagesforswatches" value=$record->expFile}
                                    </td>
                                    <td style="vertical-align:top;">
                                        {control type=files name=swatchimages label="Swatches"|gettext subtype="swatchimages" value=$record->expFile}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <hr />
                        {br}
                        <h4>{"Additional Images"|gettext}</h4>
                        <p>{"Have additional images to show for your product?"|gettext}</p>
                        
                        <div class="additional-images">
                            {control type=files name=images label="Additional Images"|gettext subtype="images" value=$record->expFile}
                        </div>
                        {br}
                        <h4>{"Additional File Attachments"|gettext}</h4>
                        <p>{"Attach Product Brochures, Docs, Manuals, etc."|gettext}</p>
                        {control type=files name=brochures label="Additional Files"|gettext subtype="brochures" value=$record->expFile}
                    </div>

                    {script unique="mainimagefunctionality"}
                    {literal}
                    YUI(EXPONENT.YUI3_CONFIG).use('node','node-event-simulate', function(Y) {
                        var radioSwitchers = Y.all('#main_image_functionalityControl input[name="main_image_functionality"]');
                        radioSwitchers.on('click',function(e){
                            Y.all(".imngfuncbody").setStyle('display','none');
                            var curdiv = Y.one("#" + e.target.get('value') + "-div");
                            curdiv.setStyle('display','block');
                        });

                        radioSwitchers.each(function(node,k){
                            if(node.get('checked')==true){
                                node.simulate('click');
                            }
                        });
                        
                    });
                    {/literal}
                    {/script}

                </div>
                <div id="quantity">
                    {control type="text" name="quantity" label="Quantity"|gettext value=$record->quantity}
                    {control type="text" name="minimum_order_quantity" label="Minimum order quantity"|gettext value=$record->minimum_order_quantity|default:1}
                    {control type="checkbox" name="allow_partial" label="Allow partial quantities?"|gettext value=1 checked=$record->allow_partial}
                    {control type="checkbox" name="is_hidden" label="Hide Product"|gettext value=$record->is_hidden}
                    {control type="radiogroup" name="availability_type" label="Quantity Display"|gettext items=$record->quantity_display default=$record->availability_type|default:0}
                    {control type="textarea" name="availability_note" label="* "|cat:("Note to display per above selection"|gettext) rows=5 cols=45 value=$record->availability_note}
                </div>
                <div id="shipping">
                    {control type="checkbox" name="no_shipping" label="This item doesn\'t require shipping"|gettext value=1 checked=$record->no_shipping}
                    {control type="dropdown" name="required_shipping_calculator_id" id="required_shipping_calculator_id" label="Required Shipping Service"|gettext includeblank="-- Select a shipping service --" items=$shipping_services value=$record->required_shipping_calculator_id onchange="switchMethods();"}
                    {foreach from=$shipping_methods key=calcid item=methods name=sm}
                        <div id="dd-{$calcid}" class="hide methods">
                        {control type="dropdown" name="required_shipping_methods[`$calcid`]" label="Shipping Methods"|gettext items=$methods value=$record->required_shippng_method}
                        </div>
                    {/foreach}
                    {control type="text" name="weight" label="Item Weight"|gettext size=4 filter=decimal value=$record->weight}
                    {control type="text" name="width" label="Width (in inches)"|gettext size=4 filter=decimal value=$record->width}
                    {control type="text" name="height" label="Height (in inches)"|gettext size=4 filter=decimal value=$record->height}
                    {control type="text" name="length" label="Length (in inches)"|gettext size=4 filter=decimal value=$record->length}
                    {control type="text" name="surcharge" label="Surcharge"|gettext size=4 filter=money value=$record->surcharge}
                </div>
                <div id="categories">
                    <a href='{link controller="storeCategory" action="manage"}'>{"Manage Categories"|gettext}</a>{br}{br}
                    <h2>{"Category is inherited from this product\'s parent."|gettext}</h2>
                </div>
                <div id="options">
                      <h2>{"Options are inherited from this product\'s parent."|gettext}</h2>
                </div>
                <div id="uifld">
                    <h2>{"User Input Fields are inherited from this item\'s parent."|gettext}</h2>
                </div>
                <div id="active">
                    <h2>{"Active/Inactive"|gettext}</h2>
                    {control type="radiogroup" name="active_type" label=" " items=$record->active_display default=$record->active_type|default:0}
                    <h2>{"Status"|gettext}</h2>
                   {control type="dropdown" name="product_status_id" label=" " frommodel=product_status items=$status_display value=$record->product_status_id}
                </div>                
                <div id="notes">
                    <h2>{"Notes"|gettext}</h2>
                    {simplenote content_type="product" content_id=$record->id require_login="1" require_approval="0" require_notification="0" tab="notes"}
                </div>
                 <div id="xtrafields">
                    <h2>{"Extra Fields"|gettext}</h2>
                    {"Extra field names are defined in this product\'s parent.  You may enter the field values for this product here."|gettext}
                    <table> 
                        {if $parent->extra_fields.0.name != '' }
                            <tr>
                                <td>
                                {control type="hidden" name="extra_fields_name[0]" value=$parent->extra_fields.0.name}
                                {control type="text" name="extra_fields_value[0]" label="Value for extra field - '`$parent->extra_fields.0.name`':" value=$record->extra_fields.0.value}</td>
                            </tr>
                            {if $parent->extra_fields.1.name != '' } 
                                <tr>
                                <td>
                                {control type="hidden" name="extra_fields_name[1]" value=$parent->extra_fields.1.name}
                                {control type="text" name="extra_fields_value[1]" label="Value for extra field - '`$parent->extra_fields.1.name`':" value=$record->extra_fields.1.value}</td>
                            </tr>
                            {/if}
                            {if $parent->extra_fields.2.name != '' } 
                                <tr>
                                    <td>
                                    {control type="hidden" name="extra_fields_name[2]" value=$parent->extra_fields.2.name}
                                    {control type="text" name="extra_fields_value[2]" label="Value for extra field - '`$parent->extra_fields.2.name`':" value=$record->extra_fields.2.value}</td>
                                </tr>
                             {/if}
                            {if $parent->extra_fields.3.name != '' } 
                                 <tr>
                                    <td>
                                    {control type="hidden" name="extra_fields_name[3]" value=$parent->extra_fields.3.name}
                                    {control type="text" name="extra_fields_value[3]" label="Value for extra field - '`$parent->extra_fields.3.name`':" value=$record->extra_fields.3.value}</td>
                                </tr>
                            {/if}
                        {else}
                            {br}{br}<i>{"There are no extra fields defined for this item."|gettext}</i>
                        {/if} 
                    </table>
                </div>
                <div id="misc">
                    <h2>{'Miscellaneous Information'|gettext}</h2>
                    {control type="text" name="warehouse_location" label="Warehouse Location"|gettext value=$record->warehouse_location}
                </div>
            </div>
        </div>
        {control type="buttongroup" submit="Save Product"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
<div class="loadingdiv">{'Loading'|gettext}</div>

{script unique="editform" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
        var YAHOO=Y.YUI2;

        var tabView = new YAHOO.widget.TabView('demo');

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

        YAHOO.util.Dom.removeClass("editproduct", 'hide');
        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
        YAHOO.util.Dom.setStyle(loading, 'display', 'none');

        function switchMethods() {
            var dd = YAHOO.util.Dom.get('required_shipping_calculator_id');
            var methdd = YAHOO.util.Dom.get('dd-'+dd.value);

            var otherdds = YAHOO.util.Dom.getElementsByClassName('methods', 'div');

            for(i=0; i<otherdds.length; i++) {
                if (otherdds[i].id == 'dd-'+dd.value) {
                    YAHOO.util.Dom.setStyle(otherdds[i].id, 'display', 'block');
                } else {
                    YAHOO.util.Dom.setStyle(otherdds[i].id, 'display', 'none');
                }

            }
            YAHOO.util.Dom.setStyle(methdd, 'display', 'block');
            //console.debug(methdd);
            //console.debug(dd.value);
        }
        YAHOO.util.Event.onDOMReady(switchMethods);
    });
{/literal}
{/script}
