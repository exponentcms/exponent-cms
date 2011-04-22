{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
 
<div class="module users manage-group-memberships">
	<h1>{$moduletitle|default:"Manage Group Membership"}</h1>	

	{$page->links}
	<table class="exp-skin-table">
		<thead>
			<tr>
				{$page->header_columns}
			</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=user name=listings}
				<tr class="{cycle values="odd,even"}">
					<td>{$user->username}</td>
					<td>{$user->firstname}</td>
					<td>{$user->lastname}</td>
					<td>
						{control type=checkbox name=is_member value=1 checked=$user->is_member}
					</td>
					<td>
						{control type=checkbox name=is_admin value=1 checked=$user->is_admin}
					</td>
				</tr>
			{foreachelse}
				<td colspan="{$page->columns|count}">No Data.</td>
			{/foreach}
		</tbody>
	</table>
	{$page->links}
</div>