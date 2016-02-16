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
    <h1>{'Order Package Pickup'|gettext}</h1>
    {form action=edit_pickup2 horizontal=1}
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
        {$now = time()}
        {$next = strtotime('+1 day')}
        {control type="popupdatetime" name="pickupdate" label="Start Date of Pickup"|gettext value=$now}
        {control type="popupdatetime" name="pickupenddate" label="End Date of Pickup"|gettext value=$next}
        {control type="textarea" name="instructions" label='Delivery Instructions'|gettext}
        {control type="buttongroup" submit="Order Package Pickup"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
