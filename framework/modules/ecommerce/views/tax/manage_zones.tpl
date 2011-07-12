{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

{icon action=edit_zone class="add" text="Add a Tax Zone"}  
{br}
{br}
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            <th style="width:50px">
                &nbsp;
            </th>
            <th>
                Name
            </th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$zones item=zone key=key name=zones}
        <tr class="{cycle values="odd,even"}">
            <td>
                {icon action=edit_zone record=$zone img="edit.png"}  
                {icon action=delete_zone record=$zone img="delete.png"}  
            </td>
            <td>
            {$zone->name}
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>

