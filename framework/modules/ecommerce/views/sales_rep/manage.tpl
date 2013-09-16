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

<div class="modules order_type showall">
	<h1>{$moduletitle|default:"Sales Rep Administration"|gettext}</h1>
	{icon class="add" action=create text='Create a new Sales Rep'|gettext}
	<div id="orders">
		{$page->links}
		<table id="prods" class="exp-skin-table">
			<thead>
				<tr>
				    <th>{'First Name'|gettext}</th>
					<th>{'Last Name'|gettext}</th>
					<th>{'Initials'|gettext}</th>
                    <th>{'Action'|gettext}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$page->records item=listing name=listings}
                    <tr class="{cycle values="odd,even"}">
                        <td>{$listing->first_name}</td>
                        <td>{$listing->last_name}</td>
                        <td>{$listing->initials}</td>
                        <td>
                            {if $permissions.manage}
                                {icon controller=sales_rep action=edit img='edit.png' id=$listing->id}
                                {icon controller=sales_rep action=delete img='delete.png' id=$listing->id}
                            {/if}
                        </td>
                    </tr>
				{foreachelse}
				    <tr class="{cycle values="odd,even"}">
				        <td colspan="4">{'No sales reps have been created yet.'|gettext}</td>
				    </tr>
				{/foreach}
		    </tbody>
		</table>
	</div>
</div>
