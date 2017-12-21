{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

{css unique="verifyfiles" corecss="button,tables"}

{/css}

<div class="importer files-verifyfiles">
	<div class="form_header">
		<h2>{'Selected Files to Import'|gettext}</h2>
	</div>
    {$haveFiles=0}
    {$failed=0}
    {$warn=0}
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="exp-skin-table">
		<thead>
            <tr>
                <th>{'Filename'|gettext}</th>
                <th></th>
            </tr>
		</thead>
		<tbody>
			{foreach from=$files_data item=status key=filename }
                {$haveFiles=1}
				<tr class="{cycle values='even,odd'}">
					<td>{$filename}</td>
					<td>
						{if $status == $smarty.const.SYS_FILES_SUCCESS}
							<span style="color: green;">{'passed'|gettext}</span>
						{elseif $status == $smarty.const.SYS_FILES_FOUNDFILE || $status == $smarty.const.SYS_FILES_FOUNDDIR}
                            {$warn=1}
							<span style="color: orange;">{'file exists'|gettext}</span>
						{else}
                            {$failed=1}
							<span style="color: red;">{'failed'|gettext}</span>
						{/if}
					</td>
				</tr>
			{foreachelse}
                <tr><td colspan="2"><em>{'No Files Selected'|gettext}</em></td></tr>
			{/foreach}
		</tbody>
	</table>
	{if $haveFiles == 1}
		{if $failed == 0}
			{if $warn == 1}{'<strong>Note:</strong> Continuing with the installation will overwrite existing files.  It is <strong>highly recommended</strong> that you ensure that you want to do this.'|gettext}<br /><br />{/if}
			{*<a class="{button_style}" href="{link action=import_files_finish}">{'Restore Files'|gettext}</a>*}
            {icon button=true action=import_files_finish text='Restore Files'|gettext}
		{else}
			{'Permissions on the webserver are preventing the restoration of these files.  Please make the necessary directories and/or files writable, and then reload this page to continue.'|gettext}
		{/if}
	{/if}
</div>