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

{css unique="verifyfiles-buttons" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/button.css"}

{/css}

{css unique="verifyfiles" corecss="tables"}

{/css}

<div class="importer files-verifyfiles">
	<div class="form_header">
		<h2>{'Selected Files to Import'|gettext}</h2>
	</div>
	{assign var=haveFiles value=0}
	{assign var=failed value=0}
	{assign var=warn value=0}
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="exp-skin-table">
		<thead>
			<th>Filename</th>
			<th></th>
		</thead>
		<tbody>
			{foreach from=$files_data item=status key=filename }
				{assign var=haveFiles value=1}
				<tr class="row {cycle values=even_row,odd_row}">
					<td>{$filename}</td>
					<td>
						{if $status == $smarty.const.SYS_FILES_SUCCESS}
							<span style="color: green;">{'passed'|gettext}</span>
						{elseif $status == $smarty.const.SYS_FILES_FOUNDFILE || $status == $smarty.const.SYS_FILES_FOUNDDIR}
							{assign var=warn value=1}
							<span style="color: orange;">{'file exists'|gettext}</span>
						{else}
							{assign var=failed value=1}
							<span style="color: red;">{'failed'|gettext}</span>
						{/if}
					</td>
				</tr>
			{foreachelse}<tr><td colspan="2"><i>{'No Files Selected'|gettext}</i></td></tr>
			{/foreach}
		</tbody>
	</table>
	{if $haveFiles == 1}
		{if $failed == 0}
			{if $warn == 1}{'<b>Note:</b> Continuing with the installation will overwrite existing files.  It is <b>highly recommended</b> that you ensure that you want to do this.'|gettext}<br /><br />{/if}
			<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link action=page page=finish importer=files}">{'Restore Files'|gettext}</a>
		{else}
			{'Permissions on the webserver are preventing the restoration of these files.  Please make the necessary directories and/or files writable, and then reload this page to continue.'|gettext}
		{/if}
	{/if}
</div>