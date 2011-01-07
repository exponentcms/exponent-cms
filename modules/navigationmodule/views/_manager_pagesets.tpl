{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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

<div class="navigationmodule manager-pagesets">
        <div class="form_header">
                <h1>{$_TR.form_title}</h1>
                <p>{$_TR.form_header}</p>
		<a class="newpage" href="{link action=edit_template}">{$_TR.new}</a>
        </div>

	<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<th><strong>{$_TR.pageset_title}</strong></th>
		<th><strong>{$_TR.actions}</strong></th>
	</tr>
	{foreach from=$templates item=t}
	<tr class="row {cycle values='odd,even'}_row">
		<td style="padding-left: 10px">
			<b>{$t->name}</b>
		</td>
		<td>
			[ <a href="{link action=view_template id=$t->id}">{$_TR.view}</a> ]
			[ <a href="{link action=edit_template id=$t->id}">{$_TR.properties}</a> ]
			[ <a href="{link action=delete_template id=$t->id}" onclick="return confirm('{$_TR.delete_confirm}');">{$_TR.delete}</a> ]
		</td>
	</tr>
	{foreachelse}
		<tr><td><i>{$_TR.no_pagesets}</i></td></tr>
	{/foreach}
	</table>
</div>
