{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
            {if $item->product->expFile.mainimage[0]->id}
                <a style="margin: 0px; padding:0px" href="{link action=show controller=donation title=$item->product->getSEFURL()}">{img file_id=$item->product->expFile.mainimage[0]->id h=50 w=50 zc=1 class="border"}</a>
            {else}
                {img src="`$asset_path`images/no-image.jpg"}
                {'No Image Available'|gettext}
            {/if}
        </td>
        <td>
            <span class="itemname"><strong><a style="margin: 0px; padding:0px" href="{link action=show controller=donation title=$item->product->getSEFURL()}">{$item->products_name}</a></strong></span>
        </td>
    </tr>
</table>



