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
                <th data-class="expand">{'Group Name'|gettext}</th>
                <th data-hide="phone">{'Description'|gettext}</th>
                <th data-hide="phone">{'Type'|gettext}</th>
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

{script unique="manage-groups" jquery='lodash.min,jquery.dataTables,DT_bootstrap,datatables.responsive'}
{literal}
    $(document).ready(function() {
        var responsiveHelper = undefined;
        var breakpointDefinition = {
            tablet: 1024,
            phone : 480
        };
        var tableElement = $('#groups-manage');

        tableElement.dataTable({
            sDom           : '<"row"<"span6"l><"span6"f>r>t<"row"<"span6"i><"span6"p>>',
            sPaginationType: 'bootstrap',
            "aoColumns": [
                null,
                null,
                null,
                { "bSearchable": false, "bSortable": false },
            ],
            oLanguage      : {
                sLengthMenu: '_MENU_ records per page'
            },
            bAutoWidth     : false,
            fnPreDrawCallback: function () {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper) {
                    responsiveHelper = new ResponsiveDatatablesHelper(tableElement, breakpointDefinition);
                }
            },
            fnRowCallback  : function (nRow) {
                responsiveHelper.createExpandIcon(nRow);
            },
            fnDrawCallback : function (oSettings) {
                responsiveHelper.respond();
            }
        });
    } );
{/literal}
{/script}
