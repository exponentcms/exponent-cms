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
                {img file_id=$item->product->expFile.mainimage[0]->id  h=50 w=50 zc=1 class="border"}
            {else}
                {img src="`$asset_path`images/no-image.jpg" alt="'No Image Available'|gettext" h=50 w=50 zc=1}
            {/if}
        </td>
        <td>
            <span class="itemname"><strong>{$item->products_name}</strong></span>
			 <div class="itembody">
				{if $message.To}
					{'To:'|gettext} {$message.To}
				{/if}
				
				{if $message.From}
					{'From'|gettext}: {$message.From}
				{/if}
				{br}{$message.Message|truncate:50:"..."}    
			</div>
        </td>
    </tr>
</table>