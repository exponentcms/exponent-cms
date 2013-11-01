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

{css unique="managemessages" corecss="tables"}

{/css}

<div class="modules order_status manage-messages">
	<h1>{$moduletitle|default:"Manage Order Status Messages"|gettext}</h1>
	{icon class="add" action=edit_message text='Add a new message'|gettext}
	<div id="orders">
		{pagelinks paginate=$page top=1}
		<table id="prods" class="exp-skin-table">
			<thead>
				<tr>
				    <th>{'Body'|gettext}</th>
					<th>{'Action'|gettext}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$page->records item=listing name=listings}
                    <tr class="{cycle values="odd,even"}">
                        <td>{$listing->body}</td>
                        <td>
                            {if $permissions.manage}
                                {icon controller=order_status action=edit_message img='edit.png' record=$listing}
                                {icon controller=order_status action=delete_message img='delete.png' record=$listing}
                            {/if}
                        </td>
                    </tr>
				{foreachelse}
				    <tr class="{cycle values="odd,even"}">
				        <td colspan="4">{'No order status messages have been created yet.'|gettext}</td>
				    </tr>
				{/foreach}
            </tbody>
		</table>
		{pagelinks paginate=$page bottom=1}
	</div>
</div>
