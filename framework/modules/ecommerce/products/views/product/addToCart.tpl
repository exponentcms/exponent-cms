{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
 
 
{css unique="add-to-cart" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/addToCart.css"}

{/css}
{* eDebug var=$product *}
{if isset($params.error)}
      <div id="msg-queue" class="common msg-queue">
    <ul class="queue error"><li>{$params.error}</li></ul>
    </div>
    {br}
    {/if}
<div class="module cart add-to-cart"> 
    <h1>{$product->title}</h1>
    {img file_id=$product->expFile.mainimage.0->id w=150 class="prod-img"}
    <p>
        <strong>Additional information is required before we can add to your cart</strong>
    {br}{br}
        If you are ordering multiple quantities of this item, the SAME information you select here will be applied to 
    all of the items. If you would like different options or personalized fields for each item, please add them one at a time to your cart.
    </p>
    <div style="clear:both"></div>
    {form controller=cart action=addItem id="options-uifields"}
        {control type=hidden name=product_id value=$product->id}
        {control type=hidden name=product_type value=$product->classname}			        
        {control type=hidden name=options_shown value=$product->id}                    
        {control type=hidden name=qty value=$params.qty} 
        {if isset($children)}
            {foreach from=$children key=child_id item=child}
                {control type=hidden name=children[$child_id] value=$child}
            {/foreach}
        {/if}
        {if $product->hasOptions()}
            <div class="product-options">
                <h2>{$product->title} Options</h2>
                {foreach from=$product->optiongroup item=og}
                    {if $og->hasEnabledOptions()} 
                        <div class="option {cycle values="odd,even"}">
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
                <span style="font-variant:small-caps;">* Selection required.</span>
            </div>
        {/if}
        
        {if !empty($product->user_input_fields) && $product->user_input_fields|@count>0 }
            <div class="user-input-fields">
            <h2>Additional Information for {$product->title}</h2>
            <p>This item would like the following additional information. Items marked with an * are required:</p>
            {foreach from=$product->user_input_fields key=uifkey item=uif}  
                <div class="user-input {cycle values="odd,even"}">
                    {if $uif.use}                   
                         {if $uif.is_required}
                             {control type=text name=user_input_fields[$uifkey] size=50 maxlength=$uif.max_length label='* '|cat:$uif.name|cat:':' required=$uif.is_required value=$params.user_input_fields.$uifkey}
                         {else}
                             {control type=text name=user_input_fields[$uifkey] size=50 maxlength=$uif.max_length label=$uif.name|cat:':' required=$uif.is_required value=$params.user_input_fields.$uifkey}
                         {/if}
                         {if $uif.description != ''}{$uif.description}{/if}
                    {/if}
                </div>
            {/foreach}
            </div>
        {/if}
        {control type="buttongroup" submit="Add to cart"}
        <div class="hideButton">
            <a href="#" class="addtocart exp-ecom-link" rel="nofollow"><strong><em>Add to cart</em></strong></a>
        </div>
    {/form}
</div>

{script unique="replace-button"}
{literal}
    YUI({ base:EXPONENT.URL_FULL+'external/yui3/build/',loadOptional: true}).use('node', function(Y) {
        Y.one('.control.buttongroup').setStyle('display','none');
        Y.one('.hideButton').removeClass('hideButton').one('.addtocart').on('click',function(e){Y.one('#options-uifields').submit()});
    });
    
{/literal}
{/script}

