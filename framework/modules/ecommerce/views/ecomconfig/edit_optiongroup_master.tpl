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
    <div class="module storeadmin edit_optiongroup_master">
        <h1>{$moduletitle|default:"Edit Product Option Group"|gettext}</h1>
        {if $record->timesImplemented > 0}
            <blockquote>
                {'This option group is being used by'|gettext} {$record->timesImplemented} {'products on your site.  Changing the name will change it for all the products currently using it.'|gettext}
            </blockquote>
        {/if}

        {form action=update_optiongroup_master}
            {control type="hidden" name=id value=$record->id}
            {control type="text" name="title" label="Name"|gettext value=$record->title focus=1}
            {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
        {/form}

        <h3>{'Options'|gettext}</h3>
        <div class="module-actions">
            {if $permissions.create}
                {icon class=add action=edit_option_master optiongroup_master_id=$record->id text='Add an option to'|gettext|cat:' '|cat:$record->title}
            {/if}
            {if $permissions.manage}
                {ddrerank items=$record->option_master only="optiongroup_master_id=`$record->id`" model=option_master label=$record->title|cat:' '|cat:'Options'|gettext}
            {/if}
        </div>
        <table class="optiontable">
            <tbody>
                {foreach name=options from=$record->option_master item=optname}
                    <tr>
                        <td>
                            {$optname->title} <span class="badge" title="{'Number times used'|gettext}">{$optname->timesImplemented}</span>
                        </td>
                        <td>
                            {icon class=edit action=edit_option_master record=$optname}
                            {if $optname->timesImplemented > 0}
                                {icon class=delete action=delete_option_master record=$optname onclick="alert('This option is being used by `$optname->timesImplemented` products. You may not delete this option until they are removed from the products.'); return false;"}
                            {else}
                                {icon class=delete action=delete_option_master record=$optname onclick="return true;"}
                            {/if}
                        </td>
                    </tr>
                {foreachelse}
                    {br}{'This option group doesn\'t have any options yet.'|gettext}
                {/foreach}
            </tbody>
        </table>

    </div>
{/if}
{/permissions}
