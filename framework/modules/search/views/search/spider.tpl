{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
 
{css unique="search-spider" corecss="tables"}

{/css}

<div class="module search spider">
    <h1>{"Regenerating Search Index"|gettext}</h1>
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>{"Type of Content"|gettext}</th>
                <th>{"Number of Content Items"|gettext}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$mods key=name item=status}
            <tr class="{cycle values="odd,even"}">
        	    <td>{$name}</td>
        	    <td>
        	        {$status}
        	    </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
