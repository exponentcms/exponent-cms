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

{if !$error}
    {if $is_email == 1}
        <style type="text/css" media="screen">
            {$css}
        </style>
    {else}
        {css unique="default-report" corecss="tables,button"}

        {/css}
    {/if}
    <div class="module forms show">
        {permissions}
            <div class="item-actions">
                {if $permissions.edit == 1}
                    {icon class=edit action=enterdata forms_id=$f->id id=$record_id title='Edit this record'|gettext}
                {/if}
                {if $permissions.delete == 1}
                    {icon class=delete action=delete forms_id=$f->id id=$record_id title='Delete this record'|gettext}
                {/if}
            </div>
        {/permissions}
        <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
            <thead>
                <tr>
                    <th colspan="2">
                        <h2>{$title}</h2>
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$fields key=fieldname item=value}
                    <tr class="{cycle values="even,odd"}">
                        <td>{$captions[$fieldname]}</td>
                        <td>{$value}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        {if !empty($referrer)}
            <p>{'Referrer'|gettext}: {$referrer}</p>
        {/if}
        {if !$is_email}
            <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}"
               href="{$backlink}">{'Back'|gettext}</a>
        {/if}
    </div>
{/if}