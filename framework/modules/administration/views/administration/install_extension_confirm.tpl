{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="install" corecss="button,tables"}

{/css}

<div class="exporter extension-filelist">
	<h1>{"Uploading New Extension"|gettext}</h1>
	{assign var=haveFiles value=1}
	{assign var=failed value=0}
	{assign var=warn value=0}
	<table cellpadding="0" cellspacing="0" width="100%" border="0" class="exp-skin-table">
		<thead>
			<tr>
				<th class="header administration_header">{'File'|gettext}</th>
				<th class="header administration_header">{'Status'|gettext}</th>
				{*<th class="header administration_header"></th>*}
			</tr>
		</thead>
		<tbody>
			{foreach from=$files item=file}
				<tr class="{cycle values="odd,even"}">
					<td>{$file.absolute}</td>
					<td>
						{if $file.canCreate == $smarty.const.SYS_FILES_SUCCESS}
							<span style="color: green;">{'passed'|gettext}</span>
						{elseif $file.canCreate == $smarty.const.SYS_FILES_FOUNDFILE || $file.canCreate == $smarty.const.SYS_FILES_FOUNDDIR}
							{assign var=warn value=1}
							<span style="color: orange;">{'file exists'|gettext}</span>
						{else}
							{assign var=failed value=1}
							<span style="color: red;">{'failed'|gettext}</span>
						{/if}
					</td>
					{*<td>*}
				{*	{if $file.ext == "tpl" || $file.ext == "php"}*}
				{*	{capture assign="filearg"}{$smarty.const.PATH_RELATVE}{$relative}{$file.absolute}{/capture}*}
				{*		<a class="mngmntlink administration_mngmntlink" href="{link module=filemanager action=viewcode file=$filearg}">*}
				{*			{if $file.ext == "tpl"}{'View Template'|gettext}{else}{'View PHP Code'|gettext}{/if}*}
				{*		</a>*}
				{*	{/if}*}
					{*</td>*}
				</tr>
			{foreachelse}
				{assign var=haveFiles value=0}
				<tr><td colspan="3">
					<i>{'No files were found in the archive'|gettext}</i>
				</td></tr>
			{/foreach}
		</tbody>
	</table>
	{if $haveFiles == 1}
{*		<br />*}
{*		<hr size="1" />*}
		{if $failed == 0}
			{if $warn == 1}{'<b>Note:</b> Continuing with the installation will overwrite existing files.  It is <b>highly recommended</b> that you ensure that you want to do this.'|gettext}<br /><br />{/if}
			<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link action=install_extension_finish patch=$patch}">{'Continue with Installation'|gettext}</a>
		{else}
			{'Permissions on the webserver are preventing the installation of this extension.  Please make the necessary directories writable, and then reload this page to continue.'|gettext}
		{/if}
	{/if}
</div>