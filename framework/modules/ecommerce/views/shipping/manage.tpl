{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="manageshipping" corecss="tables"}

{/css}

<div class="module shipping manage">
    <h1>{'Manage Shipping Options'|gettext}</h1>
    <p>
        {'This page allows you to turn different shipping options (known as shipping calculators) on and off for customers on your webstore.'|gettext}
    </p>
    <table class="exp-skin-table">
        <thead>
            <tr>
                <th>{'Default'|gettext}</th>
                <th>{'Name'|gettext}</th>
                <th>{'Description'|gettext}</th>
                <th>{'on/off'|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$calculators item=calc}
            <tr class="{cycle values="odd,even"}">
                <td>
                    {permissions}
                        {if $permissions.toggle == 1}
                        {if $calc->is_default}
                            <img src={$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}>
                        {else}
                            <a href="{link action=toggle_default id=$calc->id}"><img src={$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}></a>
                        {/if}
                    {/if}
                    {/permissions}
                </td>
                <td>{$calc->title}</td>
                <td>{$calc->body}</td>
                <td>
                    {permissions}
                        {if $permissions.toggle == 1}
                        {if $calc->enabled}
                            <a href="{link action=toggle id=$calc->id}"><img src={$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}></a>
                        {else}
                            <a href="{link action=toggle id=$calc->id}"><img src={$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}></a>
                        {/if}
                        {if $calc->hasConfig() == 1}
                            {icon action=configure img='configure.png' record=$calc title="Configure `$calc->title`"}
                        {/if}
                    {/if}
                    {/permissions}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
