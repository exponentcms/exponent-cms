{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
 
<div class="module search spider">
    <h1>Regenerating Search Index</h1>
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <th>Type of Content</th>
        <th>Number of Content Items</th>
    </tr>
    {foreach from=$mods key=name item=status}
    <tr class="row {cycle values=odd_row,even_row}">
	    <td>{$name}</td>
	    <td>
	        {$status}
	    </td>
    </tr>
    {/foreach}
    </table>
</div>
