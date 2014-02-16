{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
{css unique="manage-groups" link="`$asset_path`css/datatables-tools.css"}

{/css}

<div class="module users manage-group-memberships">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Managing Group Memberships"|gettext) module="manage-group-members"}
        </div>
		<h1>{"Manage Group Memberships"|gettext}</h1>	    
    </div>

    {form action="update_memberships"}
        <input type="hidden" name="id" value="{$group->id}"/>
        {*{pagelinks paginate=$page top=1}*}
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
                {foreach from=$page->records item=user name=listings}
                    <tr>
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
                    <td colspan="5">{'No Data'|gettext}.</td>
                {/foreach}
            </tbody>
        </table>
        {*{pagelinks paginate=$page bottom=1}*}
        {control type="buttongroup" submit="Save Memberships"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="groups-showall" jquery='jquery.dataTables,dataTables.tableTools'}
{literal}
    $(document).ready(function() {
        $('#groups-manage').dataTable({
            sPaginationType: "full_numbers",
//            sDom: 'T<"top"lfip>rt<"bottom"ip<"clear">',  // pagination location
            dom: 'T<"clear">lfrtip',
            aoColumns: [
                null,
                null,
                null,
                { "bSearchable": false, "bSortable": false },
                { "bSearchable": false, "bSortable": false },
            ]
        });
    } );
{/literal}
{/script}
