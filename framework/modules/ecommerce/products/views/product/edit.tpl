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
 
 {css unique="product-edit" link="`$asset_path`css/product_edit.css" corecss="tree"}

 {/css}
 
<div id="editproduct" class="module store edit hide exp-skin-tabview">
    {if $record->id != ""}
        <h1>Edit Information for {$modelname}</h1>
    {else}
        <h1>New {$modelname}</h1>
    {/if}
    

    {form action=update}
        {control type="hidden" name="id" value=$record->id}
		<!-- if it copied -->
		{if $record->original_id}
		{control type="hidden" name="original_id" value=$record->original_id}
		{/if}
        <div id="demo" class="yui-navset">
            <ul class="yui-nav">
            <li class="selected"><a href="#general"><em>General</em></a></li>
            <li><a href="#pricing"><em>Pricing, Tax & Discounts</em></a></li>
            <li><a href="#images"><em>Images & Files</em></a></li>
            <li><a href="#quantity"><em>Quantity</em></a></li>
            <li><a href="#shipping"><em>Shipping</em></a></li>
            <li><a href="#categories"><em>Categories</em></a></li>
            <li><a href="#options"><em>Options</em></a></li>
            <li><a href="#featured"><em>Featured</em></a></li>
            <li><a href="#relprod"><em>Related Products</em></a></li>
            <li><a href="#uifld"><em>User Input Fields</em></a></li>
            <li><a href="#active"><em>Active & Status Settings</em></a></li>            
            <li><a href="#meta"><em>Meta Info</em></a></li>
            <li><a href="#notes"><em>Notes</em></a></li>
            <li><a href="#xtrafields"><em>Extra Fields</em></a></li>      
			<li><a href="#skus"><em>SKUS/Model</em></a></li>
            <li><a href="#misc"><em>Misc</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="general">
                    {control type="hidden" name="parent_id" value=$record->parent_id}   
                    {control type="text" name="model" label="Model # / SKU" value=$record->model}
                    {control type="text" class="title" name="title" label="Product Name" value=$record->title|escape:"htmlall"}
                    {control type="dropdown" name="companies_id" label="Manufacturer" includeblank=true frommodel=company value=$record->companies_id}<a href='{link controller="company" action="manage"}'>Manage Manufacturers</a>{br}
                    {control type="textarea" name="summary" label="Product Summary" rows=5 cols=85 value=$record->summary}
                    {control type="editor" name="body" label="Product Description" height=450 value=$record->body}
                    {control type="text" class="title" name="feed_title" label="Product Title for Data Feeds" value=$record->feed_title}
                    {control type="textarea" name="feed_body" label="Product Description for Data Feeds (Description ONLY! - no HTML, no promotional language, no email addresses, phone numbers, or references to this website.)" rows=5 cols=85 value=$record->feed_body}
                    {if $product_types}
					{foreach from=$product_types key=key item=item}
						{control type="text" class="title" name="`$item`" label="`$key` Product Type" value=$record->$item}
					{/foreach}
                    {/if}
			   </div>
                <div id="pricing">
                    <fieldset>
                    <h2>General Pricing</h2>
                        <table>
                        <tr>
                            <td>{control type="text" name="base_price" label="Base Price" value=$record->base_price filter=money}</td>
                            <td>{control type="text" name="special_price" label="Special Price" value=$record->special_price filter=money}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{control type="checkbox" name="use_special_price" label="Use Special Price" value=1 checked=$record->use_special_price}</td>
                        </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                    <h2>Quantity Discounts</h2>
                        <p>
                            Quantity discounts are discounts that get applied when a customer purchases a certain 
                            amount of this product. You can configure how the discounts work by setting the discount
                            rules below. 
                        </p>
                        <table class="qty-discount">
                        <tr>
                            <td>If a customer purchases more than </td>
                            <!--td>{control type="dropdown" name="quantity_discount_num_items_mod" label=" " items=$record->quantity_discount_items_modifiers value=$record->quantity_discount_num_items}</td-->
                            <td>{control type="text" name="quantity_discount_num_items" label=" " value=$record->quantity_discount_num_items size=3 filter=integer}</td>
                            <td>items, than discount the price by</td>
                            <td>{control type="text" name="quantity_discount_amount" label=" " value=$record->quantity_discount_amount size=3 filter=decimal}
                            <td>{control type="dropdown" name="quantity_discount_amount_mod" label=" " items=$record->quantity_discount_amount_modifiers value=$record->quantity_discount_amount_mod}</td>
                        </tr>
                        <tr>
                            <td colspan="6">{control type="checkbox" name="quantity_discount_apply" label="Only apply discount to the items over the discount limit" value=1 checked=$record->quantity_discount_apply}</td>
                        </tr>
                        </table>
                    </fieldset>                 
                    <h2>Tax Class</h2>
                    {control type="dropdown" name="tax_class_id" label="" frommodel=taxclass key=id display=name includeblank="-- No Tax Required --" value=$record->tax_class_id|default:1}
                </div>
                <div id="images">
                    <div id="imagefunctionality">              
                         The image alt tag will be created dynamically by the system, however you may supply a custom one here:
                         {control type="text" name="image_alt_tag" label="Image Alt Tag" value=$record->image_alt_tag}
                        {control type=radiogroup columns=2 name="main_image_functionality" label="Main Image Functionality" items="Single Image,Image with Swatches" values="si,iws"  default=$record->main_image_functionality|default:"si"}
                        
                        <div id="si-div" class="imngfuncbody">
                            <h3>Single Image</h3>
                            <h4>Main Image</h4>
                            {control type=files name=mainimages label="Product Images" subtype="mainimage" value=$record->expFile limit=1}
                            <h4>{gettext str="Thumbnail for Main Image"}</h4>
                            <p>{gettext str="If no image is provided to use as a thumbnail, one will be generated from the main image. This image will only show if additional images are provided"}</p>
                            {control type=files name=mainthumb label="Product Images" subtype="mainthumbnail" value=$record->expFile limit=1}
                        </div>
                        <div id="iws-div" class="imngfuncbody" style="display:none;">
                            <table border="0" cellspacing="0" cellpadding="1" width="100%">
                                <tr>
                                    <th width="50%">Image</th>
                                    <th width="50%">Color/Pattern Swatch</th>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">
                                        {control type=files name=imagesforswatches label="Images" subtype="imagesforswatches" value=$record->expFile}
                                    </td>
                                    <td style="vertical-align:top;">
                                        {control type=files name=swatchimages label="Swatches" subtype="swatchimages" value=$record->expFile}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        {br}
                        <h4>{gettext str="Additional Images"}</h4>
                        <p>{gettext str="Have additional images to show for your product?"}</p>
                        
                        <div class="additional-images">
                            {control type=files name=images label="Additional Images" subtype="images" value=$record->expFile}
                        </div>
                        {br}
                        <h4>{gettext str="Additional File Attachments"}</h4>
                        <p>{gettext str="Attach Product Brochures, Docs, Manuals, etc."}</p>
                        {control type=files name=brochures label="Additional Files" subtype="brochures" value=$record->expFile}
                    </div>

                </div>
                <div id="quantity">
                    {control type="text" name="quantity" label="Quantity" value=$record->quantity}
                    {control type="text" name="minimum_order_quantity" label="Minimum order quantity" value=$record->minimum_order_quantity|default:1}
                    {control type="checkbox"  name="allow_partial" label="Allow partial quantities?" value=1 checked=$record->allow_partial}
                    {control type="checkbox" name="is_hidden" label="Hide Product" value=$record->is_hidden} 
                    {control type="radiogroup" name="availability_type" label="Quantity Display" items=$record->quantity_display default=$record->availability_type|default:0}
                    {control type="textarea" name="availability_note" label="* Note to display per above selection" rows=5 cols=45 value=$record->availability_note}
                </div>
                <div id="shipping">
                    {control type="checkbox" name="no_shipping" label="This item doesn't require shipping" value=1 checked=$record->no_shipping}
                    {control type="dropdown" name="required_shipping_calculator_id" id="required_shipping_calculator_id" label="Required Shipping Service" includeblank="--- Select a shipping service ---" items=$shipping_services value=$record->required_shipping_calculator_id onchange="switchMethods();"}
                    {foreach from=$shipping_methods key=calcid item=methods name=sm}
                        <div id="dd-{$calcid}" class="hide methods">
                        {control type="dropdown" name="required_shipping_methods[`$calcid`]" label="Shipping Methods" items=$methods value=$record->required_shippng_method}
                        </div>
                    {/foreach}
                    {control type="text" name="weight" label="Item Weight" size=4 filter=decimal value=$record->weight}
                    {control type="text" name="width" label="Width (in inches)" size=4 filter=decimal value=$record->width}
                    {control type="text" name="height" label="Height (in inches)" size=4 filter=decimal value=$record->height}                
                    {control type="text" name="length" label="Length (in inches)" size=4 filter=decimal value=$record->length}          
                    {control type="text" name="surcharge" label="Surcharge" size=4 filter=money value=$record->surcharge}
                </div>
                <div id="categories">
                    <a href='{link controller="storeCategory" action="manage"}'>Manage Categories</a>{br}{br}
                    {control type="tagtree" name="managecats" id="managecats" controller="store" model="storeCategory" draggable=false addable=false menu=true checkable=true values=$record->storeCategory expandonstart=true }
                    <a href='{link controller="storeCategory" action="manage"}'>Manage Categories</a>{br}
                </div>
                <div id="options">
                    <h2>Add options to your product.</h2>
                    By simply selecting the checkbox in front of an option in an option group (the LABEL column), that option group and option will be added to the checkout process for this product.
                    By default, the user is NOT required to make a selection.  However, if you select the Required checkbox, the user will be forced to make a selection from that option group. {br}
                    Select Single presents the option group as a dropdown field where they may select one and only option.{br}
                    Select Multiple presents the options as a checkbox group where the user may select multiple options.{br}
                    Selecting the Default radio button for an option will cause that option to be selected by default. {br}{br}
                    {include file="`$smarty.const.BASE`framework/modules/ecommerce/products/views/product/options_partial.tpl"}
                </div>
                <div id="featured">
                    {control type="checkbox" name="is_featured" label="Feature this product?" value=1 checked=$record->is_featured}
                    {control type="textarea" name="featured_body" label="Featured Description" height=450 value=$record->featured_body}          
                    {control type=files name="featured_image" label="Featured Product Images" subtype="featured_image" value=$record->expFile}
                </div>
                <div id="relprod" style="position:relative;">
                    <h2>Related Products</h2>                    
                    
                    {capture assign="callbacks"}
                    {literal}
                    
                    
                    // the text box for the title
                    var tagInput = Y.one('#related_items');

                    // the UL to append to
                    var tagUL = Y.one('#relatedItemsList');

                    // the Add Link
                    var tagAddToList = Y.one('#addToRelProdList');


                    var onRequestData = function( oSelf , sQuery , oRequest) {
                        tagInput.setStyles({'border':'1px solid green','background':'#fff url('+EXPONENT.PATH_RELATIVE+'framework/core/subsystems-1/forms/controls/assets/autocomplete/loader.gif) no-repeat 100% 50%'});
                    }
                    
                    var onRGetDataBack = function( oSelf , sQuery , oRequest) {
                        tagInput.setStyles({'border':'1px solid #000','backgroundImage':'none'});
                    }
                    
                    var appendToList = function(e,args) {
                        tagUL.appendChild(createHTML(args[2]));
                        return true;
                    }
                    
                    var removeLI = function(e) {
                        var t = e.target;
                        if (t.test('a')) tagUL.removeChild(t.get('parentNode'));
                    }

                    var createHTML = function(val) {
                        var li = '<li>'+val.title+' - <a href="javascript:{}">X</a><br />';
                            li += 'Model #: '+val.model+'';
                            li += '<br /><input type="checkbox" name="relateBothWays['+val.id+']" value="'+val.id+'"> Relate both ways';
                            li += '<input type=hidden name="relatedProducts['+val.id+']" value="'+val.id+'" /></li>';
                        var newLI = Y.Node.create(li);
                        return newLI;
                    }

                    //tagAddToList.on('click',appendToList);
                    tagUL.on('click',removeLI);

                    // makes formatResult work mo betta
                    oAC.resultTypeList = false;

                    // when we start typing...?
                    oAC.dataRequestEvent.subscribe(onRequestData);
                    oAC.dataReturnEvent.subscribe(onRGetDataBack);

                    // format the results coming back in from the query
                    oAC.formatResult = function(oResultData, sQuery, sResultMatch) {
                        return oResultData.title;
                    }

                    // what should happen when the user selects an item?
                    oAC.itemSelectEvent.subscribe(appendToList);


                    {/literal}
                    {/capture}

                    {control type="autocomplete" controller="store" action="search" name="related_items" label="Related Products" value="Search Title or SKU" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" jsinject=$callbacks}
                    
                    <ul id="relatedItemsList">
                        {foreach from=$record->crosssellItem item=prod name=prods}
                            <li>
                                {$prod->title|strip_tags} - <a href="javascript:{ldelim}{rdelim}">X</a><br />
                                Model #: {$prod->model|strip_tags}
                                <input type=hidden name="relatedProducts[{$prod->id}]" value="{$prod->id}" />
                            </li>                   
                        {/foreach}
                    </ul>
                    
                </div>
                <div id="uifld">
                    <h2>User Input Fields</h2>
                    You may define fields here that the user is required to fill out when purchasing this product.  For instance, to supply a value to be imprinted on an item.{br}
                    {control class="userInputToggle" type="checkbox" name="user_input_use[0]"  label="Show User Field 1" value=1 checked=$record->user_input_fields.0.use}
                    <div>
                        <table>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_name[0]" label="Field Name" value=$record->user_input_fields.0.name}    
                                </td>
                                <td>
                                    {control type="checkbox" name="user_input_is_required[0]" label="Required?" value=1 checked=$record->user_input_fields.0.is_required}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_min_length[0]" label="Minimum Length" value=$record->user_input_fields.0.min_length}    
                                </td>
                                <td>
                                    {control type="text" name="user_input_max_length[0]" label="Maximum Length" value=$record->user_input_fields.0.max_length}
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    {control type="textarea" name="user_input_description[0]" label="Description For Users" height=200 value=$record->user_input_fields.0.description}    
                                </td> 
                            </tr>
                        </table>
                        <hr>
                    </div>
                    {control class="userInputToggle" type="checkbox" name="user_input_use[1]"  label="Show User Field 2" value=1 checked=$record->user_input_fields.1.use}
                    <div>
                        <table>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_name[1]" label="Field Name" value=$record->user_input_fields.1.name}    
                                </td>
                                <td>
                                    {control type="checkbox" name="user_input_is_required[1]" label="Required?" value=1 checked=$record->user_input_fields.1.is_required}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_min_length[1]" label="Minimum Length" value=$record->user_input_fields.1.min_length}    
                                </td>
                                <td>
                                    {control type="text" name="user_input_max_length[1]" label="Maximum Length" value=$record->user_input_fields.1.max_length}
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    {control type="textarea" name="user_input_description[1]" label="Description For Users" height=200 value=$record->user_input_fields.1.description}    
                                </td> 
                            </tr>
                        </table>
                        <hr>
                    </div>
                    {control class="userInputToggle" type="checkbox" name="user_input_use[2]"  label="Show User Field 3" value=1 checked=$record->user_input_fields.2.use}
                    <div>
                        <table>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_name[2]" label="Field Name" value=$record->user_input_fields.2.name}    
                                </td>
                                <td>
                                    {control type="checkbox" name="user_input_is_required[2]" label="Required?" value=1 checked=$record->user_input_fields.2.is_required}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_min_length[2]" label="Minimum Length" value=$record->user_input_fields.2.min_length}    
                                </td>
                                <td>
                                    {control type="text" name="user_input_max_length[2]" label="Maximum Length" value=$record->user_input_fields.2.max_length}
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    {control type="textarea" name="user_input_description[2]" label="Description For Users" height=200 value=$record->user_input_fields.2.description}    
                                </td> 
                            </tr>
                        </table>
                        <hr>
                    </div>
                    {control class="userInputToggle" type="checkbox" name="user_input_use[3]"  label="Show User Field 4" value=1 checked=$record->user_input_fields.3.use}
                    <div>
                        <table>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_name[3]" label="Field Name" value=$record->user_input_fields.3.name}    
                                </td>
                                <td>
                                    {control type="checkbox" name="user_input_is_required[3]" label="Required?" value=1 checked=$record->user_input_fields.3.is_required}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_min_length[3]" label="Minimum Length" value=$record->user_input_fields.3.min_length}    
                                </td>
                                <td>
                                    {control type="text" name="user_input_max_length[3]" label="Maximum Length" value=$record->user_input_fields.3.max_length}
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    {control type="textarea" name="user_input_description[3]" label="Description For Users" height=200 value=$record->user_input_fields.3.description}    
                                </td> 
                            </tr>
                        </table>
                        <hr>
                    </div>
                    {control class="userInputToggle" type="checkbox" name="user_input_use[4]"  label="Show User Field 5" value=1 checked=$record->user_input_fields.4.use}
                    <div>
                        <table>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_name[4]" label="Field Name" value=$record->user_input_fields.4.name}    
                                </td>
                                <td>
                                    {control type="checkbox" name="user_input_is_required[4]" label="Required?" value=1 checked=$record->user_input_fields.4.is_required}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_min_length[4]" label="Minimum Length" value=$record->user_input_fields.4.min_length}    
                                </td>
                                <td>
                                    {control type="text" name="user_input_max_length[4]" label="Maximum Length" value=$record->user_input_fields.4.max_length}
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    {control type="textarea" name="user_input_description[4]" label="Description For Users" height=200 value=$record->user_input_fields.4.description}    
                                </td> 
                            </tr>
                        </table>
                        <hr>
                    </div>
                    {control class="userInputToggle" type="checkbox" name="user_input_use[5]"  label="Show User Field 6" value=1 checked=$record->user_input_fields.5.use}
                    <div>
                        <table>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_name[5]" label="Field Name" value=$record->user_input_fields.5.name}    
                                </td>
                                <td>
                                    {control type="checkbox" name="user_input_is_required[5]" label="Required?" value=1 checked=$record->user_input_fields.5.is_required}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {control type="text" name="user_input_min_length[5]" label="Minimum Length" value=$record->user_input_fields.5.min_length}    
                                </td>
                                <td>
                                    {control type="text" name="user_input_max_length[5]" label="Maximum Length" value=$record->user_input_fields.5.max_length}
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    {control type="textarea" name="user_input_description[5]" label="Description For Users" height=200 value=$record->user_input_fields.5.description}    
                                </td> 
                            </tr>
                        </table>
                        <hr>
                    </div>
                </div>
                <div id="active">
                    <h2>Active/Inactive</h2>
                    {control type="radiogroup" name="active_type" label=" " items=$record->active_display default=$record->active_type|default:0}
                    <h2>Status</h2>
                    {control type="dropdown" name="product_status_id" label=" " frommodel=product_status items=$status_display value=$record->product_status_id}
                </div>                
                <div id="meta">
                    <h2>Meta Info</h2>
                    {control type="text" name="sef_url" label="SEF URL" value=$record->sef_url}
                    {control type="text" name="meta_title" label="Meta Title" value=$record->meta_title|escape:"htmlall"}
                    {control type="textarea" name="meta_description" label="Meta Description" value=$record->meta_description}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords" value=$record->meta_keywords}
                </div>
                <div id="notes">
                    <h2>Notes</h2>
                    {simplenote content_type="product" content_id=$record->id require_login="1" require_approval="0" require_notification="0" tab="notes"}
                </div>
                 <div id="xtrafields">
                    <h2>Extra Fields</h2> 
                    You may add up to four extra fields to your product here.  These field names are also picked up by your child products where you can assign values to them.
                    <table> 
                        <tr>
                            <td>{control type="text" name="extra_fields_name[0]" label="Extra Field Name #1:" value=$record->extra_fields.0.name}</td>
                            {if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[0]" label="Extra Field Value #1:" value=$record->extra_fields.0.value}</td>{/if}
                        </tr>
                        <tr>
                            <td>{control type="text" name="extra_fields_name[1]" label="Extra Field Name #2:" value=$record->extra_fields.1.name}</td>
                            {if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[1]" label="Extra Field Value #2:" value=$record->extra_fields.1.value}</td>{/if}
                        </tr>
                        <tr>
                            <td>{control type="text" name="extra_fields_name[2]" label="Extra Field Name #3:" value=$record->extra_fields.2.name}</td>
                            {if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[2]" label="Extra Field Value #3:" value=$record->extra_fields.2.value}</td>{/if}
                        </tr>
                        <tr>
                            <td>{control type="text" name="extra_fields_name[3]" label="Extra Field Name #4:" value=$record->extra_fields.3.name}</td>
                            {if $record->childProduct|@count == 0}<td>{control type="text" name="extra_fields_value[3]" label="Extra Field Value #4:" value=$record->extra_fields.3.value}</td>{/if}
                        </tr>
                    </table>
                </div>
				
				<div id="skus">
                    <h2>Product SKUS / Model</h2>
					<a href='{link controller="store" action="edit_model_alias" product_id=$record->id}' class="add">Add Model Alias</a>
					<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
						<thead>
							<tr>
								<th style="width:50px">
									&nbsp;
								</th>
								<th>
									Alias
								</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$record->model_alias item=model_alias key=key name=model_aliases}
							<tr class="{cycle values='odd,even'}">
								<td>
									{icon action=edit_model_alias record=$model_alias img="edit.png"}  
									{icon action=delete_model_alias record=$model_alias img="delete.png"}  
								</td>
								<td>
								{$model_alias->model}
								</td>
							</tr>
							{/foreach}
						</tbody>
					</table>
                </div>
				
                <div id="misc">
                    <h2>Miscellaneous Information</h2>
                    {control type="text" name="warehouse_location" label="Warehouse Location" value=$record->warehouse_location}
                    <hr>
                    {control type="text" name="previous_id" label="Previous Product ID" value=$record->previous_id}  
                </div>
            </div>
        </div>
        {control type="buttongroup" submit="Save Product" cancel="Cancel"}
        {if isset($record->original_id)}
            {control type="hidden" name="original_id" value=$record->original_id}
            {control type="hidden" name="original_model" value=$record->original_model}
            {control type="checkbox" name="copy_children" label="Copy Child Products?" value="1"}
            {control type="checkbox" name="copy_related" label="Copy Related Products?" value="1"}
            {control type="checkbox" name="adjust_child_price" label="Reset Price on Child Products?" value="1"}
            {control type="text" name="new_child_price" label="New Child Price" value=""}
            {*control type="checkbox" name="copy_related" label="Copy Related Products?" value="1"*}
        {/if}
    {/form}
</div>
<div class="loadingdiv">Loading</div>


{script unique="editform" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','node-event-simulate','yui2-yahoo-dom-event','yui2-tabview','yui2-element', function(Y) {
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


