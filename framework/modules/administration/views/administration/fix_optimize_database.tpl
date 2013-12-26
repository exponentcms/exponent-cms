{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{css unique="optimize-database" corecss="tables"}

{/css}

<h1>{'Optimized the Database Tables'|gettext}</h1>
<table  class="exp-skin-table" cellspacing="0" cellpadding="0" border="0" width="100%">
	<thead>
		<tr>
			<th>{'Table Name'|gettext}</th>
			<th>{'Size of Data'|gettext} ({'kb'|gettext})</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$before key=table item=info}
			<tr class="{cycle values="even, odd"}">
				<td>{$table}</td>
				<td align="right">{$info->data_total|bytes}</td>
			</tr>
		{/foreach}
	</tbody>
</table>