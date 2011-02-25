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
    <h1>Migrate Users</h1>
    <p> 
        The following is a list of users we found in the {$config.database}.
        Select the users you would like to pull over from {$config.database}.
    </p>
    
    {form action="migrate_users"}
        <table class="exp-skin-table">
        <thead>
            <tr>
                <th>Migrate</th>
                <th>Username</th>
                <th>Name</th>
                <th>E-Mail</th>
                <th>Admin?</th>
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
                {if ($user->is_acting_admin==1)}<b>Yes</b>{else}No{/if}
            </td>            
        </tr>
        {foreachelse}
			<tr><td colspan=2>No users found in database {$config.database}</td></tr>
        {/foreach}
        </tbody>
        </table>
        {control type="buttongroup" submit="Migrate Users" cancel="Cancel"}
    {/form}
</div>