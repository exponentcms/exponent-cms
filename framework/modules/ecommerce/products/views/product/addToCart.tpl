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
{* eDebug var=$params *}
{if !empty($params.error)}
    {message class=error text=$params.error|gettext}
{/if}
<div class="module cart add-to-cart"> 
    <h1>{$product->title}</h1>
    {if $product->expFile.mainimage[0]->id}
        {img file_id=$product->expFile.mainimage[0]->id w=150 class="prod-img"}
    {/if}
    <blockquote>
        <strong>{'Additional information is required before we can add this to your cart'|gettext}</strong>
        {br}{br}
        {'If you are ordering multiple quantities of this item, the SAME information you select here will be applied to all of the items.'|gettext}&#160;&#160;
        {'If you would like different options or personalized fields for each item, please add them one at a time to your cart.'|gettext}
    </blockquote>
    {clear}
    {form id="addtocart`$product->id`" controller=cart action=addItem}
        {control type="hidden" name="controller" value=cart}
        {control type="hidden" name="product_id" value=$product->id}
        {control type="hidden" name="product_type" value=$product->classname}
        {control type="hidden" name="options_shown" value=$product->id}
        {control type="hidden" name="qty" value=$params.quantity}
        {if isset($children)}
            {foreach from=$children key=child_id item=child}
                {control type=hidden name="children[`$child_id`]" value=$child}
            {/foreach}
        {/if}

        {* NOTE display product options *}
        {foreach $params.options as $ogkey=>$og}
            {foreach $og as $optkey=>$opt}
                {control type="hidden" name="options[`$ogkey`][`$optkey`]" value=$opt}
            {/foreach}
        {/foreach}
        {if !$product->show_options || !empty($params.option_error)}
            {*{exp_include file="options.tpl"}*}
            {include file="`$smarty.const.BASE`framework/modules/ecommerce/views/store/options.tpl"}
            <div>
                <strong>{'Total Cost of Options'|gettext}:</strong>
                <span id="item-price">$0.00</span>
            </div>
        {/if}

        {* NOTE display product user input fields *}
        {*{exp_include file="input_fields.tpl"}*}
        {include file="`$smarty.const.BASE`framework/modules/ecommerce/views/store/input_fields.tpl"}
        {br}
        {control type="buttongroup" size=large color=blue submit="Add to cart"|gettext}
    {/form}
</div>
