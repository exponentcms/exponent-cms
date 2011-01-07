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

<div class="form_header">
	<h1>{$_TR.form_title}</h1>
	<p>{$_TR.form_header}</p>
</div>
<table cellpadding="2" cellspacing="0" width="100%" border="0">
<tr>
	<td class="header administration_header">{$_TR.table_name}</td>
	<td class="header administration_header">{$_TR.status}</td>
</tr>
{foreach from=$status key=table item=statusnum}
<tr class="row {cycle values='odd,even'}_row"><td>
{$table}
</td><td>
{if $statusnum == $smarty.const.TMP_TABLE_EXISTED}
<div style="color: blue; font-weight: bold">{$_TR.table_exists}</div>
{elseif $statusnum == $smarty.const.TMP_TABLE_INSTALLED}
<div style="color: green; font-weight: bold">{$_TR.succeeded}</div>
{elseif $statusnum == $smarty.const.TMP_TABLE_FAILED}
<div style="color: red; font-weight: bold">{$_TR.failed}</div>
{elseif $statusnum == $smarty.const.TMP_TABLE_ALTERED}
<div style="color: green; font-weight: bold">{$_TR.altered_existing}</div>
{elseif $statusnum == $smarty.const.TABLE_ALTER_FAILED}
<div style="color: red; font-weight: bold">{$_TR.alter_failed}</div>
{/if}
</td></tr>
{/foreach}
</table>
