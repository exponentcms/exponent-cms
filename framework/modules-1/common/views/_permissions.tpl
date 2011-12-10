{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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
 
{css unique="permissions" corecss="tables"}
{literal}
.exp-skin-table thead th {
    white-space:nowrap;
    border-right:1px solid #D4CBBA;
}
{/literal}
{/css}

<form method="post">
    <input type="hidden" name="module" value="{$__loc->mod}" />
    <input type="hidden" name="src" value="{$__loc->src}" />
    <input type="hidden" name="int" value="{$__loc->int}" />
    {if $user_form == 1}<input type="hidden" name="action" value="saveuserperms" />
    {else}<input type="hidden" name="action" value="savegroupperms" />
    {/if}
    <input type="hidden" name="_common" value="1" />
    {$page->links}
    <div style="overflow : auto; overflow-y : hidden;">
        <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
            <thead>
                <tr>
                    {$page->header_columns}
                </tr>
            </thead>
            <tbody>
                {foreach from=$page->records item=user key=ukey name=user}
                    <tr class="{cycle values="even,odd"}">
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
    {$page->links}
    {control type="buttongroup" submit="Save Permissions"|gettext cancel="Cancel"|gettext}
</form>

{script unique="permission-checking" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
    var manage = Y.all('input.manage');
    var admin = Y.all('input.administrate');
    var create = Y.all('input.create');
    var post = Y.all('input.post');
    var add_module = Y.all('input.add_module');

    var checkSubs = function(row) {
        row.each(function(n,k){
            if (!n.hasClass('manage') && !n.hasClass('administrate')) {
                n.insertBefore('<input type="hidden" name="'+n.get("name")+'" value="1">',n);
                n.setAttrs({'checked':1,'disabled':1});
            };
        });
    };

    var unCheckSubs = function(row) {
        row.each(function(n,k){
            if (!n.hasClass('manage') && !n.hasClass('administrate')) {
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
    admin.on('click',function(e){
        toggleChecks(e.target);
    });
    admin.each(function(n){
        toggleChecks(n,1);
    });
    create.on('click',function(e){
        var row = e.target.ancestor('tr').all('input[type=checkbox]');
        if(e.target.get('checked')&&!e.target.get('disabled')){
            row.each(function(n,k){
                if (n.hasClass('edit')) {
                    n.insertBefore('<input type="hidden" name="'+n.get("name")+'" value="1">',n);
                    n.setAttrs({'checked':1,'disabled':1});
                };
            });
        } else {
            row.each(function(n,k){
                if (n.hasClass('edit')) {
                    n.get('previousSibling').remove();
                    n.setAttrs({'checked':0,'disabled':0});
                };
            });
        }
    });
    create.each(function(target){
        var row = target.ancestor('tr').all('input[type=checkbox]');
        if(target.get('checked')&&!target.get('disabled')){
            row.each(function(n,k){
                if (n.hasClass('edit')) {
                    n.insertBefore('<input type="hidden" name="'+n.get("name")+'" value="1">',n);
                    n.setAttrs({'checked':1,'disabled':1});
                };
            });
        }
    });
    post.on('click',function(e){
        var row = e.target.ancestor('tr').all('input[type=checkbox]');
        if(e.target.get('checked')&&!e.target.get('disabled')){
            row.each(function(n,k){
                if (n.hasClass('edit')) {
                    n.insertBefore('<input type="hidden" name="'+n.get("name")+'" value="1">',n);
                    n.setAttrs({'checked':1,'disabled':1});
                };
            });
        } else {
            row.each(function(n,k){
                if (n.hasClass('edit')) {
                    n.get('previousSibling').remove();
                    n.setAttrs({'checked':0,'disabled':0});
                };
            });
        }
    });
    post.each(function(target){
        var row = target.ancestor('tr').all('input[type=checkbox]');
        if(target.get('checked')&&!target.get('disabled')){
            row.each(function(n,k){
                if (n.hasClass('edit')) {
                    n.insertBefore('<input type="hidden" name="'+n.get("name")+'" value="1">',n);
                    n.setAttrs({'checked':1,'disabled':1});
                };
            });
        }
    });
    add_module.on('click',function(e){
        var row = e.target.ancestor('tr').all('input[type=checkbox]');
        if(e.target.get('checked')&&!e.target.get('disabled')){
            row.each(function(n,k){
                if (n.hasClass('edit_module')) {
                    n.insertBefore('<input type="hidden" name="'+n.get("name")+'" value="1">',n);
                    n.setAttrs({'checked':1,'disabled':1});
                };
            });
        } else {
            row.each(function(n,k){
                if (n.hasClass('edit_module')) {
                    n.get('previousSibling').remove();
                    n.setAttrs({'checked':0,'disabled':0});
                };
            });
        }
    });
    add_module.each(function(target){
        var row = target.ancestor('tr').all('input[type=checkbox]');
        if(target.get('checked')&&!target.get('disabled')){
            row.each(function(n,k){
                if (n.hasClass('edit_module')) {
                    n.insertBefore('<input type="hidden" name="'+n.get("name")+'" value="1">',n);
                    n.setAttrs({'checked':1,'disabled':1});
                };
            });
        }
    });
});
{/literal}
{/script}
