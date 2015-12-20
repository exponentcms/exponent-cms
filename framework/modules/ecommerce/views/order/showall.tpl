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

{if !$smarty.const.ECOM_LARGE_DB}
{css unique="yadcf" corecss="datatables-tools"}
    table.dataTable thead > tr {
        font-size-adjust: 0.4;
    }
    table.dataTable thead > tr > th {
        padding-left: 5px;
        padding-top: 0;
        padding-bottom: 0;
        vertical-align: top;
    }
    .yadcf-filter-range-date-seperator {
        display: block;
    }
    div.dataTables_paginate ul.pagination {
        display: inline-flex;
    }
    input#yadcf-filter--orders-1.yadcf-filter {
        width: 35px;
    }
    input#yadcf-filter--orders-2.yadcf-filter {
        width: 50px;
    }
    .yadcf-filter-wrapper {
        display: block;
    }
    table.dataTable thead .sorting,
    table.dataTable thead .sorting_asc,
    table.dataTable thead .sorting_desc  {
        background-image: none;
    }
{/css}
{else}
{css unique="showallorders" corecss="tables"}

{/css}
{/if}

<div class="modules order showall">
	<h1>{$moduletitle|default:"Store Order Administration"|gettext}</h1>
    {icon class=add action=create_new_order text='Create a new order'|gettext}
	{if $closed_count > -1}
        {br}{$closed_count} {'orders have been closed.'|gettext} <a href="{link action=showall showclosed=1}">{'Expose Closed Orders'|gettext}</a>
    {else}
        {br}<a href="{link action=showall showclosed=0}">{'Hide closed orders'|gettext}</a>{br}
    {/if}
    {if $smarty.const.ECOM_LARGE_DB}
    {pagelinks paginate=$page top=1}
    {/if}
    <table id="orders"{if $smarty.const.ECOM_LARGE_DB} class="exp-skin-table"{/if}>
        <thead>
            <tr>
                <!--th><span>Purchased By</span></th-->
                {if $smarty.const.ECOM_LARGE_DB}
                {$page->header_columns}
                {else}
                <th>{'Customer'|gettext}</th>
                <th>{'Inv #'|gettext}</th>
                <th>{'Total'|gettext}</th>
                <th>{'Payment'|gettext}</th>
                <th>{'Purchased'|gettext}</th>
                <th>{'Type'|gettext}</th>
                <th>{'Status'|gettext}</th>
                <th>{'Ref'|gettext}</th>
                {/if}
            </tr>
        </thead>
        <tbody>
            {foreach from=$page->records item=listing name=listings}
                <tr class="{cycle values="odd,even"}">
                    <td>
                        <a href="{link action=show id=$listing->id}">{$listing->lastname}{if !empty($listing->lastname) || !empty($listing->firstname)}, {else}{$listing->user_id|username:'system'}{/if}{$listing->firstname}</a>
                        {*{$listing->user_id|username:'system'}*}
                    </td>
                    <td>
                        <a href="{link action=show id=$listing->id}">{$listing->invoice_id}</a>
                    </td>
                    <td style="text-align:right;"><span style="padding:3px;border-radius:5px;background-color:{if $listing->paid|lower == 'complete' ||  $listing->paid|lower == 'paid'}darkseagreen{else}lightgray{/if};" title="{if $listing->paid|lower == 'complete' || $listing->paid|lower == 'paid'}{'Paid'|gettext}{else}{'Payment Due'|gettext}{/if}">{$listing->grand_total|currency}</span></td>
                    <td>{billingcalculator::getCalcTitle($listing->method)}</td>
                    <td>{$listing->purchased|format_date:"%m/%d/%Y %I:%M%p"}</td>
                    <td>{$listing->order_type}</td>
                    <td><span style="padding:3px;border-radius:5px;background-color:{if $listing->order_status_id == $new_order}darkseagreen{else}lightgray{/if};">{$listing->status}</span></td>
                    <td>{if $listing->orig_referrer !=''}<a href="{$listing->orig_referrer}" target="_blank" title="{$listing->orig_referrer}">{icon img="clean.png" color=green}</a>{/if}</td>
                </tr>
            {foreachelse}
                <tr class="{cycle values="odd,even"}">
                    <td colspan="4">{message text='No orders have been placed yet'|gettext}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    {if $smarty.const.ECOM_LARGE_DB}
    {pagelinks paginate=$page bottom=1}
    {/if}
</div>

{if !$smarty.const.ECOM_LARGE_DB}
{script unique="manage-orders" jquery='jqueryui,select2,jquery.dataTables,dataTables.tableTools,dataTables.jqueryui,jquery.dataTables.yadcf'}
{literal}
    $(document).ready(function() {
        var tableContainer = $('#orders');

        var table = tableContainer.DataTable({
            pagingType: "full_numbers",
//            dom: 'T<"top"lfip>rt<"bottom"ip<"clear">',  // pagination location
            dom: 'T<"clear">lfrtip',
            tableTools: {
                sSwfPath: EXPONENT.JQUERY_RELATIVE+"addons/swf/copy_csv_xls_pdf.swf"
            },
            jQueryUI: true,
            scrollX: true,
            stateSave: true,
            columns: [
                { type: 'html' },
                { type: 'html' },
                { type: 'html' },
                null,
                null,
                null,
                { type: 'html' },
                { searchable: false, orderable: false },
            ],
            order: [4, 'desc']
        });

        (function () {
            var _div = document.createElement('div');

            jQuery.fn.dataTable.ext.type.search.html = function ( data ) {
                _div.innerHTML = data;

                return _div.textContent ?
                    _div.textContent.replace(/\n/g," ") :
                    _div.innerText.replace(/\n/g," ");
            };
        })();

        yadcf.init(table, [{
            column_number: 0,
            column_data_type: "html",
            html_data_type: "text",
            filter_type: "multi_select",
            filter_default_label: "",
            select_type: 'select2'
        }, {
            column_number: 1,
            column_data_type: "html",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            select_type_options: {
                width: '70px'
            }
        }, {
            column_number: 2,
            column_data_type: "html",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            select_type_options: {
                width: '70px'
            }
        }, {
            column_number: 3,
            column_data_type: "text",
            filter_type: "select",
            filter_default_label: "Select Type",
            select_type: 'select2'
        }, {
            column_number: 4,
            column_data_type: "text",
            filter_type: "range_date",
            filter_default_label: ["From","To"]
        }, {
            column_number: 5,
            column_data_type: "text",
            filter_type: "select",
            filter_default_label: "Select Type",
            select_type: 'select2'
        }, {
            column_number: 6,
            column_data_type: "html",
            html_data_type: "text",
            filter_type: "select",
            filter_default_label: "Select Status",
            select_type: 'select2'
        }]);
    } );
{/literal}
{/script}
{/if}