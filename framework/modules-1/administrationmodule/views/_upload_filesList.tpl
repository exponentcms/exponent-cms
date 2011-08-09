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

{css unique="install" corecss="tables"}

{/css}

<div class="exporter extension-filelist">
	<h1>Uploading new Extension</h1>
	{assign var=haveFiles value=1}
	{assign var=failed value=0}
	{assign var=warn value=0}
	<table cellpadding="0" cellspacing="0" width="100%" border="0" class="exp-skin-table">
		<thead>
			<tr>
				<th class="header administration_header">{$_TR.file}</th>
				<th class="header administration_header">{$_TR.status}</th>
				<th class="header administration_header"></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$files item=file}
				<tr class="{cycle values="odd,even"}">
					<td>{$file.absolute}</td>
					<td>
						{if $file.canCreate == $smarty.const.SYS_FILES_SUCCESS}
							<span style="color: green;">{$_TR.passed}</span>
						{elseif $file.canCreate == $smarty.const.SYS_FILES_FOUNDFILE || $file.canCreate == $smarty.const.SYS_FILES_FOUNDDIR}
							{assign var=warn value=1}
							<span style="color: orange;">{$_TR.file_exists}</span>
						{else}
							{assign var=failed value=1}
							<span style="color: red;">{$_TR.failed}</span>
						{/if}
					</td>
					<td>
				{*	{if $file.ext == "tpl" || $file.ext == "php"}*}
				{*	{capture assign="filearg"}{$smarty.const.PATH_RELATVE}{$relative}{$file.absolute}{/capture}*}
				{*		<a class="mngmntlink administration_mngmntlink" href="{link module=filemanager action=viewcode file=$filearg}">*}
				{*			{if $file.ext == "tpl"}{$_TR.view_template}{else}{$_TR.view_php}{/if}*}
				{*		</a>*}
				{*	{/if}*}
					</td>
				</tr>
			{foreachelse}
				{assign var=haveFiles value=0}
				<tr><td colspan="3">
					<i>{$_TR.no_files}</i>
				</td></tr>
			{/foreach}
		</tbody>
	</table>
	{if $haveFiles == 1}
{*		<br />*}
{*		<hr size="1" />*}
		{if $failed == 0}
			{if $warn == 1}{$_TR.overwrite_warning}<br /><br />{/if}
			<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link action=finish_install_extension}">{$_TR.install}</a>
		{else}
			{$_TR.bad_permissions}
		{/if}
	{/if}
</div>