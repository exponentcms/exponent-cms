{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
 * Written and Designed by James Hunt
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
 
{css unique="group" corecss="tables"}

{/css}

 

<div class="form_header">
    <h1>{$_TR.form_title}</h1>
    <p>
    {$_TR.form_header|sprintf:$group->name}
    </p
</div>


{$page->links}
<table class="exp-skin-table">
    <thead>
	<tr>
	    {$page->header_columns}
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
		{foreach from=$page->records item=group name=listings}
		<tr class="{cycle values="odd,even"}">
			<td>{$group->name}</td>
			<td>{$group->description}</td>
			<td>{if $group->inclusive}<b>Default</b>{else}Normal{/if}</td>
		    <td>
		        {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
                <div class="item-actions">
                {icon img=edit.png controller=users action=edit_group id=$group->id title="Edit"}
                {icon controller=users action=delete id=$user->id title="Delete" onclick="return confirm('Are you sure you want to delete this user?');"}
                {icon img=groupperms.png controller=users action="manage_group_memberships" id=$group->id title="Add/Remove Members to Group `$group->name`"}
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
