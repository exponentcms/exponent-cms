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
 
{*{css unique="manage_groups" corecss="tables"}*}

{*{/css}*}

<div class="module users manage-group">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Managing User Groups"|gettext) module="manage-groups"}
        </div>
        <h1>{$moduletitle|default:"Manage User Groups"|gettext}</h1>
    </div>
	<blockquote>
        {'Groups are used to treat a set of users as a single entity, mostly for permission management.'|gettext}&#160;&#160;
        {'This form allows you to determine which users belong to which groups, create new groups, modify existing groups, and remove groups.'|gettext}{br}
        {'When a new user account is created, it will be automatically added to all groups with a Type of \'Default\''|gettext}
    </blockquote>
	<div class="module-actions">
		{icon class=add controller=users action=edit_group text="Create a New User Group"|gettext alt="Create a New User Group"|gettext}
	</div>
    {br}
    {*{pagelinks paginate=$page top=1}*}
	<table id="groups-manage">
	    <thead>
			<tr>
				{*{$page->header_columns}*}
                <th>{'Group Name'|gettext}</th>
                <th>{'Description'|gettext}</th>
                <th>{'Type'|gettext}</th>
                <th>{'Actions'|gettext}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=group name=listings}
                <tr>
                    <td>{$group->name}</td>
                    <td>{$group->description}</td>
                    <td>{if $group->inclusive}<strong>{'Default'|gettext}</strong>{else}{'Normal'|gettext}{/if}</td>
                    <td>
                        {permissions}
                            <div class="item-actions">
                                {icon img="groupperms.png" controller=users action="manage_group_memberships" record=$group title="Add/Remove Members to Group"|gettext|cat:" "|cat:$group->name}
                                {icon img="edit.png" controller=users action=edit_group record=$group title="Edit this group"|gettext}
                                {icon img="delete.png" controller=users action=delete_group record=$group title="Delete this group"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this group?"|gettext)|cat:"');"}
                            </div>
                        {/permissions}
                    </td>
                </tr>
			{foreachelse}
			    <tr><td colspan="{$page->columns|count}">{'No User Groups Available'|gettext}.</td></tr>
			{/foreach}
		</tbody>
	</table>
    {*{pagelinks paginate=$page bottom=1}*}
</div>

{script unique="groups-showall" jquery='jquery.dataTables'}
{literal}
    $(document).ready(function() {
        $('#groups-manage').dataTable({
            "sPaginationType": "full_numbers",
            "sDom": '<"top"lfip>rt<"bottom"ip<"clear">',  // pagination location
            "aoColumns": [
                null,
                null,
                null,
                { "bSearchable": false, "bSortable": false },
            ]
        });
    } );
{/literal}
{/script}
