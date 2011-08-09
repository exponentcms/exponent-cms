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

<div class="importer usercsv-display-users">
	<div class="form_header">
		<h2>{$_TR.form_title}</h2>
		<p>{$_TR.form_header}</p>
	</div>
	<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr>
			<td class="header importer_header">{$_TR.status}</td>
			<td class="header importer_header">{$_TR.user_id}</td>
			<td class="header importer_header">{$_TR.username}</td>
			<td class="header importer_header">{$_TR.password}</td>
			<td class="header importer_header">{$_TR.first_name}</td>
			<td class="header importer_header">{$_TR.last_name}</td>
			<td class="header importer_header">{$_TR.email}</td>
		</tr>
		{foreach from=$userarray item=user}
			<tr class="row {cycle values=even_row,odd_row}">
				<td style="background-color:inherit;">
					{if $user->changed == 1}<span style="color:green;">{$_TR.changed}</span>
					{elseif $user->changed == "skipped"}<span style="color:red;">{$_TR.skipped|sprintf:$user->linenum})</span>
					{else}<span style="color:black;">{$_TR.success}</span>
					{/if}
				</td>
				<td>{$user->id}</td>
				<td>{$user->username}</td>
				<td>{$user->clearpassword}</td>
				<td>{$user->firstname}</td>
				<td>{$user->lastname}</td>
				<td>{$user->email}</td>
			</tr>
		{/foreach}
	</table>
</div>
