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

<div id="editproduct" class="module store edit">
    {if $record->id != ""}
        <h1>{"Edit Information for"|gettext} {'Child'|gettext} {$model_name}</h1>
    {else}
        <h1>{"New"|gettext} {$model_name}</h1>
    {/if}
    <div id="mainform">
        {form action=update}
            {control type="hidden" name="id" value=$record->id}
            {if $record->original_id}
      		{control type="hidden" name="original_id" value=$record->original_id}
      		{/if}
            <div id="childtabs" class="">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#general" role="tab" data-toggle="tab"><em>{"General"|gettext}</em></a></li>
                    <li role="presentation"><a href="#pricing" role="tab" data-toggle="tab"><em>{"Pricing, Tax & Discounts"|gettext}</em></a></li>
                    <li role="presentation"><a href="#images" role="tab" data-toggle="tab"><em>{"Images & Files"|gettext}</em></a></li>
                    <li role="presentation"><a href="#quantity" role="tab" data-toggle="tab"><em>{"Quantity"|gettext}</em></a></li>
                    <li role="presentation"><a href="#shipping" role="tab" data-toggle="tab"><em>{"Shipping"|gettext}</em></a></li>
                    <li role="presentation"><a href="#categories" role="tab" data-toggle="tab"><em>{"Categories"|gettext}</em></a></li>
                    <li role="presentation"><a href="#options" role="tab" data-toggle="tab"><em>{"Options"|gettext}</em></a></li>
                    <li role="presentation"><a href="#uifld" role="tab" data-toggle="tab"><em>{"User Input Fields"|gettext}</em></a></li>
                    <li role="presentation"><a href="#active" role="tab" data-toggle="tab"><em>{"Active & Status Settings"|gettext}</em></a></li>
                    <li role="presentation"><a href="#notes" role="tab" data-toggle="tab"><em>{"Notes"|gettext}</em></a></li>
                    <li role="presentation"><a href="#xtrafields" role="tab" data-toggle="tab"><em>{"Extra Fields"|gettext}</em></a></li>
                    <li role="presentation"><a href="#misc" role="tab" data-toggle="tab"><em>{"Misc"|gettext}</em></a></li>
                </ul>
                <div class="tab-content">
                    <div id="general" role="tabpanel" class="tab-pane fade in active">
                        {control type="hidden" name="tab_loaded[general]" value=1}
                        {control type="hidden" name="general[parent_id]" value=$record->parent_id}
                        {control type="hidden" name="general[product_type]" value='childProduct'}
                        {"Parent Product:"|gettext} <a href="{link controller='store' action='edit' id=$record->parent_id}">{$parent->title}</a>
                        {control type="text" name="general[child_rank]" label="Rank"|gettext value=$record->child_rank focus=1}
                        {control type="text" name="general[model]" label="Model # / SKU"|gettext value=$record->model}
                        {control type="text" class="title" name="general[title]" label="Product Name"|gettext value=$record->title}
                        {control type="dropdown" name="general[companies_id]" label="Manufacturer"|gettext includeblank=true frommodel=company value=$record->companies_id}
                        {icon class="manage" controller="company" action="showall" text="Manage Manufacturers"|gettext}
                        {control type="editor" name="general[body]" label="Product Description"|gettext height=250 value=$record->body}
                        {*{control type="textarea" name="summary" label="Product Summary"|gettext rows=3 cols=45 value=$record->summary}*}
                    </div>
                    <div id="pricing" role="tabpanel" class="tab-pane fade">
                        {control type="hidden" name="tab_loaded[pricing]" value=1}
                        {group label="General Pricing"|gettext}
                            <table>
                                <tr>
                                    <td>{control type="text" name="pricing[base_price]" label="Base Price"|gettext value=$record->base_price filter=money}</td>
                                    <td>{control type="text" name="pricing[special_price]" label="Special Price"|gettext value=$record->special_price filter=money}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">{control type="checkbox" name="pricing[use_special_price]" label="Use Special Price"|gettext value=1 checked=$record->use_special_price}</td>
                                </tr>
                            </table>
                        {/group}
                        {group label="Quantity Discounts"|gettext}
                            <blockquote>
                                {"Quantity discounts are discounts that get applied when a customer purchases a certain amount of this product."|gettext}&#160;&#160;
                                {"You can configure how the discounts work by setting the discount rules below."|gettext}
                            </blockquote>
                            <table class="qty-discount">
                                <tr>
                                    <td>{"If a customer purchases more than"|gettext} </td>
                                    <!--td>{control type="dropdown" name="pricing[quantity_discount_num_items_mod]" label=" " items=$record->quantity_discount_items_modifiers value=$record->quantity_discount_num_items}</td-->
                                    <td>{control type="text" name="pricing[quantity_discount_num_items]" label=" " value=$record->quantity_discount_num_items size=3 filter=integer}</td>
                                    <td>{'items, then discount the price by'|gettext}</td>
                                    <td>{control type="text" name="pricing[quantity_discount_amount]" label=" " value=$record->quantity_discount_amount size=3 filter=decimal}
                                    <td>{control type="dropdown" name="pricing[quantity_discount_amount_mod]" label=" " items=$record->quantity_discount_amount_modifiers value=$record->quantity_discount_amount_mod}</td>
                                </tr>
                                <tr>
                                    <td colspan="6">{control type="checkbox" name="pricing[quantity_discount_apply]" label="Only apply discount to the items over the discount limit"|gettext value=1 checked=$record->quantity_discount_apply}</td>
                                </tr>
                            </table>
                        {/group}
                        {group label="Tax Class"|gettext}
                            {control type="dropdown" name="pricing[tax_class_id]" label="" frommodel=taxclass key=id display=name includeblank="-- No Tax Required --"|gettext value=$record->tax_class_id}
                            {icon controller="tax" action="manage" text="Manage Taxes"|gettext}
                        {/group}
                    </div>
                    <div id="images" role="tabpanel" class="tab-pane fade">
                        <h2>{'Images'|gettext} {'are inherited from this product\'s parent.'|gettext}</h2>
                        {*<div id="imagefunctionality">*}
                            {*{control type="text" name="image_alt_tag" label="Image Alt Tag"|gettext value=$record->image_alt_tag description="The image alt tag will be created dynamically by the system, however you may supply a custom one here:"|gettext}*}
                            {*{control type=radiogroup columns=2 name="main_image_functionality" label="Main Image Functionality"|gettext items="Single Image,Image with Swatches"|gettxtlist values="si,iws"  default=$record->main_image_functionality|default:"si"}*}
                            {*<div id="si-div" class="imngfuncbody">*}
                                {*{control type=files name=mainimages label="Main Product Images"|gettext subtype="mainimage" accept="image/*" value=$record->expFile}*}
                                {*{control type=files name=mainthumb label="Product Thumbnail Images"|gettext subtype="mainthumbnail" accept="image/*" value=$record->expFile description="If no image is provided to use as a thumbnail, one will be generated from the main image."|gettext}*}
                            {*</div>*}
                            {*<div id="iws-div" class="imngfuncbody" style="display:none;">*}
                                {*<table border="0" cellspacing="0" cellpadding="1" width="100%">*}
                                    {*<tr>*}
                                        {*<th width="50%">{"Image"|gettext}</th>*}
                                        {*<th width="50%">{"Color/Pattern Swatch"|gettext}</th>*}
                                    {*</tr>*}
                                    {*<tr>*}
                                        {*<td style="vertical-align:top;">*}
                                            {*{control type=files name=imagesforswatches label="Images"|gettext subtype="imagesforswatches" accept="image/*" value=$record->expFile}*}
                                        {*</td>*}
                                        {*<td style="vertical-align:top;">*}
                                            {*{control type=files name=swatchimages label="Swatches"|gettext subtype="swatchimages" accept="image/*" value=$record->expFile}*}
                                        {*</td>*}
                                    {*</tr>*}
                                {*</table>*}
                            {*</div>*}
                            {*<hr />*}
                            {*<div class="additional-images">*}
                                {*{control type=files name=images label="Additional Images"|gettext subtype="images" accept="image/*" value=$record->expFile description="Additional images to show for your product"|gettext}*}
                            {*</div>*}
                            {*{control type=files name=brochures label="Additional File Attachments"|gettext subtype="brochures" value=$record->expFile description="Attach Product Brochures, Docs, Manuals, etc."|gettext}*}
                        {*</div>*}

                        {*script unique="mainimagefunctionality"}
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
                        {/script*}
                    </div>
                    <div id="quantity" role="tabpanel" class="tab-pane fade">
                        {control type="hidden" name="tab_loaded[quantity]" value=1}
                        {control type="text" name="quantity[quantity]" label="Quantity in stock"|gettext value=$record->quantity}
                        {control type="text" name="quantity[minimum_order_quantity]" label="Minimum order quantity"|gettext value=$record->minimum_order_quantity|default:1}
                        {control type="text" name="quantity[multiple_order_quantity]" label="Must be ordered in multiples of"|gettext value=$record->multiple_order_quantity|default:1} {* FIXME not in child product*}
                        {*{control type="checkbox" name="quantity[allow_partial]" label="Allow partial quantities?"|gettext value=1 checked=$record->allow_partial}*}
                        {*{control type="checkbox" name="quantity[is_hidden]" label="Hide Product"|gettext value=$record->is_hidden}*}
                        {control type="radiogroup" name="quantity[availability_type]" label="Quantity Display"|gettext items=$record->quantity_display default=$record->availability_type|default:0}
                        {control type="textarea" name="quantity[availability_note]" label="* "|cat:("Note to display per above selection"|gettext) rows=5 cols=45 value=$record->availability_note}
                    </div>
                    <div id="shipping" role="tabpanel" class="tab-pane fade">
                        {control type="hidden" name="tab_loaded[shipping]" value=1}
                        {control type="checkbox" name="shipping[no_shipping]" label='This item doesn\'t require shipping'|gettext value=1 checked=$record->no_shipping}
                        {control type="dropdown" name="shipping[required_shipping_calculator_id]" id="required_shipping_calculator_id" label="Required Shipping Service"|gettext includeblank="-- Select a shipping service --" items=$shipping_services value=$record->required_shipping_calculator_id}
                        {foreach from=$shipping_methods key=calcid item=methods name=sm}
                            <div id="dd-{$calcid}" class="methods" style="display:none;">
                                {control type="dropdown" name="required_shipping_methods[`$calcid`]" label="Shipping Methods"|gettext items=$methods value=$record->required_shippng_method includeblank='No specific method'|gettext}
                            </div>
                        {/foreach}
                        {icon controller="shipping" action="manage" text="Manage Shipping Options"|gettext}
                        {control type="text" name="shipping[weight]" label="Item Weight (in pounds)"|gettext size=4 filter=decimal value=$record->weight}
                        {control type="text" name="shipping[width]" label="Width (in inches)"|gettext size=4 filter=decimal value=$record->width}
                        {control type="text" name="shipping[height]" label="Height (in inches)"|gettext size=4 filter=decimal value=$record->height}
                        {control type="text" name="shipping[length]" label="Length (in inches)"|gettext size=4 filter=decimal value=$record->length}
                        {control type="text" name="shipping[surcharge]" label="Freight Surcharge"|gettext size=4 filter=decimal value=$record->surcharge description='per item'|gettext}
                    </div>
                    <div id="categories" role="tabpanel" class="tab-pane fade">
                        <h2>{'Categories'|gettext} {'are inherited from this product\'s parent.'|gettext}</h2>
                    </div>
                    <div id="options" role="tabpanel" class="tab-pane fade">
                        <h2>{'Options'|gettext} {'are inherited from this product\'s parent.'|gettext}</h2>
                    </div>
                    <div id="uifld" role="tabpanel" class="tab-pane fade">
                        <h2>{'User Input Fields'|gettext} {'are inherited from this product\'s parent.'|gettext}</h2>
                    </div>
                    <div id="active" role="tabpanel" class="tab-pane fade">
                        {control type="hidden" name="tab_loaded[status]" value=1}
                        <h2>{"Active/Inactive"|gettext}</h2>
                        {control type="radiogroup" name="status[active_type]" label=" " items=$record->active_display default=$record->active_type|default:0}
                        <h2>{"Status"|gettext}</h2>
                        {control type="dropdown" name="status[product_status_id]" label=" " frommodel=product_status items=$status_display orderby=rank value=$record->product_status_id}
                        {icon controller="product_status" action="manage" text="Manage Product Statuses"|gettext}
                    </div>
                    <div id="notes" role="tabpanel" class="tab-pane fade">
                        <h2>{"Notes"|gettext}</h2>
                        {simplenote content_type="product" content_id=$record->id require_login="1" require_approval="0" require_notification="0" tab="notes"}
                    </div>
                     <div id="xtrafields" role="tabpanel" class="tab-pane fade">
                         {control type="hidden" name="tab_loaded[extrafields]" value=1}
                         <h2>{"Extra Fields"|gettext}</h2>
                         {'Extra field names are defined in this product\'s parent.  You may enter the field values for this product here.'|gettext}
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
                                {br}{br}<em>{"There are no extra fields defined for this item."|gettext}</em>
                            {/if}
                        </table>
                    </div>
                    <div id="misc" role="tabpanel" class="tab-pane fade">
                        {control type="hidden" name="tab_loaded[misc]" value=1}
                        <h2>{'Miscellaneous Information'|gettext}</h2>
                        {control type="text" name="misc[warehouse_location]" label="Warehouse Location"|gettext value=$record->warehouse_location}
                    </div>
                </div>
            </div>
            {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
            {loading}
            {control type="buttongroup" submit="Save Product"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>

{script unique="editform" yui3mods="node"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        var switchMethods = function () {
            var dd = Y.one('#required_shipping_calculator_id');
            var ddval = dd.get('value');
            if (ddval != '') {
                var methdd = Y.one('#dd-'+ddval);
            }
            var otherdds = Y.all('.methods');

            otherdds.each(function (odds) {
                if (odds.get('id') == 'dd-'+ddval) {
                    odds.setStyle('display', 'block');
                } else {
                    odds.setStyle('display', 'none');
                }
            });
        }
        switchMethods();
        Y.one('#required_shipping_calculator_id').on('change', switchMethods);
    });
{/literal}
{/script}

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}