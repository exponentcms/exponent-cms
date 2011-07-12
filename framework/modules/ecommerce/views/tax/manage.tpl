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

<h1>{"Tax Class Manager"|gettext}</h1>

{icon action=edit class="add" text="Add a Tax Class"}  
{icon action=manage_zones class="manage" text="Manage Zones"}
{br}
{br}
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            <th>
                Class
            </th>
            <th>
                Rate
            </th>
            <th>
                Zone
            </th>
            <th>
                State
            </th>
            <th>
                Country
            </th>
			 <th>
                Action
            </th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$taxes item=tax key=key name=taxes}
        <tr class="{cycle values="odd,even"}">
            <td>
            {$tax->classname}
            </td>
            <td>
            {$tax->rate|number_format:2}%
            </td>
            <td>
            {$tax->zonename}
            </td>
            <td>
            {$tax->state}
            </td>
            <td>
            {$tax->country}
            </td>
			<td>
                {icon action=edit record=$tax img="edit.png"}  
                {icon action=delete record=$tax img="delete.png"}  
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>

