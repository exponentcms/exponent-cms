{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

{css unique="manageordertype" corecss="tables"}

{/css}

<div class="modules order_type showall">
	<h1>{$moduletitle|default:"Manage Order Types"|gettext}</h1>
	{icon class="add" action=create text='Create a new order type'|gettext}
	<div id="orders">
		{pagelinks paginate=$page top=1}
		<table id="prods" class="exp-skin-table">
			<thead>
				<tr>
				    <th>{'Default'|gettext}</th>
                    <th>{'Creates New User'|gettext}</th>
                    <th>{'Emails Customer'|gettext}</th>
                    <th>{'Affects Inventory'|gettext}</th>
					<th>{'Name'|gettext}</th>
					<th>{'Action'|gettext}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$page->records item=listing name=listings}
                    <tr class="{cycle values="odd,even"}">
                        <td>{if $listing->is_default == 1}{icon img="clean.png" color=green}{/if}</td>
                        <td>{if $listing->creates_new_user == 1}{icon img="clean.png" color=green}{/if}</td>
                        <td>{if $listing->emails_customer == 1}{icon img="clean.png" color=green}{/if}</td>
                        <td>{if $listing->affects_inventory == 1}{icon img="clean.png" color=green}{/if}</td>
                        <td>{$listing->title}</td>
                        <td>
                            {if $permissions.manage}
                                <div class="item-actions">
                                    {icon controller=order_type action=edit record=$listing}
                                    {icon controller=order_type action=delete record=$listing}
                                </div>
                            {/if}
                        </td>
                    </tr>
				{foreachelse}
				    <tr class="{cycle values="odd,even"}">
				        <td colspan="4">{'No order types have been created yet.'|gettext}</td>
				    </tr>
				{/foreach}
		</tbody>
		</table>
		{pagelinks paginate=$page bottom=1}
	</div>
</div>
