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

<table border="0" cellspacing="0" cellpadding="0" class="cart-item">
    <tr>
        <td class="cart-image">
            {if $item->product->expFile.images[0]->id}
                {img file_id=$item->product->expFile.images[0]->id square=35}
            {else}
                {'No Image Available'|gettext}
            {/if}
        </td>
        <td>
            <span class="itemname">{$item->products_name}</span>{br}
            {'Registering'|gettext} {$number} {'people'|gettext}:{br}
            {$people|truncate:50:"..."}        
        </td>
    </tr>
</table>


