{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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
{css unique="standalone" corecss="tables"}

{/css}


<div class="navigationmodule manager-standalone">
	<div class="form_header">
		<h1>{$_TR.form_title}</h1>
		<p>{$_TR.form_header}</p>
		<a class="add" href="{link action=edit_contentpage parent=-1}">{$_TR.new}</a>
	</div>

	<table cellpadding="2" cellspacing="0" border="0" width="100%" class="exp-skin-table">
    <thead>
        <tr>
    		<th><strong>{$_TR.page_title}</strong></th>
    		<th><strong>{$_TR.actions}</strong></th>
    		<th><strong>{$_TR.permissions}</strong></th>
    	</tr>
	</thead>
	<tbody>
	{foreach from=$sections item=section}

	<tr class="{cycle values=odd,even}">
	<td>
		{if $section->active}
			<a href="{link section=$section->id}" class="navlink">{$section->name}</a>&nbsp;
		{else}
			{$section->name}&nbsp;
		{/if}
	</td><td>
        {icon action=edit_contentpage id=$section->id img=edit.png title=$_TR.alt_edit}
        {icon action=delete id=$section->id img=delete.png title=$_TR.alt_delete onclick="return confirm('`$_TR.delete_confirm`');"}
	</td><td>
        {icon int=$section->id action=userperms _common=1 img=userperms.png title=$_TR.alt_userperm}
        {icon int=$section->id action=groupperms _common=1 img=groupperms.png title=$_TR.alt_groupperm}
	</td></tr>
	{foreachelse}
		<tr><td colspan=3><i>{$_TR.no_pages}</i></td></tr>
	{/foreach}
	</tbody>
	</table>
</div>
