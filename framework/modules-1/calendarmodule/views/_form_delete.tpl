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

{css unique="calendar-form-delete" corecss="tables"}

{/css}

<div class="module calendar delete">
<h1>{'Delete Event'|gettext}</h1>
<div class="form_header"><p>{'The event you have opted to delete is a recurring event.  You can decide to delete just this instance of it, or all instances, below.'|gettext}</p></div>
<form action="?" method="post">
<input type="hidden" name="module" value="calendarmodule" />
<input type="hidden" name="action" value="delete_process" />
<input type="hidden" name="id" value="{$event->id}" />
<table cellspacing="0" cellpadding="2" width="100%" class="exp-skin-table">
{include file="_recur_dates.tpl"}
<tr>
	<td colspan="2"><input class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" type="submit" value="{'Delete Selected'|gettext}" /></td>
</tr>
</table>
</form>
	</div>
