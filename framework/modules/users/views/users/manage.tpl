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
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

<div class="module users manage">
	<h1>{$moduletitle|default:"Manage Users"}</h1>	
	<p>
        From here, you can create, modify and remove normal user accounts. 
        You will not be able to create, modify or remove administrator accounts (these options will be disabled).
    </p>
    {icon class="add" module="users" action="create" title="Create a New User" alt="Create a New User"}
	{$page->links}
	<table class="exp-skin-table">
	    <thead>
		<tr>
		    {$page->header_columns}
			<th>&nbsp;</th>
		</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=user name=listings}
			<tr class="{cycle values="odd,even"}">
				<td>{$user->username}</td>
				<td>{$user->firstname}</td>
				<td>{$user->lastname}</td>
				<td>{if $user->is_acting_admin == 1}{img src=`$smarty.const.ICON_RELATIVE`toggle_on.gif}{/if}</td>
			    <td>
			        {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
                    <div class="item-actions">
                    {icon img=edit.png action=edituser id=$user->id title="Edit"}
                    {icon img=lock.png action=change_password id=$user->id title="Change this users password"}
                    {icon img=delete.png action=delete id=$user->id title="Delete" onclick="return confirm('Are you sure you want to delete this user?');"}
                    </div>
                    {/permissions}
			    </td>
			</tr>
			{foreachelse}
			    <td colspan="{$page->columns|count}">No Data.</td>
			{/foreach}
		</tbody>
		</table>
		{$page->links}
</div>
