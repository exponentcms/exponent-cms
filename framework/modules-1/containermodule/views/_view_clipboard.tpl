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

<div class="containermodule view-clipboard">
<h1>{'Clipboard Contents'|gettext}</h1>
<table cellspacing="0" cellpadding="5" border="0" width="100%">
	<tr>
		<td class="header">{'Module Type'|gettext}</td>
		<td class="header">{'Title'|gettext}</td>
		<td class="header">{'Copied From Page'|gettext}</td>
		<td class="header">{'Cut/Copy'|gettext}?</td>
		<td class="header">{'Description'|gettext}</td>
	</tr>
	{foreach name=a from=$items item=item}
        <tr class="row {cycle values='odd_row,even_row'}">
            <td>{$item->module}</td>
            <td><b>{$item->title}</b></td>
            <td>{$item->copied_from}</td>
            <td>{$item->operation}</td>
            <td>{$collection->description}</td>
        <tr>
    {foreachelse}
        <tr>
            <td colspan="2" align="center"><i>{'Your clipboard is currently empty'|gettext}.</i></td>
        </tr>
    {/foreach}
</table>
</div>
