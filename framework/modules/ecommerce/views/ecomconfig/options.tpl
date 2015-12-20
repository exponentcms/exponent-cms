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

{css unique="options"}
    .optiontable {
        border:1px solid red;
        margin : 12px;
    }
    .optiontable thead {
        border-bottom:1px solid red;
        background-color:rgba(237, 199, 185, 1);
    }
    .optiontable tr {
        border-bottom:1px solid red;
    }
    .optiontable td,
    .optiontable th {
        padding : 12px;
    }
{/css}
{permissions}
{if $permissions.manage}
    <div class="module storeadmin options">
        <h1>{$moduletitle|default:"Manage Product Options"|gettext}</h1>
        {permissions}
            <div class="module-actions">
                {if $permissions.create}
                    {icon class=add action=edit_optiongroup_master text="Create new option group"|gettext}
                {/if}
                {*{if $permissions.manage}*}
                    {*{ddrerank items=$optiongroups model="option_master" label="Product Options"|gettext}*}
                {*{/if}*}
            </div>
        {/permissions}
        {foreach from=$optiongroups item=group}
            <table class="optiontable">
                <thead>
                    <tr>
                        <th>
                            <h3>
                                {$group->title} <span class="badge" title="{'Number times used'|gettext}">{$group->timesImplemented}</span>
                            </h3>
                        </th>
                        <th>
                            {icon class=edit action=edit_optiongroup_master record=$group}
                            {icon class=delete action=delete_optiongroup_master record=$group onclick="return confirm('This option group is being used by `$group->timesImplemented` products. Deleting this option group will also delete all of the options related to it. Are you sure you want to delete this option group?');"}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {*<tr>*}
                        {*<td colspan=2>*}
                            {*{icon class=add action=edit_option_master optiongroup_master_id=$group->id text='Add an option to'|gettext|cat:' '|cat:$group->title}*}
                            {foreach name=options from=$group->option_master item=optname}
                                <tr>
                                    <td>
                                        {$optname->title} <span class="badge" title="{'Number times used'|gettext}">{$optname->timesImplemented}</span>
                                    </td>
                                    <td>
                                        {*{icon class=edit action=edit_option_master record=$optname}*}
                                        {*{if $optname->timesImplemented > 0}*}
                                            {*{icon class=delete action=delete_option_master record=$optname onclick="alert('This option is being used by `$optname->timesImplemented` products. You may not delete this option until they are removed from the products.'); return false;"}*}
                                        {*{else}*}
                                            {*{icon class=delete action=delete_option_master record=$optname onclick="return true;"}*}
                                        {*{/if}*}
                                    </td>
                                </tr>
                            {foreachelse}
                                {br}{'This option group doesn\'t have any options yet.'|gettext}
                            {/foreach}
                        {*</td>*}
                    {*</tr>*}
                </tbody>
            </table>
        {foreachelse}
            <h2>{'There are no product options setup yet.'|gettext}</h2>
        {/foreach}
    </div>
{/if}
{/permissions}
