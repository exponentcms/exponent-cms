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

<div class="module order edit">
    <h1>{'Editing order totals'|gettext}</h1>
    {form action=save_totals}
        {control type=hidden name=orderid value=$order->id}
        {'You may manually update the order totals here.'|gettext} {br}
        * {'Keep in mind, if you edit, add, or remove order items, the order will automatically recalculate these totals.'|gettext} {br}
        <table width="80%">
            <tr>
                <td width="40%">
                    {control type=text name=subtotal label="Subtotal"|gettext value=$order->subtotal filter=money focus=1}
                </td>
                <td width="40%">
                    * {'This is the total of all order items.'|gettext}
                </td>
            </tr>
            <tr>
                <td>
                    {control type=text name=total_discounts label="Total Discounts"|gettext value=$order->total_discounts filter=money}
                </td>
                <td>
                    * {'Total discounts you want reflected on this order.'|gettext}
                </td>
            </tr>
            {if !$order->shipping_taxed}
                <tr>
                    <td>
                        {control type=text name=tax label="Tax" value=$order->tax filter=money}
                    </td>
                    <td>
                        * {'Total of tax for this order'|gettext}
                    </td>
                </tr>
            {/if}
            <tr>
                <td>
                    {control type=text name=shipping_total label="Shipping Total"|gettext value=$order->shipping_total filter=money}
                </td>
                <td>
                    * {'Total of shipping for this order.'|gettext}
                </td>
            </tr>
            {if $order->shipping_taxed}
                <tr>
                    <td>
                        {control type=text name=tax label="Tax" value=$order->tax filter=money}
                    </td>
                    <td>
                        * {'Total of tax for this order'|gettext}
                    </td>
                </tr>
            {/if}
            <tr>
                <td>
                    {control type=text name=surcharge_total label="Surcharge Total"|gettext value=$order->surcharge_total filter=money}
                </td>
                <td>
                    * {'Total of per-product freight surcharges.'|gettext}
                </td>
            </tr>
            <tr>
                <td>
                    {'You may enter the grand total manually, or select the checkbox below to auto calculate the grand total based on the other fields.'|gettext}
                    {control type=text name=grand_total label="Grand Total"|gettext value=$order->grand_total filter=money}
                </td>
                <td>
                    * {'Grand total of the order'|gettext}
                </td>
            </tr>
            <tr>
                <td>
                    {control type=checkbox label='Auto calculate grand total?'|gettext flip=true name=autocalc value=1 checked=true}
                </td>
                <td>
                    {control type=buttongroup submit="Save Totals"|gettext cancel="Cancel"|gettext}
                </td>
            </tr>
        </table>
    {/form}
</div>
