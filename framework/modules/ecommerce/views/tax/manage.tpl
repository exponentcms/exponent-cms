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

{css unique="tax" corecss="tables"}

{/css}

<h1>{"Tax Manager"|gettext}</h1>

<div class="module-actions">
    {icon action=edit class="add" text="Add a Tax Rate"|gettext}
    {icon action=manage_classes class="manage" text="Manage Tax Classes"|gettext}
    {icon action=manage_zones class="manage" text="Manage Tax Zones"|gettext}
</div>
{br}
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            <th>
                {'Enabled'|gettext}
            </th>
            <th>
                {'Class'|gettext}
            </th>
            <th>
                {'Rate'|gettext}
            </th>
            <th>
                {'Origin'|gettext}
            </th>
            <th>
                {'Shipping'|gettext}
            </th>
            <th>
                {'Zone'|gettext}
            </th>
			 <th>
                {'Action'|gettext}
            </th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$taxes item=tax key=key name=taxes}
            <tr class="{cycle values="odd,even"}">
                <td>
                    {if $tax->inactive != 1}
                        {icon img="clean.png" color=green title='Enabled'|gettext}
                    {/if}
                </td>
                <td>
                    {$tax->classname}
                </td>
                <td>
                    {$tax->rate|number_format:2}%
                </td>
                <td>
                    {if $tax->origin_tax == 1}{icon img="clean.png" color=green}{/if}
                </td>
                <td>
                    {if $tax->shipping_taxed == 1}{icon img="clean.png" color=green}{/if}
                </td>
                <td>
                    {$tax->zonename} <em>({if !empty($tax->state)}{$tax->state}, {/if}{$tax->country})</em>
                </td>
                <td class="module-actions">
                    {icon action=edit record=$tax img="edit.png"}
                    {icon action=delete record=$tax img="delete.png"}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
