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
 
{*{css unique="permissions" corecss="tables"}*}
{*{literal}*}
{*.exp-skin-table thead th {*}
    {*white-space:nowrap;*}
    {*border-right:1px solid #D4CBBA;*}
{*}*}
{*{/literal}*}
{*{/css}*}
{css unique="manage-perms" corecss="datatables-tools"}

{/css}

{*<form method="post">*}
    {*<input type="hidden" name="module" value="{$page->controller}" />*}
    {*<input type="hidden" name="action" value="{if $user_form == 1}userperms_save{else}groupperms_save{/if}" />*}
    {*<input type="hidden" name="mod" value="{$loc->mod}" />*}
    {*<input type="hidden" name="src" value="{$loc->src}" />*}
    {*<input type="hidden" name="int" value="{$loc->int}" />*}
{if $user_form == 1}{$action = 'userperms_save'}{else}{$action = 'groupperms_save'}{/if}
{form action=$action module=$page->controller}
    {control type="hidden" name="mod" value=$loc->mod}
    {control type="hidden" name="src" value=$loc->src}
    {control type="hidden" name="int" value=$loc->int}
    {$page->links}
    <div style="overflow : auto; overflow-y : hidden;">
        <table id="permissions" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    {*{$page->header_columns}*}
                    {foreach  from=$page->columns item=column key=name name=column}
                        <th{if ($is_group && $column@first) || (!$is_group && $column@iteration < 4)} class="sortme"{else} class="nosort"{/if}{if $column@first} data-class="expand"{elseif !$is_group && ($column@iteration == 2 || $column@iteration == 3)} data-hide="phone,tablet"{elseif ($is_group && ($column@iteration > 4) || !$is_group && ($column@iteration > 6))} data-hide="phone,tablet"{elseif ($is_group && ($column@iteration > 2) || !$is_group && ($column@iteration > 4))} data-hide="phone"{/if}>{$name}</th>
                    {/foreach}
                </tr>
            </thead>
            <tbody>
                {foreach from=$page->records item=user key=ukey name=user}
                    {*<input type="hidden" name="users[]" value="{$user->id}" />*}
                    {control type="hidden" name="users[]" value=$user->id}
                    <tr>
                        {if !$is_group}
                            <td>
                                {$user->username}
                            </td>
                            <td>
                                {$user->firstname}
                            </td>
                            <td>
                                {$user->lastname}
                            </td>
                        {else}
                            <td>
                                {$user->name}
                            </td>
                        {/if}
                        {foreach from=$perms item=perm key=pkey name=perms}
                            <td class="checks">
                                <input class="{$pkey}" type="checkbox"{if $user->$pkey==1||$user->$pkey==2} checked{/if} name="permdata[{$user->id}][{$pkey}]" value="1"{if $user->$pkey==2} disabled=1{/if} id="permdata[{$user->id}][{$pkey}]">
                            </td>
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {$page->links}
    {control type="buttongroup" submit="Save Permissions"|gettext cancel="Cancel"|gettext}
{*</form>*}
{/form}

{script unique="permission-checking" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', "event", "node-event-delegate", function(Y) {
    var checkSubs = function(row) {
        row.each(function(n,k){
            if (!n.hasClass('manage')) {
                n.insertBefore('<input type="hidden" name="'+n.get("name")+'" value="1">',n);
                n.setAttrs({'checked':1,'disabled':1});
            };
        });
    };
    var unCheckSubs = function(row) {
        row.each(function(n,k){
            if (!n.hasClass('manage')) {
                n.get('previousSibling').remove();
                n.setAttrs({'checked':0,'disabled':0});
            };
        });
    };
    var toggleChecks = function(target,start) {
        var row = target.ancestor('tr').all('input[type=checkbox]');
        var row1 = target.ancestor('tr').next('tr.row-detail');
        if (row1 != null) checks1 = row1.all('input[type=checkbox]');
        if(target.get('checked')&&!target.get('disabled')){
            checkSubs(row);
            if (checks1 != null) checkSubs(checks1);;
        } else {
            if (!start) {
                unCheckSubs(row);
                if (checks1 != null) unCheckSubs(checks1);;
            }
        }
    };
    Y.one('#permissions').delegate('click',function(e){
        toggleChecks(e.target);
    }, 'input.manage');
    Y.one('#permissions').delegate(function(n){
        toggleChecks(n,1);
    }, 'input.manage');

    });
{/literal}
{/script}

{script unique="permissions" jquery='jquery.dataTables,dataTables.tableTools,dataTables.bootstrap,datatables.responsive'}
{literal}
    $(document).ready(function() {
        var responsiveHelper;
        var breakpointDefinition = {
            tablet: 1024,
            phone : 480
        };
        var tableContainer = $('#permissions');

        var table = tableContainer.DataTable({
            columnDefs: [
//                { searchable: true, targets: [ {/literal}{if !$is_group}0, 1, 2{else}0{/if}{literal} ] },
//                { sortable: true, targets: [ {/literal}{if !$is_group}0, 1, 2{else}0{/if}{literal} ] },
//                { searchable: false, targets: [ '_all' ] },
//                { sortable: false, targets: [ '_all' ] },
                {targets: [ "sortme"], sortable: true },
                {targets: [ 'nosort' ], sortable: false }
            ],
            autoWidth: false,
            preDrawCallback: function () {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper) {
                    responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
                }
            },
            rowCallback: function (nRow) {
                responsiveHelper.createExpandIcon(nRow);
            },
            drawCallback: function (oSettings) {
                responsiveHelper.respond();
            }
        });
        var tt = new $.fn.dataTable.TableTools( table, { sSwfPath: EXPONENT.JQUERY_RELATIVE+"addons/swf/copy_csv_xls_pdf.swf" } );
        $( tt.fnContainer() ).insertBefore('div.dataTables_wrapper');
    } );
{/literal}
{/script}
