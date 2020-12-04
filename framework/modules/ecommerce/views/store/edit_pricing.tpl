{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

{control type="hidden" name="tab_loaded[pricing]" value=1}
{if count($record->childProduct)}
    <h4><em>({'Price is determined by Child products.'|gettext})</em></h4>
{/if}
{group label="General Pricing"|gettext}
    <table>
        <tr>
            <td>{control type="text" name="pricing[base_price]" label="Base Price"|gettext value=$record->base_price filter=decimal size=15}</td>
            <td>{control type="text" name="pricing[special_price]" label="Special Price"|gettext value=$record->special_price filter=decimal size=15}</td>
        </tr>
        <tr>
            <td colspan="2">{control type="checkbox" name="pricing[use_special_price]" label="Use Special Price"|gettext value=1 checked=$record->use_special_price postfalse=1}</td>
        </tr>
    </table>
{/group}
{group label="Quantity Discounts"|gettext}
    <blockquote>
        {'Quantity discounts are discounts that get applied when a customer purchases a certain amount of this product.'|gettext}
        {'You can configure how the discount works by setting the discount rules below.'|gettext}{br}
    </blockquote>
    <table class="qty-discount">
        <tr>
            <td>{'If a customer purchases more than'|gettext} </td>
            <!--td>{control type="dropdown" name="pricing[quantity_discount_num_items_mod]" items=$record->quantity_discount_items_modifiers value=$record->quantity_discount_num_items}</td-->
            <td>{control type="text" name="pricing[quantity_discount_num_items]" value=$record->quantity_discount_num_items size=3 filter=integer}</td>
            <td>{'items, then discount the price by'|gettext}</td>
            <td>{control type="text" name="pricing[quantity_discount_amount]" value=$record->quantity_discount_amount size=3 filter=decimal}
            <td>{control type="dropdown" name="pricing[quantity_discount_amount_mod]" items=$record->quantity_discount_amount_modifiers value=$record->quantity_discount_amount_mod}</td>
        </tr>
        <tr>
            <td colspan="6">{control type="checkbox" name="pricing[quantity_discount_apply]" label="Only apply discount to the items over the discount limit"|gettext value=1 checked=$record->quantity_discount_apply postfalse=1}</td>
        </tr>
    </table>
{/group}
{group label="Tax Class"|gettext}
    {control type="dropdown" name="pricing[tax_class_id]" label="" frommodel=taxclass key=id display=name includeblank="-- No Tax Required --"|gettext value=$record->tax_class_id|default:1}
    {icon controller="tax" action="manage" text="Manage Taxes"|gettext}
{/group}