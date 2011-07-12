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
 
{css unique="product-show" link="`$asset_path`css/storefront.css" corecss="button,tables"}

{/css}

{css unique="product-show" link="`$asset_path`css/ecom.css"}

{/css}

{if $product->user_message != ''}
<div id="msg-queue" class="common msg-queue">
    <ul class="queue error">
        <li>{$product->user_message}</li>
    </ul>
</div>
{/if}

<div class="module store show">
    <h1>{$product->title}</h1>
    
    {permissions}
    <div class="item-actions">
        {if $permissions.edit == 1}
            {icon action=edit record=$product title="Edit `$product->title`"}
        {/if}
        {if $permissions.delete == 1}
            {icon action=delete record=$product title="Delete `$product->title`" onclick="return confirm('Are you sure you want to delete this product?');"}
        {/if}
        {if $permissions.edit == 1}
            {icon action=copyProduct class="copy" text="Copy Product" title="Copy `$product->title` " record=$product}
        {/if}
        {if $permissions.edit == 1}   
        
            <a href="{link controller=store action=edit parent_id=$product->id product_type='childProduct'}" class="add">Add Child Product</a>
        {/if}
    </div>
    {/permissions}
    
    <div class="large-ecom-image">
        {if $product->main_image_functionality=="iws"}
            {* Image with swatches *}
            {if $product->image_alt_tag !=''} 
                {img file_id=$product->expFile.imagesforswatches[0]->id w=250 alt="`$product->image_alt_tag`" title="`$product->title`" class="large-img" id="enlarged-image"}
            {else}
                {img file_id=$product->expFile.imagesforswatches[0]->id w=250 alt="Image of `$product->title`" title="`$product->title`" class="large-img" id="enlarged-image"}
            {/if}
            {assign value=$product->expFile.imagesforswatches[0]->id var=mainimg}
        {else}
            {if $product->image_alt_tag !=''}  
                {img file_id=$product->expFile.mainimage[0]->id w=250 alt="`$product->image_alt_tag`" title="`$product->title`"  class="large-img" id="enlarged-image"}
            {else}
                {img file_id=$product->expFile.mainimage[0]->id w=250 alt="Image of `$product->title`" title="`$product->title`" class="large-img" id="enlarged-image"}
            {/if}
            {assign value=$product->expFile.mainimage[0]->id var=mainimg}
            
        {/if}
    </div>
    
    

    <div class="prod-price"> 
        {* 
            [0] => Always available even if out of stock.
            [1] => Available but shown as backordered if out of stock.
            [2] => Unavailable if out of stock.
            [3] => Show as &quot;Call for Price&quot;.
        *}                                                                                      
        {if $product->availability_type == 3}
            <strong>{"Call for price"|gettext}</strong>
        {else}
            {if $product->use_special_price}                     
                <span class="regular-price on-sale">{currency_symbol}{$listing->base_price|number_format:2}</span>
                <span class="sale-price">{currency_symbol}{$listing->special_price|number_format:2}&nbsp;<sup>{"SALE!"|gettext}</sup></span>
            {else}
                <span class="regular-price">{currency_symbol}{$listing->base_price|number_format:2}</span>
            {/if}
        {/if}
    </div>
    
    {if $product->childProduct|@count == 0}   
    <div class="addtocart-area">
        {form id="addtocart`$product->id`" controller=cart action=addItem} 
        {control type="hidden" name="product_id" value="`$product->id`"}   
        {control type="hidden" name="product_type" value="`$product->product_type`"}
        {*control name="qty" type="text" value="`$product->minimum_order_quantity`" size=3 maxlength=5 class="lstng-qty"*}

        {if $product->hasOptions()}
            <div class="product-options">
                {foreach from=$product->optiongroup item=og}
                    {if $og->hasEnabledOptions()} 
                        <div class="option {cycle values="odd,even"}">
                            <h4>{$og->title}</h4>
                            {if $og->allow_multiple}
                                {optiondisplayer product=$product options=$og->title view=checkboxes display_price_as=diff selected=$params.options}           
                            {else}
                                {if $og->required}
                                    {optiondisplayer product=$product options=$og->title view=dropdown display_price_as=diff selected=$params.options required=true}          
                                {else}
                                    {optiondisplayer product=$product options=$og->title view=dropdown display_price_as=diff selected=$params.options}          
                                {/if}                                           
                            {/if}
                        </div> 
                    {/if}
                {/foreach}
            </div>
        {/if}

        {if $product->availability_type == 0 && $product->active_type == 0}
            <button type="submit" class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE}" rel="nofollow">{"Add to Cart"|gettext}</button>
        {elseif $product->availability_type == 1 && $product->active_type == 0}
            <button type="submit" class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE}" rel="nofollow">{"Add to Cart"|gettext}</button>
            {if $product->quantity <= 0}<span class="error">{$product->availability_note}</span>{/if}   
        {elseif $product->availability_type == 2}
            {if $user->isAdmin()}
                <button type="submit" class="awesome red {$smarty.const.BTN_SIZE}" rel="nofollow">{"Add to Cart"|gettext}</button>
            {/if}
            {if $product->quantity <= 0}<span class="error">{$product->availability_note}</span>{/if}              
        {elseif $product->active_type == 1}
            {if $user->isAdmin()}
                <button type="submit" class="awesome red {$smarty.const.BTN_SIZE}" rel="nofollow">{"Add to Cart"|gettext}</button>
            {/if}
            <em class="unavailable">{"Product currently unavailable for purchase"|gettext}</em>
        {/if}    
        
        {/form}
    </div> 
    {/if}   
    
    
    {if $product->company->id}
    <p class="manufacturer">
        {gettext str="Manufactured by"}:
        <a href="{link controller=company action=show id=$product->company->id}">{$product->company->title}</a>
    </p>
    {/if}
    
    {if $product->model}
    <p class="sku">
        {gettext str="SKU"}:
        <strong>{$product->model}</strong>
    </p>
    {/if}
    
    {if $product->warehouse_location}
    <p class="warehouse-location">
        LOC:{$product->warehouse_location}
    </p>
    {/if}    
    
    {if $product->minimum_order_quantity > 1}
    {br}
    <p>
        <span>This item has a minimum order quantity of {$product->minimum_order_quantity}</span>
    </p>
    {/if}    

    {if $product->expFile.images[0]->id}
    <div class="additional thumbnails">
        <h3>{gettext str="Additional Images"}</h3>
        <ul>
            {if $product->expFile.mainthumbnail[0]->id}
                <li>{img file_id=$product->expFile.mainthumbnail[0]->id w=50 h=50 zc=1 class="thumbnail" id="thumb-`$mainimg`"}</li>
            {else}
                <li>{img file_id=$mainimg w=50 h=50 zc=1 class="thumbnail" id="thumb-`$mainimg`"}</li>
            {/if}
        {foreach from=$product->expFile.images item=thmb}
            <li>{img file_id=$thmb->id w=50 h=50 zc=1 class="thumbnail" id="thumb-`$thmb->id`"}</li>
        {/foreach}
        </ul>
    </div>
    {/if}
    
    {rating content_type="product" subtype="quality" label="Product Rating"|gettext record=$product}
    
    {if $product->main_image_functionality=="iws"}
    <div class="swatches thumbnails">
        <h3>{gettext str="Available Patterns"}</h3>
        <ul>
        {foreach from=$product->expFile.swatchimages item=swch key=key}
            <li>
                {img file_id=$swch->id w=50 h=50 zc=1 class="swatch" id="thumb-`$product->expFile.imagesforswatches[$key]->id`"}
                <div>{img file_id=$swch->id w=100 h=100 zc=1 class="swatch" id="thumb-`$swch->id`"}{if $swch->title}<strong>{$swch->title}</strong>{/if}</div>
            </li>
        {/foreach}
        </ul>
    </div>
    {/if}
    
    {if $product->main_image_functionality=="iws" || $product->expFile.images[0]->id}
        
    {/if}
    {script unique="swapthumb" yui3mods='node'}
    {literal}

    YUI({ base:EXPONENT.YUI3_PATH,loadOptional: true}).use('node', function(Y) {
        var thumbs = Y.all('.thumbnails li img.thumbnail');
        var swatches = Y.all('.swatches li img.swatch');
        var mainimg = Y.one('#enlarged-image');

        var swapimage = function(e){
            var tmbid = e.target.get('id').split('-')[1];
            mainimg.set('src',EXPONENT.URL_FULL+"thumb.php?id="+tmbid+"&w=250");
        };
        
        thumbs.on('click',swapimage);
        swatches.on('click',swapimage);

    });

    {/literal}
    {/script}
            
    <div class="bodycopy">
        {$product->body}        
    </div>

    {if $product->expFile.brochures[0]->id}
    <div class="more-information">
        <h3>{gettext str="Additional Product Information"}</h3>
        <ul>
        {foreach from=$product->expFile.brochures item=doc}
            <li><a href="{link action=downloadfile id=$doc->id}">{if $doc->title}{$doc->title}{else}{$doc->filename}{/if}</a></li>
        {/foreach}
        </ul>
    </div>
    {/if}
     
    <div style="clear:both"></div>
    {if $product->childProduct|@count >= 1}
    {permissions}                   
    {if $permissions.delete == 1}   
        {icon class=delete action=deleteChildren record=$product title="Delete `$product->title`'s Children" onclick="return confirm('Are you sure you want to delete ALL child products?');"} 
        Delete All Child Products
    {/if}
    {/permissions}
    
    <div id="child-products" class="exp-ecom-table">
        {form id="child-products-form" controller=cart action=addItem}
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th><strong>{gettext str="QTY"}</strong></th>
                    <th><strong>{gettext str="SKU"}</strong></th>
                    {if $product->extra_fields}
                    {foreach from=$product->extra_fields item=chiprodname}                        
                        <th><span>{$chiprodname.name}</span></th>                            
                    {/foreach}
                    {/if}
                    <th style="text-align: right;"><strong>{gettext str="PRICE"}</strong></th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$product->childProduct item=chiprod}
                <tr class="{cycle values="odd,even"}">
                            
                        {* 
                            [0] => Always available even if out of stock.
                            [1] => Available but shown as backordered if out of stock.
                            [2] => Unavailable if out of stock.
                            [3] => Show as &quot;Call for Price&quot;.
                        *}

                    {if  $chiprod->active_type == 0 && $product->active_type == 0 && ($chiprod->availability_type == 0 || $chiprod->availability_type == 1 || ($chiprod->availability_type == 2 && ($chiprod->quantity - $chiprod->minimum_order_quantity >= 0))) }
                        <td>
                            <input name="prod-check[]" type="checkbox" value="{$chiprod->id}">
                        </td>
                        <td>
                            <input name="prod-quantity[{$chiprod->id}]" type="text" value="{$chiprod->minimum_order_quantity}" size=3 maxlength=5>
                        </td>
                    {elseif ($chiprod->availability_type == 2 && $chiprod->quantity <= 0) && $chiprod->active_type == 0}
                        <td>

                        </td>
                        <td>
                            <span><a href="javascript:void();" rel=nofollow title="{$chiprod->availability_note}">{"Out Of Stock"|gettext}</a></span>
                        </td>
                    {elseif $product->active_type != 0 || $chiprod->availability_type == 3 || $chiprod->active_type == 1 || $chiprod->active_type == 2}
                        <td>

                        </td>
                         <td>
                             N/A
                        </td> 
                    {/if}
                
                    <td>
                        <span>{$chiprod->model}</span>
                    </td>
                    {if $chiprod->extra_fields}
                    {foreach from=$chiprod->extra_fields item=ef}
                    <td>
                        <span>{$ef.value|stripslashes}</span>
                    </td>
                    {/foreach}
                    {/if}
                    <td style="text-align: right;">
                        {if $chiprod->availability_type == 3 && $chiprod->active_type == 0}
                            <strong><a href="javascript:void();" rel=nofollow title="{$chiprod->availability_note}">Call for price</a></strong>                
                        {else}
                            {if $chiprod->use_special_price}
                                <span style="color:red; font-size: 8px; font-weight: bold;">SALE</span>{br}
                                <span style="color:red; font-weight: bold;">{currency_symbol}{$chiprod->special_price|number_format:2}</span>
                            {else}
                                <span>{currency_symbol}{$chiprod->base_price|number_format:2}</span>
                            {/if}
                        {/if}
                    </td> 
                    <td>
                    {permissions}
                    <div class="item-actions">
                        {if $permissions.edit == 1}                                                        
                            {icon img="edit.png" action=edit id=$chiprod->id title="Edit `$chiprod->title`"}
                        {/if}
                        {if $permissions.delete == 1}
                            {icon img="delete.png" action=delete record=$chiprod title="Delete `$chiprod->title`" onclick="return confirm('Are you sure you want to delete this child product?');"}
                        {/if}
                        {if $permissions.edit == 1}
                            {icon action=copyProduct img="copy.png" title="Copy `$chiprod->title` " record=$chiprod}
                        {/if}
                    </div>
                    {/permissions}
                    </td>
                    </tr>                
                {/foreach}
            </tbody>
        </table>

        {if $product->active_type == 0}
        <a id="submit-chiprods" href="javascript:{ldelim}{rdelim}" class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE} exp-ecom-link" rel="nofollow"><strong><em>Add selected items to cart</em></strong></a>
        {/if}
        {/form}
        
        {script unique="children-submit"}
        {literal}
        YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
            Y.one('#submit-chiprods').on('click',function(e){
                e.halt();
                var frm = Y.one('#child-products-form');
                var chcks = frm.all('input[type="checkbox"]');
                var txts = frm.all('input[type="text"]');
                
                bxchkd=0;
                var msg = ""
                
                chcks.each(function(bx,key){
                    if (bx.get('checked')) {
                        bxchkd++;
                        if (parseInt(txts.item(key).get('value'))<=0) {
                            msg = " You'll also need a value greter than 0 for a quantity."
                        }
                    };
                });
                
                if (bxchkd==0 || msg!="") {
                    alert('You need to check at least 1 product before it can be added to your cart'+msg);
                } else {
                    frm.submit();
                };

            });
            
            
        });
        {/literal}
        {/script}
        
    </div>
    {/if}
    
     {if $product->crosssellItem|@count >= 1}
     <div class="products ipr{$config.images_per_row} related-products">
         <h2>{"Related Items"|gettext}</h2>
         
         {counter assign="ipr" name="ipr" start=1}

         {foreach name=listings from=$product->crosssellItem item=listing}

         {if $smarty.foreach.listings.first || $open_row}
             <div class="product-row">
             {assign var=open_row value=0}
         {/if}

         {include file=$listing->getForm('storeListing')}


         {if $smarty.foreach.listings.last || $ipr%$config.images_per_row==0}
             </div>
             {assign var=open_row value=1}
         {/if}
         {counter name="ipr"}
         {*if !$listing->active_type==2}

             {$ipr}
             {if $smarty.foreach.listings.first || $ipr%0}
             {counter name="ipr" start=0}
             <div class="product-row">
             {/if}

             {include file=$listing->getForm('storeListing')}

             {if $smarty.foreach.listings.last || $ipr%0}
                 </div>
             {/if}
         {counter name="ipr" start=$ipr+1}
         {/if*}
         {/foreach}
     </div>
     {/if}
</div>