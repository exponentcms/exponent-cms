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
			<tr><td colspan="2"><b>{if $mod_data[0] != ''}{$mod_data[0]}{else}{'Unknown module'|gettext} : {$modname}{/if}</b></td></tr>
			{foreach from=$mod_data[1] key=file item=status}
				{assign var=haveFiles value=1}
				<tr>
					<td style="padding-left: 2.5em;">{$file}</td>
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
			{foreachelse}<tr><td colspan="2"><i>{'No Files found'|gettext}</i></td></tr>
			{/foreach}
		{foreachelse}<tr><td colspan="2"><i>{'No Module Types Selected'|gettext}</i></td></tr>
		{/foreach}
	</table>
	{if $haveFiles == 1}
		<br />
		<hr size="1" />
		{if $failed == 0}
			{if $warn == 1}{'<b>Note:</b> Continuing with the installation will overwrite existing files.  It is <b>highly recommended</b> that you ensure that you want to do this.'|gettext}<br /><br />{/if}
			<a class="mngmntlink" href="{link action=page page=finish importer=files}">{'Restore Files'|gettext}</a>
		{else}
			{$'Permissions on the webserver are preventing the restoration of these files.  Please make the necessary directories and/or files writable, and then reload this page to continue.'|gettext}
		{/if}
	{/if}
</div>