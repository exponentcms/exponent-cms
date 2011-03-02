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

<div class="module migration manage-users">
    <h1>Migrate Users and Groups</h1>
    <p> 
        The following is a list of users and groups we found in the database {$config.database}.
        Select the users and groups you would like to pull over from {$config.database}.
		User and group permissions will NOT be migrated.  Group assignments will likewise NOT be migrated.
		You must reassign users to groups after migration and reassign permissions for new users and groups.
    </p>
    
    {form action="migrate_users"}
        <table class="exp-skin-table">
        <thead>
            <tr>
                <th>Migrate</th>
                <th>Username</th>
                <th>Name</th>
                <th>E-Mail</th>
                <th>Is Admin</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$users item=user name=users}
        <tr class="{cycle values="even,odd"}">            
            <td>
                {control type="checkbox" name="users[]" label=" " value=$user->id checked=true}
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
				{if $user->is_acting_admin == 1}{img src=`$smarty.const.ICON_RELATIVE`toggle_on.gif}{/if}
            </td>            
        </tr>
        {foreachelse}
			<tr><td colspan=5>No users found to migrate from the database {$config.database}</td></tr>
        {/foreach}
        </tbody>
		<tbody>
		<tr><td>&nbsp;</td></tr>
        </tbody>
        <thead>
            <tr>
                <th>Migrate</th>
                <th>Group Name</th>
                <th>Description</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$groups item=group name=groups}
        <tr class="{cycle values="even,odd"}">            
            <td>
                {control type="checkbox" name="groups[]" label=" " value=$group->id checked=true}
            </td>
            <td>
                {$group->name}
            </td>
            <td>
                {$group->description}
            </td>
            <td>
				{if $group->inclusive}<b>Default</b>{else}Normal{/if}				
            </td>            
        </tr>
        {foreachelse}
			<tr><td colspan=4>No groups found to migrate from the database {$config.database}</td></tr>
        {/foreach}
        </tbody>
        </table>
        {control type="buttongroup" submit="Migrate Users/Groups" cancel="Cancel"}
    {/form}
</div>