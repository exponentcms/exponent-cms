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

{css unique="yadcf" lesscss="`$smarty.const.JQUERY_RELATIVE`addons/less/select2-bootstrap.less"}
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
    .yadcf-filter-range-date-seperator {
        display: block;
    }
    div.dataTables_paginate ul.pagination {
        display: inline-flex;
    }
    input#yadcf-filter--orders-1.yadcf-filter,
    input#yadcf-filter--orders-2.yadcf-filter {
        width: 50px;
    }
    .yadcf-filter-wrapper {
        display: block;
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
    .yadcf-filter-reset-button {
        padding: 2px 5px;
        height: 24px;
        margin-top: 6px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 4px;
        color: #333333;
        background-color: #ffffff;
        border: 1px solid #cccccc;
        display: inline-block;
        font-weight: normal;
        text-align: center;
        vertical-align: middle;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        white-space: nowrap;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .yadcf-filter-wrapper {
        display: flex;
        white-space: normal;
        margin-top: 7px;
    }
    .yadcf-filter-wrapper-inner {
        border: none;
    }
    .yadcf-filter-range-date-seperator,
    .yadcf-filter-range-number-seperator {
        margin-left: 2px;
        margin-right: 2px;
    }
    .yadcf-filter,
    .yadcf-filter-range-date,
    .yadcf-filter-range {
        line-height: 1;
        min-height: 28px;
        font: inherit;
        font-weight: normal;
        font-size: 14px;
        color: black;
        background-color: #FFF;
        border: 1px solid #CCC;
        border-radius: 4px;
        padding-left: 5px;
    }
{/literal}
{/css}

<div class="modules order showall">
	<h1>{$moduletitle|default:"Store Order Administration"|gettext}</h1>
    {icon class=add action=create_new_order text='Create a new order'|gettext}
	{if $closed_count > -1}
        {br}{$closed_count} {'orders have been closed.'|gettext} <a href="{link action=showall showclosed=1}">{'Expose Closed Orders'|gettext}</a>
    {else}
        {br}<a href="{link action=showall showclosed=0}">{'Hide closed orders'|gettext}</a>
    {/if}
    <table class="responsive{if $smarty.const.ECOM_LARGE_DB} exp-skin-table{/if}" id="orders">
        <thead>
            <tr>
                <th data-class="expand">{'Customer'|gettext}</th>
                <th>{'Inv#'|gettext}</th>
                <th>{'Total'|gettext}</th>
                <th data-hide="phone">{'Payment'|gettext}</th>
                <th data-hide="phone">{'Purchased'|gettext}</th>
                <th data-hide="phone,tablet">{'Type'|gettext}</th>
                <th data-hide="phone">{'Status'|gettext}</th>
                <th data-hide="phone,tablet">{'Ref'|gettext}</th>
            </tr>
        </thead>
        {if !$smarty.const.ECOM_LARGE_DB}
        <tbody>
            {foreach from=$page->records item=listing name=listings}
                <tr class="{cycle values="odd,even"}">
                    <td>
                        <a href="{link action=show id=$listing->id}">{$listing->lastname}{if !empty($listing->lastname) || !empty($listing->firstname)}, {else}{$listing->user_id|username:'system'}{/if}{$listing->firstname}</a>
                        {$listing->user_id|username:'system'}
                    </td>
                    <td>
                        <a href="{link action=show id=$listing->id}">{$listing->invoice_id}</a>
                    </td>
                    <td style="text-align:right;"><span class="badge {if $listing->paid|lower == 'complete' || $listing->paid|lower == 'paid'}alert-success{/if}" title="{if $listing->paid|lower == 'complete' ||  $listing->paid|lower == 'paid'}{'Paid'|gettext}{else}{'Payment Due'|gettext}{/if}">{$listing->grand_total|currency}</span></td>
                    <td>{billingcalculator::getCalcTitle($listing->method)}</td>
                    <td data-order="{$listing->purchased}" data-search="{$listing->purchased|format_date:"%m/%d/%Y %I:%M%p"}">{$listing->purchased|format_date:"%m/%d/%Y %I:%M%p"}</td>
                    <td>{$listing->order_type}</td>
                    <td><span class="badge alert-{if $listing->order_status_id == $new_order}success{else}default{/if}">{$listing->status}</span></td>
                    <td>{if $listing->orig_referrer !=''}<a href="{$listing->orig_referrer}" target="_blank" title="{$listing->orig_referrer}">{icon img="clean.png" color=green}</a>{/if}</td>
                </tr>
            {/foreach}
        </tbody>
        {/if}
    </table>
</div>

{script unique="manage-orders" jquery='moment,bootstrap-datetimepicker,select2,jquery.dataTables,jquery.dataTables.yadcf'}
{literal}
    $(document).ready(function() {
//        var responsiveHelper;
//        var breakpointDefinition = {
//            tablet: 1024,
//            phone : 480
//        };
        var tableContainer = $('#orders');
        var table = tableContainer.DataTable({
    {/literal}
    {if $smarty.const.ECOM_LARGE_DB}
    {literal}
            processing: true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-fw"></i> <span>{/literal}{'Loading Records'|gettext}...{literal}</span> '
            },
            serverSide: true,
            ajax: eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=order&action=getOrdersByJSON&json=1{/literal}{if $closed_count == -1}&showclosed=1{/if}{literal}",
    {/literal}
    {/if}
    {literal}
            stateSave: true,
            columns: [
                { data: 'name', type: 'html' },
                { data: 'invoice_id', type: 'html-num-fmt', "orderSequence": [ "desc", "asc" ] },
                { data: 'grand_total', type: 'html-num-fmt', "orderSequence": [ "desc", "asc" ], className: "text-right" },
                { data: 'calc' },
                { data: 'purchased', "orderSequence": [ "desc", "asc" ] },
                { data: 'order_type' },
                { data: 'status', type: 'html' },
                { data: 'orig_referrer', searchable: false, orderable: false }
            ],
            order: [[4, 'desc']],
            //scrollX: true,
            autoWidth: false,
            pageLength: {/literal}{ecomconfig var='pagination_default' default=10}{literal},
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

        //  Strip HTML using DOM methods
        (function () {
            var _div = document.createElement('div');

            jQuery.fn.dataTable.ext.type.search.html = function ( data ) {
                _div.innerHTML = data;

                return _div.textContent ?
                    _div.textContent.replace(/\n/g," ") :
                    _div.innerText.replace(/\n/g," ");
            };
        })();

        var datepickerDefaults = {
            showTodayButton: true,
            showClear: true
        };

        yadcf.init(table, [{
            column_number: 0,
            column_data_type: "html",
            html_data_type: "text",
            // filter_type: "multi_select",
            filter_type: "text",
            filter_default_label: "",
            filter_delay: 500,
            // select_type: 'select2'
            style_class: 'form-control',
        }, {
            column_number: 1,
            column_data_type: "html",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            filter_delay: 500,
            style_class: 'form-control',
            select_type_options: {
                width: '70px'
            }
        }, {
            column_number: 2,
            column_data_type: "html",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            filter_delay: 500,
            style_class: 'form-control',
            select_type_options: {
                width: '70px'
            }
        }, {
            column_number: 3,
            column_data_type: "text",
            filter_type: "select",
            filter_default_label: "Select Payment",
            select_type: 'select2',
            select_type_options: {
            //     width: '50px',
                minimumResultsForSearch: -1 // remove search box
            },
            style_class: 'form-control',
        }, {
            column_number: 4,
            column_data_type: "text",
            // html5_data: "data-search",
            datepicker_type: 'bootstrap-datetimepicker',
            filter_plugin_options: datepickerDefaults,
            date_format: 'MM/DD/YYYY hh:mmA',
            filter_type: "range_date",
            filter_default_label: ["From","To"],
            filter_delay: 500,
        }, {
            column_number: 5,
            column_data_type: "text",
            filter_type: "select",
            filter_default_label: "Select Type",
            select_type: 'select2',
            select_type_options: {
            //     width: '50px',
                minimumResultsForSearch: -1 // remove search box
            },
            style_class: 'form-control',
        }, {
            column_number: 6,
            column_data_type: "html",
            html_data_type: "text",
            filter_type: "select",
            filter_default_label: "Select Status",
            select_type: 'select2',
            select_type_options: {
            //     width: '50px',
                minimumResultsForSearch: -1 // remove search box
            },
            style_class: 'form-control',
        }]);

    {/literal}
    {if $smarty.const.ECOM_LARGE_DB}
    {literal}
        setInterval( function () {
            $.ajax({
                headers: { 'X-Transaction': 'Getting Invoice Number'},
                url: EXPONENT.PATH_RELATIVE+'index.php?controller=order&action=getInvoiceNumByJSON&ajax_action=1',
                success: function(invnum){
                    var data = table.ajax.json();
                    if (invnum != data.invoicenum) {
                        table.ajax.reload(); // user paging is reset on reload
                    }
                }
            });
        }, 30000 );
    {/literal}
    {/if}
    {literal}
    });
{/literal}
{/script}
