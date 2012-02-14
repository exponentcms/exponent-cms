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
 
{css unique="add-to-cart" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/addToCart.css"}

{/css}
{* eDebug var=$params *}
{if isset($params.error)}
      <div id="msg-queue" class="common msg-queue">
    <ul class="queue error"><li>{$params.error}</li></ul>
    </div>
    {br}
    {/if}
<div class="module cart add-to-cart"> 
    <h1>{$product->title}</h1>
    {if $product->expFile.mainimage.0->id}
    {img file_id=$product->expFile.mainimage.0->id w=150 class="prod-img"}
    {/if}
    <p>
        <strong>{'Additional information is required before we can add to your cart'|gettext}</strong>
    {br}{br}
        {'If you are ordering multiple quantities of this item, the SAME information you select here will be applied to all of the items.'|gettext}&nbsp;&nbsp;
        {'If you would like different options or personalized fields for each item, please add them one at a time to your cart.'|gettext}
    </p>
    {clear}
    {form controller=cart action=addItem id="options-uifields"}
        {control type=hidden name=product_id value=$product->id}
        {control type=hidden name=product_type value=$product->classname}			        
        {control type=hidden name=options_shown value=$product->id}                    
        {control type=hidden name=qty value=$params.quantity} 
        {if isset($children)}
            {foreach from=$children key=child_id item=child}
                {control type=hidden name="children[`$child_id`]" value=$child}
            {/foreach}
        {/if}
        {if $product->hasOptions()}
            <div class="product-options">
                <h2>{$product->title} {'Options'|gettext}</h2>
                {foreach from=$product->optiongroup item=og}
                    {if $og->hasEnabledOptions()}
                        <div class="option {cycle values="odd,even"}">
                            {if $og->allow_multiple}
                                {optiondisplayer product=$product options=$og->title view=checkboxes display_price_as=diff selected=$params.options}           
                            {else}
                                {if $og->required}
                                    {$og->title}
                                    {optiondisplayer product=$product options=$og->title view=dropdown display_price_as=diff selected=$params.options required=true}          
                                {else}
                                    {optiondisplayer product=$product options=$og->title view=dropdown display_price_as=diff selected=$params.options}          
                                {/if}                                           
                            {/if}
                        </div> 
                    {/if}
                {/foreach}
                <span style="font-variant:small-caps;">* {'Selection required'|gettext}.</span>
            </div>
        {/if}
        
        {if !empty($product->user_input_fields) && $product->user_input_fields|@count>0 }
            <div class="user-input-fields">
            <h2>{'Additional Information for'|gettext} {$product->title}</h2>
            <p>{'This item would like the following additional information. Items marked with an * are required:'|gettext}</p>
            {foreach from=$product->user_input_fields key=uifkey item=uif}  
                <div class="user-input {cycle values="odd,even"}">
                    {if $uif.use}                   
                         {if $uif.is_required}
                             {control type=text name="user_input_fields[`$uifkey`]" size=50 maxlength=$uif.max_length label='* '|cat:$uif.name|cat:':' required=$uif.is_required value=$params.user_input_fields.$uifkey}
                         {else}
                             {control type=text name="user_input_fields[`$uifkey`]" size=50 maxlength=$uif.max_length label=$uif.name|cat:':' required=$uif.is_required value=$params.user_input_fields.$uifkey}
                         {/if}
                         {if $uif.description != ''}{$uif.description}{/if}
                    {/if}
                </div>
            {/foreach}
            </div>
        {/if}
        {control type="buttongroup" submit="Add to cart"|gettext}
    {/form}
</div>

