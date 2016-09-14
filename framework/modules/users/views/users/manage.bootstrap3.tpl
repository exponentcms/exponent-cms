{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{css unique="manage-groups" corecss="datatables-tools"}

{/css}

<div class="module users manage">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Users"|gettext) module="manage-users"}
        </div>
        <h2>{$moduletitle|default:"Manage Users"|gettext}</h2>
        <blockquote>
            {'From here, you can create, modify and remove normal user accounts.'|gettext}&#160;&#160;
            {'You will not be able to create, modify or remove administrator accounts (these options will be disabled).'|gettext}
        </blockquote>
    </div>
	<div class="module-actions">
		{icon class=add module=users action=create text="Create a New User"|gettext}
	</div>
    {br}
    {$table_filled = true}
	<table id="users-manage">
	    <thead>
			<tr>
                <th data-class="expand">{'Username'|gettext}</th>
                <th data-hide="phone" data-name="First">{'First Name'|gettext}</th>
                <th data-hide="phone" data-name="Last">{'Last Name'|gettext}</th>
                <th data-hide="phone" data-name="Admin">{'Is Admin'|gettext}</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
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
                                {icon img="edit.png" class=edit action=edituser record=$user}
                                {icon img="change_password.png" class=password action=change_password record=$user title="Change this users password"|gettext}
                                {icon img="delete.png" action=delete record=$user title="Delete"|gettext onclick="return confirm('Are you sure you want to delete this user?');"}
                            </div>
                        {/permissions}
                    </td>
                </tr>
			{foreachelse}
                {$table_filled = false}
			    <td colspan="5"><h4>{'No Users'|gettext}</h4></td>
			{/foreach}
		</tbody>
	</table>
</div>

{if $table_filled}
{script unique="manage-users" jquery='jquery.dataTables,dataTables.tableTools,dataTables.bootstrap3,datatables.responsive'}
{literal}
    $(document).ready(function() {
        var responsiveHelper;
        var breakpointDefinition = {
            tablet: 1024,
            phone : 480
        };
        var tableContainer = $('#users-manage');

        var table = tableContainer.DataTable({
            columns: [
                null,
                null,
                null,
                { searchable: false, orderable: true },
                { searchable: false, orderable: false },
            ],
            autoWidth: false,
            preDrawCallback: function () {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper) {
                    responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
                }
            },
            rowCallback: function (nRow) {
                responsiveHelper.createExpandIcon(nRow);
            },
            drawCallback: function (oSettings) {
                responsiveHelper.respond();
            }
        });
        var tt = new $.fn.dataTable.TableTools( table, { sSwfPath: EXPONENT.JQUERY_RELATIVE+"addons/swf/copy_csv_xls_pdf.swf" } );
        $( tt.fnContainer() ).insertBefore('div.dataTables_wrapper');
    } );
{/literal}
{/script}
{/if}
