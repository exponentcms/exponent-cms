{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

{css unique="manageusers" corecss="tables"}

{/css}

<div class="module migration manage-users">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Migrating Users and Groups"|gettext) module="migrate-users"}
        </div>
		<h1>{"Migrate Users and Groups"|gettext}</h1>	    
    </div>

    <p> 
        The following is a list of users and groups we found in the database {$config.database}.
        Select the users and groups you would like to pull over from {$config.database}.
		User and group permissions will NOT be migrated.
    </p>
    {form action="migrate_users"}
        <table class="exp-skin-table">
			<thead>
				<tr>
					<th>{"Migrate"|gettext}</th>
					<th>{"Replace"|gettext}</th>
					<th>{"Username"|gettext}</th>
					<th>{"Name"|gettext}</th>
					<th>{"E-Mail"|gettext}</th>
					<th>{"Admin"|gettext}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$users item=user name=users}
					<tr class="{cycle values="even,odd"}">            
						<td>
							{if ($user->exists == true)}
								<em>(exists)</em>
							{else}
								{control type="checkbox" name="users[]" label=" " value=$user->id checked=true}
							{/if}			
						</td>
						<td>
							{if ($user->exists == true)}
								{control type="checkbox" name="rep_users[]" label=" " value=$user->id checked=false}
							{else}
								<em>(new)</em>
							{/if}			
						</td>
						<td>
							{$user->username}
						</td>
						<td>
							{$user->firstname} {$user->lastname}
						</td>
						<td>
							{$user->email}
						</td>
						<td>
							{if $user->is_acting_admin == 1}{img src=$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}{/if}
						</td>            
					</tr>
				{foreachelse}
					<tr><td colspan=5>No users found to migrate from the database {$config.database}</td></tr>
				{/foreach}
			</tbody>
			<tbody>
				<tr><td colspan=5>{control type="checkbox" name="wipe_users" label="Erase all current users?" value=1 checked=false}</td></tr>
				<tr><td>&nbsp;</td></tr>
			</tbody>
			<thead>
				<tr>
					<th>{"Migrate"|gettext}</th>
					<th>{"Replace"|gettext}</th>
					<th>{"Group Name"|gettext}</th>
					<th>{"Description"|gettext}</th>
					<th>{"Type"|gettext}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$groups item=group name=groups}
					<tr class="{cycle values="even,odd"}">            
						<td>
							{if ($group->exists == true)}
								<em>(exists)</em>
							{else}
								{control type="checkbox" name="groups[]" label=" " value=$group->id checked=true}
							{/if}			
						</td>
						<td>
							{if ($group->exists == true)}
								{control type="checkbox" name="rep_groups[]" label=" " value=$group->id checked=false}
							{else}
								<em>(new)</em>
							{/if}			
						</td>
						<td>
							{$group->name}
						</td>
						<td>
							{$group->description}
						</td>
						<td>
							{if $group->inclusive}<b>{"Default"|gettext}</b>{else}{"Normal"|gettext}{/if}				
						</td>            
					</tr>
				{foreachelse}
					<tr><td colspan=5>No groups found to migrate from the database {$config.database}</td></tr>
				{/foreach}
			</tbody>
			<tr><td colspan=5>{control type="checkbox" name="wipe_groups" label="Erase all current groups?" value=1 checked=false}</td></tr>
        </table>
        {control type="buttongroup" submit="Migrate Users/Groups" cancel="Cancel"}
    {/form}
	{br}<hr>{br}
	<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link module=migration action=manage_pages}"><b>Next Step -> Migrate Pages</b></a>
</div>
