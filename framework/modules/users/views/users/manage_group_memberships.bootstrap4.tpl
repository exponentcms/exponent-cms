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

{css unique="manage-groups"}
{literal}
    table.dataTable thead > tr {
        font-size-adjust: 0.4;
    }
    table.dataTable thead > tr > th {
        padding-left: 5px;
        padding-top: 0;
        padding-bottom: 0;
        vertical-align: top;
    }
    .row-detail .yadcf-filter-wrapper {
        display: none;
    }
    table.dataTable thead .sorting,
    table.dataTable thead .sorting_asc,
    table.dataTable thead .sorting_desc  {
        background-image: none;
    }
    .dataTables_wrapper .dataTables_processing {
        background: lightgray;
        height: 55px;
        border: 1px black solid;
    }
{/literal}
{/css}

<div class="module users manage-group-memberships">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Managing Group Memberships"|gettext) module="manage-group-members"}
        </div>
		<h2>{"Manage Group Memberships"|gettext} - {$group->name}</h2>
    </div>

    {form id="myform" action="update_memberships"}
        {control type="hidden" name="id" value=$group->id}
        <table id="groups-manage" class="table">
            <thead>
                <tr>
                    {*<th>id</th>*}
                    <th data-class="expand">{'Username'|gettext}</th>
                    <th data-hide="phone" data-name="First">{'First Name'|gettext}</th>
                    <th data-hide="phone" data-name="Last">{'Last Name'|gettext}</th>
                    <th data-name="Member">{'Is Member'|gettext}</th>
                    <th data-hide="phone" data-name="Admin">{'Is Admin'|gettext}</th>
                </tr>
            </thead>
            {*{if !$smarty.const.ECOM_LARGE_DB}*}
            <tbody>
                {foreach from=$page->records item=grp_user name=listings}
                    <tr>
                        {*<td>{$grp_user->id}</td>*}
                        <td>{$grp_user->username}</td>
                        <td>{$grp_user->firstname}</td>
                        <td>{$grp_user->lastname}</td>
                        <td>
                            {*{$grp_user->is_member}*}
                            {control type=checkbox name="memdata[`$grp_user->id`][is_member]" value=1 checked=$grp_user->is_member}
                        </td>
                        <td>
                            {*{$grp_user->is_admin}*}
                            {control type=checkbox name="memdata[`$grp_user->id`][is_admin]" value=1 checked=$grp_user->is_admin}
                        </td>
                    </tr>
                {foreachelse}
                    <td colspan="5"><h4>{'No Data'|gettext}</h4></td>
                {/foreach}
            </tbody>
            {*{/if}*}
        </table>
        {control type="buttongroup" submit="Save Memberships"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="manage-groups" jquery='jquery.dataTables,dataTables.checkboxes'}
{literal}
    $(document).ready(function() {
        // var responsiveHelper;
        // var breakpointDefinition = {
        //     tablet: 1024,
        //     phone : 480
        // };
        var table = $('#groups-manage').DataTable({
            stateSave: true,
//            autoWidth: false,
//             order: [[0, 'asc']],
        {/literal}
        {*
        {if $smarty.const.ECOM_LARGE_DB}
        {literal}
            processing: true,
            "language": {
                processing: '<i class="fas fa-spinner fa-spin fa-fw"></i> <span>Loading Records...</span> '
            },
            serverSide: true,
            ajax: eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=users&action=getUsersByJSON3&group={/literal}{$group->id}{literal}&json=1",
        {/literal}
        {/if}
        *}
        {literal}
            columns: [
                { data: 'username' },
                { data: 'firstname' },
                { data: 'lastname' },
                { data: 'group_id', searchable: false, orderable: true },
                { data: 'is_admin', searchable: false, orderable: false },
            ],
            // columnDefs: [
            //     {
            //         targets: [3,4],
            //         data: 'id',
                    // checkboxes: {
                    //     selectRow: true
                    // },
                    // createdCell:  function (td, cellData, rowData, row, col){
                    //     if( col == 3 && rowData['group_id'] == '{/literal}{$group->id}{literal}'){
                            // this.api().cell(td).checkboxes.select();
                        // }
                        // if( col == 4 && rowData['is_admin'] == '1'){
                        //     this.api().cell(td).checkboxes.select();
                        // }
                    // }
                // },
                // {
                //     targets: 5,
                //     data: 'group_id',
                //     visible: false
                // },
                // {
                //     targets: 6,
                //     data: 'is_admin',
                //     visible: false
                // }
            // ],
        });

        // Handle form submission event
//         $('#myform').on('submit', function(e){
//             var form = this;
//
// // var rows_selected1 = table.data().columns().checkboxes.selected();
//             var rows_selected = table.column(3).checkboxes.selected();
//             // Iterate over all selected checkboxes
//             $.each(rows_selected, function(index, rowId){
//                 // Create a hidden element
//                 $(form).append(
//                     $('<input>')
//                     .attr('type', 'hidden')
//                     .attr('name', 'memdata['+rowId+'][is_member]')
//                     .val(rowId)
//                 );
//             });
//
//             var rows_selected = table.column(4).checkboxes.selected();
//             // Iterate over all selected checkboxes
//             $.each(rows_selected, function(index, rowId){
//                 // Create a hidden element
//                 $(form).append(
//                     $('<input>')
//                     .attr('type', 'hidden')
//                     .attr('name', 'memdata['+rowId+'][is_admin]')
//                     .val(rowId)
//                 );
//             });
//         });

        // Handle form submission event
        $('#myform').on('submit', function(e){
           var form = this;

           // Iterate over all checkboxes in the table
           table.$('input[type="checkbox"]').each(function(){
              // If checkbox doesn't exist in DOM
              if(!$.contains(document, this)){
                 // If checkbox is checked
                 if(this.checked){
                    // Create a hidden element
                    $(form).append(
                       $('<input>')
                          .attr('type', 'hidden')
                          .attr('name', this.name)
                          .val(this.value)
                    );
                 }
              }
           });
        });
    });
{/literal}
{/script}
