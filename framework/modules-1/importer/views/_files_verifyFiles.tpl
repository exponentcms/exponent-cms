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

<div class="importer files-verifyfiles">
	{assign var=haveFiles value=0}
	{assign var=failed value=0}
	{assign var=warn value=0}
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		{foreach from=$files_data item=mod_data key=modname }
			<tr><td colspan="2"><b>{if $mod_data[0] != ''}{$mod_data[0]}{else}{$_TR.unknown_module} : {$modname}{/if}</b></td></tr>
			{foreach from=$mod_data[1] key=file item=status}
				{assign var=haveFiles value=1}
				<tr>
					<td style="padding-left: 2.5em;">{$file}</td>
					<td>
						{if $status == $smarty.const.SYS_FILES_SUCCESS}
							<span style="color: green;">{$_TR.passed}</span>
						{elseif $status == $smarty.const.SYS_FILES_FOUNDFILE || $status == $smarty.const.SYS_FILES_FOUNDDIR}
							{assign var=warn value=1}
							<span style="color: orange;">{$_TR.file_exists}</span>
						{else}
							{assign var=failed value=1}
							<span style="color: red;">{$_TR.failed}</span>
						{/if}
					</td>
				</tr>
			{foreachelse}<tr><td colspan="2"><i>{$_TR.no_files}</i></td></tr>
			{/foreach}
		{foreachelse}<tr><td colspan="2"><i>{$_TR.no_modules}</i></td></tr>
		{/foreach}
	</table>
	{if $haveFiles == 1}
		<br />
		<hr size="1" />
		{if $failed == 0}
			{if $warn == 1}{$_TR.overwrite_warning}<br /><br />{/if}
			<a class="mngmntlink" href="{link action=page page=finish importer=files}">{$_TR.restore_files}</a>
		{else}
			{$_TR.bad_permissions}
		{/if}
	{/if}
</div>