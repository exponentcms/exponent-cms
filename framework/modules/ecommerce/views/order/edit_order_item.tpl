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

<div class="module order edit">
    <h1>{'Editing order item'|gettext}</h1>
    {form action=save_order_item}
        {control type=hidden name=id value=$oi->id}
        {control type=hidden name=orderid value=$oi->orders_id}
        <blockquote>
            {'You may change the item quantity here, price, as well as edit the options and user input fields.'|gettext}{br}
            {'If you would like to change the product, please delete it and add the correct item.'|gettext}{br}
            {'Note'|gettext}:{br}
            <strong>* {'If you edit, add, or remove order items, the order will automatically recalculate the order totals.'|gettext}</strong>{br}
            <strong>* {'If this item has product options and those options modify the price, YOU must adjust the price below manually if you change the options. This will NOT recalculate the option price modifiers automatically.'|gettext}</strong>{br}
        </blockquote>
        <table width='60%'>
            <tr>
                <td>{'Item name:'|gettext}</td>
                <td>{control type=textarea name=products_name cols=40 rows=2 label="" value=$oi->products_name focus=1}</td>
            </tr>
            {if $oi->product_type == 'product'}
            <tr>
                <td>{'Item model:'|gettext}</td>
                <td>{$oi->products_model}</td>
            </tr>
            {/if}
            <tr>
                <td>{'Item price:'|gettext}</td>
                <td>{control type=text name=products_price label="" value=$oi->products_price filter=money}</td>
            </tr>
            <tr>
                <td>{'Item quantity:'|gettext}</td>
                <td>{control type=text name=quantity label="" value=$oi->quantity}</td>
            </tr>
            {if $oi->product_type == 'product'}
            <tr>
                <td>{'Status:'|gettext}</td>
                <td>{control type="dropdown" name="product_status_id" frommodel=product_status items=$status_display orderby=rank value=$oi->products_status}</td>
            </tr>
            {/if}
        </table>

        {$product = $oi->product}
        {* NOTE display product options *}
        {*{exp_include file="options.tpl"}*}
        {include file="`$smarty.const.BASE`framework/modules/ecommerce/views/store/options.tpl"}

        {* NOTE display product user input fields *}
        {*{exp_include file="input_fields.tpl"}*}
        {include file="`$smarty.const.BASE`framework/modules/ecommerce/views/store/input_fields.tpl"}

        {control type=buttongroup submit="Save Order Item Change"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
