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
    <h1>{'Purchase Package Pickup'|gettext}</h1>
    {form action=save_pickup}
        {control type="hidden" name="id" value=$shipping->id}
        <h4>{$shipping->carrier} - {$shipping->option_title}</h4>
        {'Package'|gettext}:
        <ul>
            {if $shipping->predefinedpackage}
                <li>{$shipping->predefinedpackage}</li>
            {else}
                <li>{$shipping->width}in x {$shipping->height}in x {$shipping->length}in</li>
            {/if}
            <li>{$shipping->weight}lbs</li>
        </ul>
        {control type=dropdown name=pickuprate items=$rates label="Pickup Cost"|gettext}
        {"Start Date of Pickup"|gettext}: {$shipping->shipping_options.pickup_date|format_date:DISPLAY_DATETIME_FORMAT}
        {br}{"End Date of Pickup"|gettext}: {$shipping->shipping_options.pickup_date_end|format_date:DISPLAY_DATETIME_FORMAT}
        {br}{'Delivery Instructions'|gettext}: {$shipping->shipping_options.pickup_instructions}
        {control type="buttongroup" submit="Purchase Package Pickup"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
