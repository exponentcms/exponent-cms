{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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
    input#yadcf-filter--prods-0.yadcf-filter {
        width: 80px;
    }
    input#yadcf-filter--prods-1.yadcf-filter {
        width: 240px;
    }
    input#yadcf-filter--prods-2.yadcf-filter {
        width: 150px;
    }
    input#yadcf-filter--prods-3.yadcf-filter {
        width: 50px;
    }
    input#yadcf-filter--prods-4.yadcf-filter {
        width: 80px;
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
    .yadcf-filter-reset-button {
        padding: 2px 5px;
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
{/literal}
{/css}

<div class="module store showall-uncategorized">
    <h1>{'Manage Products'|gettext}</h1>
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit title="Create a new product"|gettext text="Add a product"|gettext}
			{/if}
            {if $permissions.manage}
                {icon controller=storeCategory action=manage text="Manage Categories"|gettext}
                {icon class=configure controller=store action=config text="Configure Store"|gettext}{br}
                {icon class=report controller=store action=nonUnicodeProducts text="Show Non-Unicode Products"|gettext}
                {icon class=import controller=store action=uploadModelAliases text="Upload Model Aliases"|gettext}
            {/if}
		</div>
    {/permissions}
    <div id="products">
        <table id="prods" style="width:95%;">
            <thead>
                <tr>
                    <th>{'Type'|gettext}</th>
                    <th>{'Product Name'|gettext}</th>
                    <th>{'Model #'|gettext}</th>
                    <th>{'Children'|gettext}</th>
                    <th>{'Price'|gettext}</th>
                    <th>{'Action'|gettext}</th>
                </tr>
            </thead>
            {if !$smarty.const.ECOM_LARGE_DB}
            <tbody>
                {foreach from=$page->records item=listing name=listings}
                    <tr class="{cycle values="odd,even"}">
                        {*<td>{img file_id=$listing->expFile.images[0]->id square=60}</td>*}
                        <td>{$listing->product_type|ucwords}</td>
                        <td>
                            {if $listing->product_type == "eventregistration"}
                                <a href={link controller=eventregistration action=show title=$listing->sef_url}>{img file_id=$listing->fileid square=true h=50}{br}{$listing->title}</a>
                            {elseif $listing->product_type == "donation"}
                                <a href={link controller=donation action=show title=$listing->sef_url}>{img file_id=$listing->fileid square=true h=50}{br}{$listing->title}</a>
                            {elseif $listing->product_type == "giftcard"}
                                <a href={link controller=store action=show title=$listing->sef_url}>{img file_id=$listing->fileid square=true h=50}{br}{$listing->title}</a>
                            {else}
                                <a href={link controller=store action=show title=$listing->sef_url}>{img file_id=$listing->fileid square=true h=50}{br}{$listing->title}</a>
                            {/if}
                        </td>
                        <td>{$listing->model|default:"N/A"}</td>
                        <td>{$listing->children|regex_replace:'/^0$/':''}</td>
                        {*<td>*}
                            {*{if $listing->product_type == "product"}*}
                                {*<a href={link controller=store action=show title=$listing->sef_url}>{$listing->title}</a>*}
                            {*{else}*}
                                {*{$listing->title}*}
                            {*{/if}*}
                        {*</td>*}
                        <td>
                            {if $listing->product_type == "product"}
                                {$listing->base_price|currency}
                            {/if}
                        </td>
                        <td>
                            {permissions}
                                <div class="item-actions">
                                    {if $permissions.edit || ($permissions.create && $listing->poster == $user->id)}
                                        {icon action=edit record=$listing title="Edit `$listing->title`"}
                                    {/if}
                                    {if $permissions.edit && ($listing->product_type == "product" || $listing->product_type == "eventregistration")}
                                        {icon class=copy action=copyProduct title="Copy `$listing->title` " record=$listing}
                                    {/if}
                                    {if $permissions.delete || ($permissions.create && $listing->poster == $user->id)}
                                        {icon action=delete record=$listing title="Delete `$listing->title`"}
                                    {/if}
                                </div>
                            {/permissions}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
            {/if}
        </table>
    </div>
</div>

{script unique="manage-products" jquery='jquery.dataTables,dataTables.bootstrap,jquery.dataTables.yadcf'}
{literal}
    $(document).ready(function() {
        // var responsiveHelper;
        // var breakpointDefinition = {
        //     tablet: 1024,
        //     phone : 480
        // };
        var tableContainer = $('#prods');

        var table = tableContainer.DataTable({
    {/literal}
    {if $smarty.const.ECOM_LARGE_DB}
    {literal}
            processing: true,
            serverSide: true,
            ajax: eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=store&action=getProductsByJSON&json=1",
    {/literal}
    {/if}
    {literal}
//            jQueryUI: true,
            stateSave: true,
            columns: [
                { data: 'product_type', type: 'text' },
                { data: 'title', type: 'html' },
                { data: 'model', type: 'text' },
                { data: 'children', type: 'text' },
                { data: 'base_price', type: 'num-fmt', className: "text-right" },
                { data: 'id', searchable: false, orderable: false },
            ],
            order: [[5, 'asc']],
            autoWidth: false,
            pageLength: {/literal}{ecomconfig var='pagination_default' default=10}{literal},
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
            column_data_type: "text",
            html_data_type: "text",
            // filter_type: "multi_select",
            filter_type: "text",
            filter_default_label: "",
            // select_type: 'select2',
            style_class: 'form-control',
            select_type_options: {
                width: '30px'
            }
        }, {
            column_number: 1,
            column_data_type: "html",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            style_class: 'form-control',
            select_type_options: {
                width: '70px'
            }
        }, {
            column_number: 2,
            column_data_type: "text",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            style_class: 'form-control',
            select_type_options: {
                width: '30px'
            }
        }, {
            column_number: 3,
            column_data_type: "text",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            style_class: 'form-control',
            select_type_options: {
                width: '30px'
            }
        }, {
            column_number: 4,
            column_data_type: "text",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            style_class: 'form-control',
            select_type_options: {
                width: '30px'
            }
        }]);
    } );
{/literal}
{/script}
