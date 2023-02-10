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

{css unique="viewregistrants" corecss="tables"}

{/css}

{css unique="event-show1" link="`$asset_path`css/eventregistration.css"}

{/css}

{css unique="yadcf"}
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
    div.dataTables_paginate ul.pagination {
        display: inline-flex;
    }
    .yadcf-filter-wrapper {
        display: block;
    }
    table.dataTable thead .sorting,
    table.dataTable thead .sorting_asc,
    table.dataTable thead .sorting_desc  {
        background-image: none;
    }
    .text-right {
        text-align: right;
    }
{/literal}
{/css}

<div class="store  store show event-registration">
    <div class="form_header">
        {permissions}
            <div class="module-actions">
                {if $permissions.create}
                    {icon class="add" controller=store action=edit product_type=eventregistration text="Add an event"|gettext}
                {/if}
                {if $permissions.manage}
                     {icon action=manage text="Manage Active Events"|gettext}
                {/if}
            </div>
        {/permissions}
        <h2>{'Event Information'|gettext}</h2>
        <h3>{$event->title}</h3>
        {permissions}
            <div class="item-actions">
                {if $permissions.edit || ($permissions.create && $event->poster == $user->id)}
                    {icon controller="store" action=edit record=$event}
                    {icon controller="store" action=copyProduct class="copy" record=$event text="Copy" title="Copy `$event->title` "}
                {/if}
                {if $permissions.delete || ($permissions.create && $event->poster == $user->id)}
                    {icon controller="store" action=delete record=$event}
                {/if}
            </div>
        {/permissions}

        <div id="eventregform">
            <span class="label">{'Event Date'|gettext}: </span>
            <span class="value">{$event->eventdate|format_date:"%A, %B %e, %Y"}
                {if (!empty($event->eventenddate) && $event->eventdate != $event->eventenddate)} {'to'|gettext} {$event->eventenddate|format_date:"%A, %B %e, %Y"}{/if}
            </span>{br}
            <span class="label">{'Start Time'|gettext}: </span>
            <span class="value">{($event->eventdate+$event->event_starttime)|format_date:"%l:%M %p"}</span>{br}
            <span class="label">{'End Time'|gettext}: </span>
            <span class="value">{($event->eventdate+$event->event_endtime)|format_date:"%l:%M %p"}</span>{br}
            {if !empty($event->location)}
                <span class="label">{'Location:'|gettext} </span>
                <span class="value">{$event->location}</span>{br}
            {/if}
            <span class="label">{'Price per person:'|gettext} </span>
            <span class="value">{if $event->base_price}{$event->base_price|currency}{else}{'No Cost'|gettext}{/if}</span>{br}
            <span class="label">{'Seats Registered:'|gettext} </span>
            <span class="value">{$count}{if $event->quantity} {'of'|gettext} {$event->quantity}{/if}</span>{br}
            <span class="label">{'Registration Closes:'|gettext} </span>
            <span class="value">{$event->signup_cutoff|format_date:"%A, %B %e, %Y"}</span>
        </div>
    </div>
    {br}
    {form action="emailRegistrants" id="email-registrants"}
        <div class="events">
            {permissions}
                <div class="module-actions">
                    {if $registrants|count < $event->quantity || $event->quantity == 0}
                        {if $permissions.create}
                            {icon class="add" action=edit_registrant event_id=$event->id text="Manually Add a Registrant"|gettext}
                        {/if}
                    {/if}
                    {icon class=downloadfile controller=eventregistration action=export id=$event->id text='Export this Event Roster'|gettext}
                </div>
            {/permissions}
            {$controls = $event->getAllControls()}
            <div style="overflow: auto; overflow-y: hidden;">
            {$table_filled = true}
            <table id="view-registrants" class="table">
                <thead>
                    <tr>
                        {*<th>{'Registrant Name'|gettext}</th>*}
                        {*<th>{'Registrant Email'|gettext}</th>*}
                        {*<th>{'Registrant Phone'|gettext}</th>*}
                        {foreach from=$controls item=control key=name name=control}
                            <th{if $control@first} data-class="expand"{elseif $control@iteration < 4} data-hide="phone"{elseif $control@iteration > 7} data-hide="always"{else} data-hide="phone,tablet"{/if}>
                                <span>{$control->caption}</span>
                            </th>
                        {foreachelse}
                            <th>{'Name'|gettext}</th>
                            <th>{'Quantity'|gettext}</th>
                        {/foreach}
                        <th>{'Paid?'|gettext}</th>
                        <th>{'Actions'|gettext}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $registrants|count > 0}
                        {$is_email = false}
                        {foreach from=$registrants item=registrant key=id}
                            {*{get_user user=$user assign=registrant}*}
                            <tr>
                                {*<td>{$registrant->name}</td>*}
                                {*<td>*}
                                    {*{if !empty($registrant->email)}{control type="hidden" name="email_addresses[]" value={$registrant->email}}{/if}*}
                                    {*<a href="mailto:{$registrant->email}">{$registrant->email}</a>*}
                                {*</td>*}
                                {*<td>{$registrant->phone} </td>*}
                                {foreach $controls as $control}
                                    {$ctlname = $control->name}
                                    <td>
                                       {if $ctlname == 'email'}
                                           {$is_email = true}
                                           {control type="hidden" name="email_addresses[]" value={$registrant->$ctlname}}
                                           <a href="mailto:{$registrant->$ctlname}" title="{'Send them an email'|gettext}">{$registrant->$ctlname}</a>
                                       {else}
                                           {$registrant->$ctlname}
                                       {/if}
                                    </td>
                                {foreachelse}
                                    <th>{$registrant->user}</th>
                                    <th>{$registrant->qty}</th>
                                {/foreach}
                                <td>
                                    {if $registrant->order_id}
                                        <a href="{link controller="order" action="show" id=$registrant->order_id}" title="{'Edit this order'|gettext}">{$registrant->payment}</a>
                                    {else}
                                        {$registrant->payment}
                                    {/if}
                                </td>
                                <td>
                                    {permissions}
                                        <div class="item-actions">
                                            {if $permissions.edit}
                                                {icon class=edit action=edit_registrant event_id=$event->id id=$registrant->id title='Edit this Registrant'|gettext}
                                            {/if}
                                            {if $permissions.delete}
                                                 {icon class="delete" action=delete_registrant event_id=$event->id id=$registrant->id title='Delete this Registrant'|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this registrant from the roster?"|gettext)|cat:"');"}
                                            {/if}
                                        </div>
                                    {/permissions}
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        {$table_filled = false}
                        <tr class="{cycle values="odd,even"}">
                            <td colspan="4"><h4>{'There is currently no one registered'|gettext}</h4></td>
                        </tr>
                    {/if}
                </tbody>
            </table>
            </div>
        </div>
        {if $registrants|count > 0 && $is_email}
            {group label='Send an Email to All Registrants'|gettext}
                {control type="text" name="email_subject" label="Subject"|gettext}
                {control type="editor" name="email_message" label="Message"|gettext}
                {control type="uploader" name="attach" label="Attachment"|gettext description='Optionally send a file attachment'|gettext}
                {control type="buttongroup" submit="Send Email"|gettext}
            {/group}
        {/if}
    {/form}
</div>

{if $table_filled}
{script unique="view-registrants" jquery='jquery.dataTables'}
{literal}
    $(document).ready(function() {
        // var responsiveHelper;
        // var breakpointDefinition = {
        //     tablet: 1024,
        //     phone : 480
        // };
        var tableContainer = $('#view-registrants');

        var table = tableContainer.DataTable({
            columnDefs: [
                { searchable: false, targets: [ -2 ] },
                { orderable: false, targets: [ -2 ] },
            ],
            autoWidth: false,
            //scrollX: true,
            // preDrawCallback: function () {
            //     // Initialize the responsive datatables helper once.
            //     if (!responsiveHelper) {
            //         responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
            //     }
            // },
            // rowCallback: function (nRow) {
            //     responsiveHelper.createExpandIcon(nRow);
            // },
            // drawCallback: function (oSettings) {
            //     responsiveHelper.respond();
            // }
        });
        // restore all rows so we get all form input instead of only those displayed
        // $('#email-registrants').on('submit', function (e) {
        //     // Force all the rows back onto the DOM for postback
        //     table.rows().nodes().page.len(-1).draw(false);  // This is needed
        //     if ($(this).valid()) {
        //         return true;
        //     }
        //     e.preventDefault();
        // });

        // Handle form submission event
        $('#manage-groups').on('submit', function(e){
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
{/if}