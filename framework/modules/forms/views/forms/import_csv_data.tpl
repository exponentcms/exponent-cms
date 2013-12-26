{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{css unique="exporteql" corecss="tables"}

{/css}

<div class="importer import_csv">
	<div class="form_header">
		<h2>{'Import Form Data - Form Data Imported Into Database'|gettext}</h2>
		<blockquote>{'The following data was added to the database.  If the record is highlighted green, then the form record was added to the database with changes to the data.  If the record is highlighted in red, that record could not be added to the database due to errors.'|gettext}</blockquote>
	</div>
	<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr>
			<td class="header importer_header">{'Status'|gettext}</td>
			<td class="header importer_header">{'User ID'|gettext}</td>
			<td class="header importer_header">{'Username'|gettext}</td>
			<td class="header importer_header">{'Password'|gettext}</td>
			<td class="header importer_header">{'First Name'|gettext}</td>
			<td class="header importer_header">{'Last Name'|gettext}</td>
			<td class="header importer_header">{'Email'|gettext}</td>
		</tr>
		{foreach from=$userarray item=user}
			<tr class="{cycle values='even,odd'}">
				<td style="background-color:inherit;">
					{if $user->changed == 1}<span style="color:green;">{'Changed'|gettext}</span>
					{elseif $user->changed == "skipped"}<span style="color:red;">{'Ignored&#160;(Line&#160;%s)'|sprintf:$user->linenum})</span>
					{else}<span style="color:black;">{'Success'|gettext}</span>
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
