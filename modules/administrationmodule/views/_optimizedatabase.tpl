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
<div class="form_title">{$_TR.form_title}</div>
<div class="form_header">{$_TR.form_header}</div>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
	<td class="header administration_header">{$_TR.table_name}</td>
	<td class="header administration_header" align="right">{$_TR.data_size}</td>
</tr>

{foreach from=$before key=table item=info}
<tr class="row {cycle values='odd,even'}_row">
	<td>{$table}</td>
	<td align="right">{math format="%.3f" equation="x / 1024" x=$info->data_total} {$_TR.kb}</td>
</tr>
{/foreach}
</table>