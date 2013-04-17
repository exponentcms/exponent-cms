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

 {css unique="purchase-orders" corecss="tables" link="`$asset_path`css/purchaseorder.css"}

{/css}
<div class="vendor show">
    <div class="module-actions">
        {icon action=manage_vendors class=manage text="Manage Vendors"|gettext}
   	</div>
	<h2>
		{$vendor_title}
	</h2>
	{permissions}
		{icon action=edit_vendor class="edit" id=$vendor->id}
		{icon action=delete_vendor class="delete" id=$vendor->id}
	{/permissions}
	<table class="exp-skin-table">
        <tbody>
			{foreach from=$vendor item=item key=key name=item}
				{if $item}
					<tr class='{cycle values="odd,even"}'>
						<th>
							{$key}:
						</th>
						 <td>
							{$item}
						</td>
					</tr>
				{/if}
			{/foreach}
        </tbody>
    </table>
</div>