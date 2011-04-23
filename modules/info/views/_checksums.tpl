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
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="header info_header">{$_TR.file}</td>
		<td class="header info_header">{$_TR.checksum}</td>
	</tr>
{if $error == ""}
{foreach from=$files key=file item=oldmd5}
	{capture assign=relpath}{$relative[$file].dir}{$relative[$file].file}{/capture}
	{assign var=csum value=$checksums[$file]}
	{if $csum == ""}{assign var=csum value=$_TR.no_md5}{/if}
	<tr class="row {cycle values=even_row,odd_row}">
		<td>{$relative[$file].dir}<b><a href="{link module=filemanager action=viewcode file=$relpath}">{$relative[$file].file}</a></b></td>
		{if $checksums[$file] == $oldmd5}
		<td style="color: green;">{$csum}</td>
		{else}
		<td style="color: red;">{$csum}</td>
		{/if}
	</tr>
{/foreach}
{else}
	<tr>
		<td align="center" colspan="2"><i>{$error}</i></td>
	</tr>
{/if}
</table>