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
<div class="form_title">{'Optimize Database'|gettext}</div>
<div class="form_header">{'Exponent is running table optimization right now, to rebuild the internal structure of your database.  With large sites or sites that change regularly, this optimization can enhance the overall performance and responsiveness of the site.'|gettext}</div>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
	<td class="header administration_header">{'Table Name'|gettext}</td>
	<td class="header administration_header" align="right">{'Size of Data (kb)'|gettext}</td>
</tr>

{foreach from=$before key=table item=info}
<tr class="row {cycle values='odd,even'}_row">
	<td>{$table}</td>
	<td align="right">{math format="%.3f" equation="x / 1024" x=$info->data_total} {'kb'|gettext}</td>
</tr>
{/foreach}
</table>