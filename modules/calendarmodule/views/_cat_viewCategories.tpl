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
<h1>{$_TR.form_title}</h1>
<table cellspacing="0" cellpadding="2" border="0">
{foreach from=$categories item=category}
	<td>{$category->name}</td>
	<td>
		<div style="width: 32px; height: 16px; background-color: {$category->color}">&nbsp;</div>
	</td>
</tr>
{foreachelse}
<tr>
	<td colspan="2" align="center"><i>{$_TR.no_categories}</i></td>
</tr>
{/foreach}
</table>