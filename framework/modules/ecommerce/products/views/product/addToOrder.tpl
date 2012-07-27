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
{* edebug var=$product *}
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
        <strong>{"Additional information is required before we can add to your cart"|gettext}</strong>
    {br}{br}
        {"If you are ordering multiple quantities of this item, the SAME information you select here will be applied to all of the items."|gettext}&#160;&#160;
        {"If you would like different options or personalized fields for each item, please add them one at a time to your cart."|gettext}
    </p>
    {clear}
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
                            msg = "{/literal}{"You\'ll also need a value greater than 0 for a quantity."|gettext}{literal}"
                        }
                    };
                });
                
                if (bxchkd==0 || msg!="") {
                    alert('{/literal}{"You need to check at least 1 product before it can be added to your cart"|gettext}{literal}'+msg);
                } else {
                    frm.submit();
                };

            });
            
            
        });
        {/literal}
        {/script}
    {form controller=order action=save_new_order_item id="save_new_order_item_form"}
        {control type=hidden name=product_id value=$product->id}
        {control type=hidden name=orderid value=$params.orderid}
        {control type=hidden name=product_type value=$product->classname}			        
        {control type=hidden name=options_shown value=$product->id}                    
        {if $product->childProduct|@count >= 1}
    
    <div id="child-products" class="exp-ecom-table">
       <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>&#160;</th>
                    <th><strong>{"QTY"|gettext}</strong></th>
                    <th><strong>{"SKU"|gettext}</strong></th>
                    {foreach from=$product->extra_fields item=chiprodname}                        
                        <th><span>{$chiprodname.name}</span></th>                            
                    {/foreach}
                    <th style="text-align: right;"><strong>{"PRICE"|gettext}</strong></th>
                    <th>&#160;</th>
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
                                * }

                                {if  $chiprod->active_type == 0 && $product->active_type == 0 && ($chiprod->availability_type == 0 || $chiprod->availability_type == 1 || ($chiprod->availability_type == 2 && ($chiprod->quantity - $chiprod->minimum_order_quantity >= 0))) }
                                    <td><input name="prod-check[]" type="checkbox" value="{$chiprod->id}"></td>
                                    <td><input name="prod-quantity[{$chiprod->id}]" type="text" value="{$chiprod->minimum_order_quantity}" size=3 maxlength=5></td>
                                {elseif ($chiprod->availability_type == 2 && $chiprod->quantity <= 0) && $chiprod->active_type == 0}
                                    <td colspan="2"><span><a href="javascript:void();" rel=nofollow title="{$chiprod->availability_note}">Out Of Stock</a></span></td>
                                {elseif $product->active_type != 0 || $chiprod->availability_type == 3 || $chiprod->active_type == 1 || $chiprod->active_type == 2}
                                     <td colspan="2" style="text-align:center; font-weight: bold;">N/A</td> 
                                {/if}
                            
                            <td><span>{$chiprod->model}</span></td>
                            {if $chiprod->extra_fields}
                                {foreach from=$chiprod->extra_fields item=ef}
                                <td><span>{$ef.value|stripslashes}</span></td>
                                {/foreach}
                            {/if}
                            <td style="text-align: right;"> 
                                {if $chiprod->use_special_price}
                                    <span style="color:red; font-size: 8px; font-weight: bold;">SALE</span>{br}
                                    <span>{currency_symbol}<input name="prod-price[{$chiprod->id}]" type="text" value="{$chiprod->special_price|number_format:2}" size=7 maxlength=9></span>
                                {else}
                                    <span>{currency_symbol}<input name="prod-price[{$chiprod->id}]" type="text" value="{$chiprod->base_price|number_format:2}" size=7 maxlength=9></span>
                                {/if}                                                                 
                            </td> 
                            <td>
                            </td>
                        </tr>                
                {/foreach}
            </tbody>
        </table>        
    </div>
                
        {else}
            {control type=text name=qty label="Quantity" value=1}
            {control type=text name=products_price label="Products Price" value=$product->base_price}
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
                <span style="font-variant:small-caps;">* {"Selection required"|gettext}.</span>
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
        {control type="buttongroup" submit="Add Item(s) to Order"|gettext}
    {/form}
</div>