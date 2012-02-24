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

{css unique="managebilling" corecss="tables"}

{/css}

<div class="module billing manage">
    <h1>{'Manage Payment Options'|gettext}</h1>
    <p>
        {'This page allows you to turn different payment options (known as billing calculators) on and off for customers on your webstore.'|gettext}
    </p>
    
    <table class="exp-skin-table">
        <thead>
        <tr>
            <th>{'Name'|gettext}</th>
            <th>{'Description'|gettext}</th>
            <th>{'Enabled'|gettext}</th>
            <th>{'Configure'|gettext}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$calculators item=calc}
        <tr class="{cycle values="odd,even"}">
            <td>{$calc->title}</td>
            <td>{$calc->body}</td>
            <td>
                {permissions}
					<div class="item-actions">
                    {if $permissions.manage == 1}                        
						{if $calc->enabled}
							<a href="{link action=activate id=$calc->id enabled=0}">{icon img="toggle_on.png"}</a>
						{else}
							<a href="{link action=activate id=$calc->id enabled=1}">{icon img="toggle_off.png"}</a>
						{/if}						
					{/if}
					</div>
                {/permissions}
            </td>
            <td>
                {if $calc->calculator->hasConfig() == 1}
                    {icon action=configure record=$calc title="Configure"|gettext|cat:" `$calc->title`"}
                {/if}
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
        
</div>
