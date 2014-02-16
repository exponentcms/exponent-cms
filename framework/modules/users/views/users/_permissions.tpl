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
{css unique="manage-perms" link="`$asset_path`css/datatables-tools.css"}

{/css}

<form method="post">
    <input type="hidden" name="module" value="{$page->controller}" />
    <input type="hidden" name="action" value="{if $user_form == 1}userperms_save{else}groupperms_save{/if}" />
    <input type="hidden" name="mod" value="{$loc->mod}" />
    <input type="hidden" name="src" value="{$loc->src}" />
    <input type="hidden" name="int" value="{$loc->int}" />
    {*{$page->links}*}
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
                    <input type="hidden" name="users[]" value="{$user->id}" />
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
</form>

{script unique="permission-checking" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
    var manage = Y.all('input.manage');

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
        if(target.get('checked')&&!target.get('disabled')){
            checkSubs(row);
        } else {
            if (!start) {
                unCheckSubs(row);
            }
        }
    };
    manage.on('click',function(e){
        toggleChecks(e.target);
    });
    manage.each(function(n){
        toggleChecks(n,1);
    });

});
{/literal}
{/script}

{script unique="permissions" jquery='jquery.dataTables,dataTables.tableTools'}
{literal}
    $(document).ready(function() {
        $('#permissions').dataTable({
            sPaginationType: "full_numbers",
//            sDom: 'T<"top"lfip>rt<"bottom"ip<"clear">',  // pagination location
            dom: 'T<"clear">lfrtip',
            aoColumnDefs: [
//                { "bSearchable": true, "aTargets": [ {/literal}{if !$is_group}0, 1, 2{else}0{/if}{literal} ] },
//                { "bSortable": true, "aTargets": [ {/literal}{if !$is_group}0, 1, 2{else}0{/if}{literal} ] },
//                { "bSearchable": false, "aTargets": [ '_all' ] },
//                { "bSortable": false, "aTargets": [ '_all' ] },
                {"aTargets": [ "sortme"], "bSortable": true },
                {"aTargets": [ 'nosort' ], "bSortable": false }
            ],
        });
    } );
{/literal}
{/script}
