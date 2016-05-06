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
 
{css unique="manage-perms" corecss="datatables-tools"}

{/css}

{if $user_form == 1}{$action = 'userperms_save'}{else}{$action = 'groupperms_save'}{/if}
{form action=$action module=$page->controller}
    {control type="hidden" name="mod" value=$loc->mod}
    {control type="hidden" name="src" value=$loc->src}
    {control type="hidden" name="int" value=$loc->int}
    <div style="overflow : auto; overflow-y : hidden;">
        <table id="permissions" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    {*{$page->header_columns}*}
                    {foreach  from=$page->columns item=column key=name name=column}
                        <th{if ($is_group && $column@first) || (!$is_group && $column@iteration < 4)} class="sortme"{else} class="nosort"{/if}>{$name}</th>
                    {/foreach}
                </tr>
            </thead>
            <tbody>
                {foreach from=$page->records item=user key=ukey name=user}
                    <tr>
                        {if !$is_group}
                            <td>
                                {control type="hidden" name="users[]" value=$user->id}
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
                                {control type="hidden" name="users[]" value=$user->id}
                                {$user->name}
                            </td>
                        {/if}
                        {foreach from=$perms item=perm key=pkey name=perms}
                            <td>
                                <input class="{$pkey}" type="checkbox"{if $user->$pkey==1||$user->$pkey==2} checked{/if} name="permdata[{$user->id}][{$pkey}]" value="1"{if $user->$pkey==2} disabled=1{/if} id="permdata[{$user->id}][{$pkey}]">
                            </td>
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {*{$page->links}*}
    {control type="buttongroup" submit="Save Permissions"|gettext cancel="Cancel"|gettext}
{/form}

{script unique="permission-checking" yui3mods="node"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var manage = Y.all('input.manage');

    var checkSubs = function(row) {
        row.each(function(n, k){
            if (!n.hasClass('manage')) {
                n.insertBefore('<input type="hidden" name="' + n.get("name") + '" value="1">',n);
                n.setAttrs({'checked':1, 'disabled':1});
            };
        });
    };
    var unCheckSubs = function(row) {
        row.each(function(n, k){
            if (!n.hasClass('manage')) {
                n.get('previousSibling').remove();
                n.setAttrs({'checked':0, 'disabled':0});
            };
        });
    };
    var toggleChecks = function(target, start) {
        var row = target.ancestor('tr').all('input[type=checkbox]');
        if(target.get('checked') && !target.get('disabled')){
            checkSubs(row);
        } else {
            if (!start) {
                unCheckSubs(row);
            }
        }
    };
    Y.one('#permissions').delegate('click', function(e){
//        toggleChecks(e.target);
    }, 'input.manage');
    Y.all('#permissions input.manage').each(function(n){
//        toggleChecks(n, 1);
    });
});
{/literal}
{/script}

{script unique="permissions" jquery='jquery.dataTables,dataTables.tableTools'}
{literal}
    $(document).ready(function() {
        var checkSubs = function(row) {
            row.each(function(k, n) {
                if (!$(n).hasClass('manage')) {
                    $('<input type="hidden" name="' + n.name + '" value="1">').insertBefore($(n));
                    $(n).prop({'checked':true, 'disabled':true});
                };
            });
        };
        var unCheckSubs = function(row) {
            row.each(function(k, n) {
                if (!$(n).hasClass('manage')) {
                    $(n).prev().remove();
                    $(n).prop({'checked':false, 'disabled':false});
                };
            });
        };
        var toggleChecks = function(target, start) {
            var row = $(target).closest('tr').find(':checkbox');
            var row1 = $(target).closest('tr').next('tr.row-detail');  // if responsive
            var checks1 = null;
            if (row1.length)
                checks1 = row1.find(':checkbox');
            if(target.checked && !target.disabled) {
                checkSubs(row);
                if (checks1 != null && checks1.length)
                    checkSubs(checks1);;
            } else {
                if (!start) {
                    unCheckSubs(row);
                    if (checks1 != null && checks1.length)
                        unCheckSubs(checks1);;
                }
            }
        };
        $('#permissions').delegate('input.manage', 'click', function(e){
            toggleChecks(e.target);
        });
        $('#permissions input.manage').each(function(k, e){
            toggleChecks(e, 1);
        });

        $('#permissions').DataTable({
            pagingType: "full_numbers",
//            dom: 'T<"top"lfip>rt<"bottom"ip<"clear">',  // pagination location
            dom: 'T<"clear">lfrtip',
            tableTools: {
                sSwfPath: EXPONENT.JQUERY_RELATIVE+"addons/swf/copy_csv_xls_pdf.swf"
            },
            scrollX: true,
            columnDefs: [
//                { searchable: true, targets: [ {/literal}{if !$is_group}0, 1, 2{else}0{/if}{literal} ] },
//                { sortable: true, targets: [ {/literal}{if !$is_group}0, 1, 2{else}0{/if}{literal} ] },
//                { searchable: false, targets: [ '_all' ] },
//                { sortable: false, targets: [ '_all' ] },
                {targets: [ "sortme"], orderable: true },
                {targets: [ 'nosort' ], orderable: false }
            ],
        });

        // restore all rows so we get all form input instead of only those displayed
        $('#manage-groups').on('submit', function (e) {
            // Force all the rows back onto the DOM for postback
            table.rows().nodes().page.len(-1).draw(false);  // This is needed
            if ($(this).valid()) {
                return true;
            }
            e.preventDefault();
        });
    });
{/literal}
{/script}
