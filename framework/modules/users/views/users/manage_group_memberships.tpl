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
 
{*{css unique="group" corecss="tables"}*}

{*{/css}*}
{css unique="manage-groups" corecss="datatables-tools"}

{/css}

<div class="module users manage-group-memberships">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Managing Group Memberships"|gettext) module="manage-group-members"}
        </div>
		<h2>{"Manage Group Memberships"|gettext} - {$group->name}</h2>
    </div>

    {form action="update_memberships"}
        {*<input type="hidden" name="id" value="{$group->id}"/>*}
        {control type="hidden" name="id" value=$group->id}
        {*{pagelinks paginate=$page top=1}*}
        {$table_filled = true}
        <table id="groups-manage">
            <thead>
                <tr>
                    {*{$page->header_columns}*}
                    <th>{'Username'|gettext}</th>
                    <th>{'First Name'|gettext}</th>
                    <th>{'Last Name'|gettext}</th>
                    <th>{'Is Member'|gettext}</th>
                    <th>{'Is Admin'|gettext}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$page->records item=grp_user name=listings}
                    <tr>
                        <td>{$grp_user->username}</td>
                        <td>{$grp_user->firstname}</td>
                        <td>{$grp_user->lastname}</td>
                        <td>
                            {control type=checkbox name="memdata[`$grp_user->id`][is_member]" value=1 checked=$grp_user->is_member}
                        </td>
                        <td>
                            {control type=checkbox name="memdata[`$grp_user->id`][is_admin]" value=1 checked=$grp_user->is_admin}
                        </td>
                    </tr>
                {foreachelse}
                    {$table_filled = false}
                    <td colspan="5"><h4>{'No Data'|gettext}</h4></td>
                {/foreach}
            </tbody>
        </table>
        {*{pagelinks paginate=$page bottom=1}*}
        {control type="buttongroup" submit="Save Memberships"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{if $table_filled}
{script unique="groups-showall" jquery='jquery.dataTables,dataTables.tableTools'}
{literal}
    $(document).ready(function() {
        $('#groups-manage').DataTable({
            pagingType: "full_numbers",
//            dom: 'T<"top"lfip>rt<"bottom"ip<"clear">',  // pagination location
            dom: 'T<"clear">lfrtip',
            tableTools: {
                sSwfPath: EXPONENT.JQUERY_RELATIVE+"addons/swf/copy_csv_xls_pdf.swf"
            },
            columns: [
                null,
                null,
                null,
                { searchable: false, orderable: false },
                { searchable: false, orderable: false },
            ]
        });
    } );
{/literal}
{/script}
{/if}