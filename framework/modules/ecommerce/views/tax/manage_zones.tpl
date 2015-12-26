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

<h1>{"Tax Zone Manager"|gettext}</h1>

<div class="module-actions">
    {icon action=edit_zone class="add" text="Add a Tax Zone"|gettext}
</div>
{br}
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            <th>
                {'Name'|gettext}
            </th>
            <th>
                {'State'|gettext}
            </th>
            <th>
                {'Country'|gettext}
            </th>
            <th>
                {'Action'|gettext}
            </th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$zones item=zone key=key name=zones}
            <tr class="{cycle values="odd,even"}">
                <td>
                    {$zone->name}
                </td>
                <td>
                    {$zone->state}
                </td>
                <td>
                    {$zone->country}
                </td>
                <td class="module-actions">
                    {icon action=edit_zone record=$zone img="edit.png" title='Edit tax class'|gettext}
                    {icon action=delete_zone record=$zone img="delete.png" title='Delete tax zone along with classes and rates'|gettext}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
{br}
<div class="module-actions">
    {icon action=manage class="reply" text="Return to Tax Manager"|gettext}
</div>
