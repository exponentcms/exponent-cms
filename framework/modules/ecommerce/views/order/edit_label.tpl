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
    <h1>{'Purchase Shipping Label'|gettext}</h1>
    {form action=save_label}
        {control type="hidden" name="id" value=$shipping->id}
        {* FIXME may have to note that rate was adjusted *}
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
        {br}{'Cost'|gettext}: {$cost|currency}
        {control type="buttongroup" submit="Purchase Label"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
