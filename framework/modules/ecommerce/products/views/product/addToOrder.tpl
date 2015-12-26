{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
{if !empty($params.error)}
    {message class=error text=$params.error|gettext}
{/if}
<div class="module cart add-to-cart"> 
    <h1>{$product->title}</h1>
    {if $product->expFile.mainimage[0]->id}
        {img file_id=$product->expFile.mainimage[0]->id w=150 class="prod-img"}
    {/if}
    {*{$product->body}*}
    <blockquote>
        <strong>{"Additional information is required before we can add this to your order"|gettext}</strong>
        {br}{br}
        {"If you are adding multiple quantities of this item, the SAME information you select here will be applied to all of the items."|gettext}&#160;&#160;
        {"If you would like different options or personalized fields for each item, please add them one at a time to your order."|gettext}
    </blockquote>
    {clear}
    {form controller=order action=save_new_order_item id="save_new_order_item_form"}
        {control type="hidden" name="product_id" value=$product->id}
        {control type="hidden" name="orderid" value=$params.orderid}
        {control type="hidden" name="product_type" value=$product->classname}
        {control type="hidden" name="options_shown" value=$product->id}
        {if $product->childProduct|@count >= 1}
            {script unique="children-submit" yui3mods="node"}
            {literal}
                YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
                    Y.one('#submit-itemSubmit').on('click',function(e){
                        e.halt();
                        var frm = Y.one('#child-products');
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
                            Y.one('#save_new_order_item_form').submit();
                        };
                    });
                });
                {/literal}
             {/script}
            <div id="child-products" class="exp-ecom-table">
               <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <thead>
                        <tr>
                            <th>&#160;</th>
                            <th><strong>{"QTY"|gettext}</strong></th>
                            <th><strong>{"SKU"|gettext}</strong></th>
                            {foreach from=$product->extra_fields item=chiprodname}
                                <th><span>{$chiprodname.name}</span></th>
                            {/foreach}
                            <th style="text-align: right; padding-right: 10px"><strong>{"PRICE"|gettext}</strong></th>
                            {*<th>{'Action'|gettext}</th>*}
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
                                    <td><input class="checkbox form-control" name="prod-check[]" type="checkbox" value="{$chiprod->id}"></td>
                                    <td><input class="form-control" name="prod-quantity[{$chiprod->id}]" type="text" value="{$chiprod->minimum_order_quantity}" size=3 maxlength=5></td>
                                {elseif ($chiprod->availability_type == 2 && $chiprod->quantity <= 0) && $chiprod->active_type == 0}
                                    <td colspan="2"><span><a href="javascript:void();" rel=nofollow title="{$chiprod->availability_note}">{'Out Of Stock'|gettext}</a></span></td>
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
                                        <span style="color:red; font-size: 8px; font-weight: bold;">{'SALE'|gettext}</span>{br}
                                        <span>{currency_symbol}<input class="form-control" name="prod-price[{$chiprod->id}]" type="text" value="{$chiprod->special_price|number_format:2}" size=7 maxlength=9></span>
                                    {else}
                                        <span>{currency_symbol}<input class="form-cotnrol" name="prod-price[{$chiprod->id}]" type="text" value="{$chiprod->base_price|number_format:2}" size=7 maxlength=9></span>
                                    {/if}
                                </td>
                                {*<td>&#160;</td>*}
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {else}
            {control type=text name=qty label="Quantity" value=1}
            {control type=text name=products_price label="Products Price" value=$product->base_price filter=money}
        {/if}

        {* NOTE display product options *}
        {*{exp_include file="options.tpl"}*}
        {include file="`$smarty.const.BASE`framework/modules/ecommerce/views/store/options.tpl"}

        {* NOTE display product user input fields *}
        {*{exp_include file="input_fields.tpl"}*}
        {include file="`$smarty.const.BASE`framework/modules/ecommerce/views/store/input_fields.tpl"}

        {control type="buttongroup" id="submit-item" size=large color=green submit="Add Item(s) to Order"|gettext}
    {/form}
</div>
