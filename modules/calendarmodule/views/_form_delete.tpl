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
<div class="form_title">{$_TR.form_title}</div>
<div class="form_header"><p>{$_TR.form_header}</p></div>
<form action="?" method="post">
<input type="hidden" name="module" value="calendarmodule" />
<input type="hidden" name="action" value="delete_process" />
<input type="hidden" name="id" value="{$event->id}" />
<table cellspacing="0" cellpadding="2" width="100%">
{include file="_recur_dates.tpl"}
<tr>
	<td colspan="2"><input type="submit" value="{$_TR.delete_selected}" /></td>
</tr>
</table>
</form>
