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

{css unique="manageusers" corecss="tables"}

{/css}

<div class="module migration manage-users">
	{*<a class="{button_style}" href="{link module=migration action=manage_pages}"><strong>{'Skip to Next Step -> Migrate Pages'|gettext}</strong></a>*}
    {icon button=true module=migration action=manage_pages text='Skip to Next Step -> Migrate Pages'|gettext}
    {br}{br}<hr />
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Migrating Users and Groups"|gettext) module="migrate-users"}
        </div>
		<h1>{"Migrate Users and Groups"|gettext}</h1>	    
    </div>
    <blockquote>
        {'The following is a list of users and groups we found in the database'|gettext} {$config.database}.
        {'Select the users and groups you would like to pull over from'|gettext} {$config.database}.
		{'User and group permissions will NOT be migrated.'|gettext}
    </blockquote>
    {form action="migrate_users"}
        <table class="exp-skin-table">
			<thead>
				<tr>
					<th><input type='checkbox' name='checkallmu' title="{'Select All/None'|gettext}" onchange="selectAllmu(this.checked)" checked=1> {"Migrate"|gettext}</th>
					<th><input type='checkbox' name='checkallru' title="{'Select All/None'|gettext}" onchange="selectAllru(this.checked)"> {"Replace"|gettext}</th>
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
					<tr><td colspan=5>{'No users found to migrate from the database'|gettext} {$config.database}</td></tr>
				{/foreach}
			</tbody>
			<tbody>
				<tr><td colspan=5>{control type="checkbox" name="wipe_users" label="Erase all current users"|gettext|cat:"?" value=1 checked=false}</td></tr>
				<tr><td>&#160;</td></tr>
			</tbody>
			<thead>
				<tr>
					<th><input type='checkbox' name='checkallmg' title="{'Select All/None'|gettext}" onchange="selectAllmg(this.checked)" checked=1> {"Migrate"|gettext}</th>
					<th><input type='checkbox' name='checkallrg' title="{'Select All/None'|gettext}" onchange="selectAllrg(this.checked)"> {"Replace"|gettext}</th>
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
							{if $group->inclusive}<strong>{"Default"|gettext}</strong>{else}{"Normal"|gettext}{/if}
						</td>            
					</tr>
				{foreachelse}
					<tr><td colspan=5>{'No groups found to migrate from the database'|gettext} {$config.database}</td></tr>
				{/foreach}
			</tbody>
			<tr><td colspan=5>{control type="checkbox" name="wipe_groups" label="Erase all current groups"|gettext|cat:"?" value=1 checked=false}</td></tr>
        </table>
        {control type="buttongroup" submit="Migrate Users/Groups"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="manageusers"}
    function selectAllmu(val) {
        var checks = document.getElementsByName("users[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }

    function selectAllru(val) {
        var checks = document.getElementsByName("rep_users[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
    function selectAllmg(val) {
        var checks = document.getElementsByName("groups[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }

    function selectAllrg(val) {
        var checks = document.getElementsByName("rep_groups[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/script}
