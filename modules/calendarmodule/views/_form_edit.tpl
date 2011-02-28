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
 
{css unique="calendar-edit1" link="`$smarty.const.YUI2_PATH`assets/skins/sam/calendar.css"}

{/css}

{css unique="calendar-edit2" link="`$smarty.const.PATH_RELATIVE`modules/calendarmodule/assets/css/cal-edit.css"}

{/css}

<div class="calendarmodule edit">
	<div class="form_title">
		{if $is_edit == 1}{$_TR.form_title_edit}{else}{$_TR.form_title_new}{/if}
	</div>
	<div class="form_header">
		<p>{$_TR.form_header}</p>
	</div>
	{$form_html}
</div>

