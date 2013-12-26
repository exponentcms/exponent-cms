{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="cart show inline">
	<h1>{$moduletitle}</h1>
	<ul>
		{foreach from=$items item=item}
			<li>
				<a href="{link controller=store action=show id=$item->product_id}">{img file_id=$item->product->files[0]->id square=55}{$item->products_name}</a>
                <span class="price">{$item->products_price|currency} ({"quantity"|gettext}: {$item->quantity})</span>
				<a href="{link action=removeItem id=$item->id}" class="removefromcart">{"Remove from cart"|gettext}</a>
				{clear}
			</li>
		{foreachelse}
            <li>{'You currently have no items in your cart'|gettext}</li>
        {/foreach}
	</ul>
    {br}<em>{"Cart Total"|gettext}: {$total|currency}</em>
	{br}<a class="checkout" href="{securelink action="checkout"}">{'Checkout Now'|gettext}</a>
</div>
