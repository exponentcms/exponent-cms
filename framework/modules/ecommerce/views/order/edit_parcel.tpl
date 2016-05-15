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

{css unique="showorder" link="`$asset_path`css/ecom.css" corecss="tables"}

{/css}

<div class="module order edit_parcel">
    <h1>{'Create Shipping Package'|gettext}</h1>
    {form action=save_parcel}
        {*{control type="hidden" name="id" value=$orderid}*}
        {control type="hidden" name="id" value=$shipping->id}
        {control type=dropdown id=packages name=predefinedpackage items=$shipping->calculator->getPackages($shipping->carrier) includeblank="Custom Size"|gettext value=$default->package label="Package Type"|gettext}
        {group label="Custom Package Size"|gettext id=custompkg}
            {control type="text" name="width" label="Width (in inches)"|gettext size=4 filter=decimal value=$shipping->calculator->configdata.default_width}
            {control type="text" name="height" label="Height (in inches)"|gettext size=4 filter=decimal value=$shipping->calculator->configdata.default_height}
            {control type="text" name="length" label="Length (in inches)"|gettext size=4 filter=decimal value=$shipping->calculator->configdata.default_length}
        {/group}
        {control type="text" name="weight" label="Total Weight (in pounds)"|gettext size=4 filter=decimal value=$shipping->calculator->configdata.default_max_weight}
        <h3>{'Items in this package'|gettext}</h3>
        <table class="exp-skin-table">
            <thead>
                <th>{'Included'|gettext}</th>
                <th>SKU</th>
                <th>{'Product'|gettext}</th>
                <th>{'Quantity'|gettext}</th>
            </thead>
            <tbody>
            {foreach $shipping->orderitem as $oi}
                <tr>
                    {*<td>{control type=checkbox name="in_box[`$oi->id`]" value=1 checked=1}</td>   *}{* FIXME enable *}
                    <td>{control type=hidden name="in_box[`$oi->id`]" value=1}{control type=checkbox name="in_box[`$oi->id`]" value=1 checked=1 disabled=1}</td>
                    <td>{$oi->products_model}</td>
                    <td>{$oi->products_name}</td>
                    {*<td>{control type=number name="qty[`$oi->id`]" min=1 max=$oi->quantity value=$oi->quantity}</td>  *}{* FIXME adjustable quantity *}
                    <td>{control type=hidden name="qty[`$oi->id`]" value=$oi->quantity}{$oi->quantity}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {control type="buttongroup" submit="Save Parcel"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="edit-parcel" jquery=1}
{literal}
    $(document).ready(function() {
        var packagepicker = $('#packages'); // the packages dropdown
        var custompkg = $('#custompkg'); // the custom package settings div

        //listens for a change in the packages dropdown
        packagepicker.on('change', function (e) {
            if (e.target.value != '') {
                custompkg.hide("slow");
            } else {
                custompkg.show("slow");
            }
        });
    });
{/literal}
{/script}
