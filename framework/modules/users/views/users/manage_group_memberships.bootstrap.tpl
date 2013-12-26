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
 
{css unique="group" corecss="tables"}

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
                    <th data-class="expand">{'Username'|gettext}</th>
                    <th data-hide="phone">{'First Name'|gettext}</th>
                    <th data-hide="phone">{'Last Name'|gettext}</th>
                    <th>{'Is Member'|gettext}</th>
                    <th data-hide="phone">{'Is Admin'|gettext}</th>
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
            sDom           : '<"row-fluid"<"span6"l><"span6"f>r>t<"row-fluid"<"span6"i><"span6"p>>',
            sPaginationType: 'bootstrap',
            "aoColumns": [
                null,
                null,
                null,
                { "bSearchable": false, "bSortable": false },
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
