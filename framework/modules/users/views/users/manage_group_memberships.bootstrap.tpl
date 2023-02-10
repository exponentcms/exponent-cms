{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

<div class="module users manage-group-memberships">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help with"|gettext|cat:" "|cat:("Managing Group Memberships"|gettext) module="manage-group-members"}
        </div>
		<h2>{"Manage Group Memberships"|gettext} - {$group->name}</h2>
    </div>

    {form id="myform" action="update_memberships"}
        {control type="hidden" name="id" value=$group->id}
        {$table_filled = true}
        <table id="groups-manage" class="table">
            <thead>
                <tr>
                    <th data-class="expand">{'Username'|gettext}</th>
                    <th data-hide="phone" data-name="First">{'First Name'|gettext}</th>
                    <th data-hide="phone" data-name="Last">{'Last Name'|gettext}</th>
                    <th data-name="Member">{'Is Member'|gettext}</th>
                    <th data-hide="phone" data-name="Admin">{'Is Admin'|gettext}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$page->records item=grp_user name=listings}
                    <tr>
                        <td>{$grp_user->username}</td>
                        <td>{$grp_user->firstname}</td>
                        <td>{$grp_user->lastname}</td>
                        <td>
                            {*{$grp_user->id}*}
                            {control type=checkbox name="memdata[`$grp_user->id`][is_member]" value=1 checked=$grp_user->is_member}
                        </td>
                        <td>
                            {*{$grp_user->id}*}
                            {control type=checkbox name="memdata[`$grp_user->id`][is_admin]" value=1 checked=$grp_user->is_admin}
                        </td>
                    </tr>
                {foreachelse}
                    {$table_filled = false}
                    <td colspan="5"><h4>{'No Data'|gettext}</h4></td>
                {/foreach}
            </tbody>
        </table>
        {control type="buttongroup" submit="Save Memberships"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{if $table_filled}
{script unique="manage-groups" jquery='jquery.dataTables2,dataTables.bootstrap2,dataTables.checkboxes'}
{literal}
    $(document).ready(function() {
        // var responsiveHelper;
        // var breakpointDefinition = {
        //     tablet: 1024,
        //     phone : 480
        // };
        var tableContainer = $('#groups-manage');

        var table = tableContainer.DataTable({
            autoWidth: false,
            columns: [
                null,
                null,
                null,
                { searchable: false, orderable: false },
                { searchable: false, orderable: false },
            ],
            // columnDefs: [
            //    {
            //       targets: [3,4],
            //       data: 0,
            //       checkboxes: true
            //    }
            // ],
        });

        // Handle form submission event
        // $('#myform').on('submit', function(e){
        //     var form = this;
        //
        //     var rows_selected = table.column(3).checkboxes.selected();
        //     // Iterate over all selected checkboxes
        //     $.each(rows_selected, function(index, rowId){
        //         // Create a hidden element
        //         $(form).append(
        //             $('<input>')
        //             .attr('type', 'hidden')
        //             .attr('name', 'memdata['+rowId+'][is_member]')
        //             .val(rowId)
        //         );
        //     });
        //     var rows_selected = table.column(4).checkboxes.selected();
        //     // Iterate over all selected checkboxes
        //     $.each(rows_selected, function(index, rowId){
        //         // Create a hidden element
        //         $(form).append(
        //             $('<input>')
        //             .attr('type', 'hidden')
        //             .attr('name', 'memdata['+rowId+'][is_admin]')
        //             .val(rowId)
        //         );
        //     });
        // });

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
    } );
{/literal}
{/script}
{/if}