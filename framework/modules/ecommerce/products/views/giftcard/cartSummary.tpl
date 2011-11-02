{*
* Copyright (c) 2007-2008 OIC Group, Inc.
* Written and Designed by Adam Kessler
*
* This file is part of Exponent
*
* Exponent is free software; you can redistribute
* it and/or modify it under the terms of the GNU
* General Public License as published by the Free
* Software Foundation; either version 2 of the
* License, or (at your option) any later version.
* GPL: http://www.gnu.org/licenses/gpl.txt
*
*}


<table border="0" cellspacing="0" cellpadding="0" class="cart-item">
    <tr>
        <td class="cart-image">
            {if $item->product->expFile.mainimage[0]->id}
                {img file_id=$item->product->expFile.mainimage[0]->id  h=50 w=50 zc=1 class="border"}
            {else}
                No Image Available
            {/if}
        </td>
        <td>
            <span class="itemname"><strong>{$item->products_name}</strong></span>
			 <div class="itembody">
				 To: {$message.To} From: {$message.From} 
				{br}{$message.Message|truncate:50:"..."}    
			</div>
        </td>
    </tr>
</table>