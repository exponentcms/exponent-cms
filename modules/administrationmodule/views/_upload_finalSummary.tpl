{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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
{if $nofiles == 1}{$_TR.no_files}{else}
<table cellpadding="0" cellspacing="0" border="0" width="100%">
{foreach from=$success item=status key=file}
<tr>
	<td>{$file}</td>
	<td>
		{if $status == 1}
		<span style="color: green">{$_TR.copied}</span>
		{else}
		<span style="color: red">{$_TR.failed}</span>
		{/if}
	</td>
</tr>
{/foreach}
</table>
<a class="mngmntlink administration_mngmntlink" href="{$redirect}">{$_TR.back}</a>
{/if}