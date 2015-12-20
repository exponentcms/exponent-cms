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
    input#yadcf-filter--prods-3.yadcf-filter {
        width: 50px;
    }
    input#yadcf-filter--prods-2.yadcf-filter {
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
{css unique="managestore" corecss="tables"}

{/css}
{/if}

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
        {if $smarty.const.ECOM_LARGE_DB}
		{pagelinks paginate=$page top=1}
        {/if}
        <table id="prods" style="width:95%;"{if $smarty.const.ECOM_LARGE_DB} class="exp-skin-table"{/if}>
            <thead>
                <tr>
                    {*<th></th>*}
                    {if $smarty.const.ECOM_LARGE_DB}
                    {$page->header_columns}
                    {else}
                    <th>{'Type'|gettext}</th>
                    <th>{'Product Name'|gettext}</th>
                    <th>{'Model #'|gettext}</th>
                    <th>{'Price'|gettext}</th>
                    {/if}
                    <th>{'Action'|gettext}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$page->records item=listing name=listings}
                    <tr class="{cycle values="odd,even"}">
                        {*<td>{img file_id=$listing->expFile.images[0]->id square=60}</td>*}
                        <td>{$listing->product_type|ucwords}</td>
                        <td>
                            {if $listing->product_type == "eventregistration"}
                                <a href={link controller=eventregistration action=show title=$listing->sef_url}>{img file_id=$listing->expFile.mainimage[0]->id square=true h=50}{br}{$listing->title}</a>
                            {elseif $listing->product_type == "donation"}
                                <a href={link controller=donation action=show title=$listing->sef_url}>{img file_id=$listing->expFile.mainimage[0]->id square=true h=50}{br}{$listing->title}</a>
                            {elseif $listing->product_type == "giftcard"}
                                <a href={link controller=store action=show title=$listing->sef_url}>{img file_id=$listing->expFile.mainimage[0]->id square=true h=50}{br}{$listing->title}</a>
                            {else}
                                <a href={link controller=store action=show title=$listing->sef_url}>{img file_id=$listing->expFile.mainimage[0]->id square=true h=50}{br}{$listing->title}</a>
                                {*{img file_id=$listing->expFile.mainimage[0]->id square=true h=50}*}
                            {/if}
                        </td>
                        <td>{$listing->model|default:"N/A"}</td>
                        {*<td>*}
                            {*{if $listing->product_type == "product"}*}
                                {*<a href={link controller=store action=show title=$listing->sef_url}>{$listing->title}</a>*}
                            {*{else}*}
                                {*{$listing->title}*}
                            {*{/if}*}
                        {*</td>*}
                        <td>
                            {*{if $listing->product_type == "product"}*}
                                {$listing->base_price|currency}
                            {*{/if}*}
                        </td>
                        <td>
                            {permissions}
                                <div class="item-actions">
                                    {if $permissions.edit || ($permissions.create && $listing->poster == $user->id)}
                                        {icon action=edit record=$listing title="Edit `$listing->title`"}
                                    {/if}
                                    {if $permissions.delete || ($permissions.create && $listing->poster == $user->id)}
                                        {icon action=delete record=$listing title="Delete `$listing->title`"}
                                    {/if}
                                    {if $permissions.edit && ($listing->product_type == "product" || $listing->product_type == "eventregistration")}
                                        {icon class=copy action=copyProduct title="Copy `$listing->title` " record=$listing}
                                    {/if}
                                </div>
                            {/permissions}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        {if $smarty.const.ECOM_LARGE_DB}
		{pagelinks paginate=$page bottom=1}
        {/if}
    </div>
</div>

{if !$smarty.const.ECOM_LARGE_DB}
{script unique="manage-products" jquery='jqueryui,select2,jquery.dataTables,dataTables.tableTools,dataTables.jqueryui,jquery.dataTables.yadcf'}
{literal}
    $(document).ready(function() {
        var tableContainer = $('#prods');

        var table = tableContainer.DataTable({
            pagingType: "full_numbers",
//            dom: 'T<"top"lfip>rt<"bottom"ip<"clear">',  // pagination location
            dom: 'T<"clear">lfrtip',
            tableTools: {
                sSwfPath: EXPONENT.JQUERY_RELATIVE+"addons/swf/copy_csv_xls_pdf.swf"
            },
            //jQueryUI: true,
            //renderer: {
            //    "header": "bootstrap"
            //},
            scrollX: true,
            stateSave: true,
            columns: [
                { type: 'text' },
                { type: 'html' },
                { type: 'text' },
                { type: 'text' },
                { searchable: false, orderable: false },
            ],
            //order: [1, 'asc'],
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
            //column_data_type: "text",
            //html_data_type: "text",
            filter_type: "multi_select",
            //filter_default_label: "",
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
            column_data_type: "text",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            select_type_options: {
                width: '70px'
            }
        }, {
            column_number: 3,
            column_data_type: "text",
            html_data_type: "text",
            filter_type: "text",
            filter_default_label: "",
            select_type_options: {
                width: '70px'
            }
        }]);
    } );
{/literal}
{/script}
{/if}