{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Users"|gettext) module="manage-users"}
        </div>
        {if $smarty.const.ECOM}
            <h2>{$moduletitle|default:"Manage Customers"|gettext}</h2>
        {else}
            <h2>{$moduletitle|default:"Manage Users"|gettext}</h2>
        {/if}
        <blockquote>
             {'From here, you can create, modify and remove normal user accounts.'|gettext}&#160;&#160;
             {'You will not be able to create, modify or remove administrator accounts (these options will be disabled).'|gettext}
         </blockquote>
    </div>
	<div class="module-actions">
		{icon class=add module=users action=create text="Create a New User"|gettext}
	</div>
    {br}
	<table id="users-manage">
	    <thead>
			<tr>
                <th>{'Username'|gettext}</th>
                <th>{'First Name'|gettext}</th>
                <th>{'Last Name'|gettext}</th>
                <th>{'Is Admin'|gettext}</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
        {if !$smarty.const.ECOM_LARGE_DB}
		<tbody>
			{foreach from=$page->records item=user name=listings}
                <tr>
                    <td>{$user->username}</td>
                    <td>{$user->firstname}</td>
                    <td>{$user->lastname}</td>
                    <td>
                        {if $user->is_acting_admin == 1}
                            <img src="{$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}">
                        {/if}
                    </td>
                    <td>
                        {permissions}
                            <div class="item-actions">
                                {if $smarty.const.ECOM}
                                    {icon img="view.png" class=view action=viewuser record=$user}
                                {/if}
                                {icon img="edit.png" class=edit action=edituser record=$user}
                                {if !$user->is_ldap}
                                {icon img="change_password.png" class=password action=change_password record=$user title="Change this users password"|gettext}
                                {/if}
                                {icon img="delete.png" action=delete record=$user title="Delete"|gettext onclick="return confirm('Are you sure you want to delete this user?');"}
                            </div>
                        {/permissions}
                    </td>
                </tr>
			{/foreach}
		</tbody>
        {/if}
	</table>
</div>

{script unique="users-showall" jquery='jquery.dataTables'}
{literal}
    $(document).ready(function() {
        var tableContainer = $('#users-manage');

        var table = tableContainer.DataTable({
    {/literal}
    {if $smarty.const.ECOM_LARGE_DB}
    {literal}
            processing: true,
            "language": {
                processing: '<span>Loading Records...</span> '
            },
            serverSide: true,
            ajax: eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=users&action=getUsersByJSON2&json=1",
    {/literal}
    {/if}
    {literal}
            pagingType: "full_numbers",
            stateSave: true,
            columns: [
                { data: 'username' },
                { data: 'firstname' },
                { data: 'lastname' },
                { data: 'is_acting_admin', searchable: false, orderable: true },
                { data: 'id', searchable: false, orderable: false },
            ],
            order: [[0, 'asc']],
            autoWidth: false,
        });
    } );
{/literal}
{/script}
