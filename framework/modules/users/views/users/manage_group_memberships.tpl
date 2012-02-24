{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div class="module users manage-group-memberships">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Managing Group Memberships"|gettext) module="manage-group-members"}
        </div>
		<h1>{"Manage Group Memberships"|gettext}</h1>	    
    </div>

    {form action="update_memberships"}
    <input type="hidden" name="id" value="{$group->id}"/>
    {pagelinks paginate=$page top=1}
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
						{control type=checkbox name="memdata[`$user->id`][is_member]" value=1 checked=$user->is_member}
					</td>
					<td>
						{control type=checkbox name="memdata[`$user->id`][is_admin]" value=1 checked=$user->is_admin}
					</td>
				</tr>
			{foreachelse}
				<td colspan="{$page->columns|count}">{'No Data'|gettext}.</td>
			{/foreach}
		</tbody>
	</table>
    {pagelinks paginate=$page bottom=1}
    {control type="buttongroup" submit="Save Memberships"|gettext cancel="Cancel"|gettext}
    {/form}
</div>


