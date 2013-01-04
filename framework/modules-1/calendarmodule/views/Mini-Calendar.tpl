{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
 
{css unique="cal" link="`$smarty.const.PATH_RELATIVE`framework/modules-1/calendarmodule/assets/css/calendar.css"}

{/css}

<div class="module calendar mini-cal"> 
	<table class="mini-cal">
		<p>
            <a class="nav doublearrow" href="{link action=viewmonth time=$prevmonth view='Mini-Calendar'}" title="{'Prev'|gettext}">&laquo;</a>
            {$now|format_date:"%B"}
            <a class="nav doublearrow" href="{link action=viewmonth time=$nextmonth view='Mini-Calendar'}" title="{'Next'|gettext}">&raquo;</a>
        </p>

		<tr class="daysoftheweek">
			{if $smarty.const.DISPLAY_START_OF_WEEK == 0}
			<th scope="col" abbr="{'Sun'|gettext}" title="{'Sunday'|gettext}">{'S'|gettext}</th>
			{/if}
			<th scope="col" abbr="{'Mon'|gettext}" title="{'Monday'|gettext}">{'M'|gettext}</th>
			<th scope="col" abbr="{'Tue'|gettext}" title="{'Tuesday'|gettext}">{'T'|gettext}</th>
			<th scope="col" abbr="{'Wed'|gettext}" title="'Wednesday'|gettext}">{'W'|gettext}</th>
			<th scope="col" abbr="{'Thu'|gettext}" title="{'Thursday'|gettext}">{'T'|gettext}</th>
			<th scope="col" abbr="{'Fri'|gettext}" title="{'Friday'|gettext}">{'F'|gettext}</th>
			<th scope="col" abbr="{'Sat'|gettext}" title="{'Saturday'|gettext}">{'S'|gettext}</th>
			{if $smarty.const.DISPLAY_START_OF_WEEK != 0}
			<th scope="col" abbr="{'Sun'|gettext}" title="{'Sunday'|gettext}">{'S'|gettext}</th>
			{/if}
		</tr>
		{foreach from=$monthly item=week key=weekid}
			<tr class="{if $currentweek == $weekid}calendar_currentweek{/if}">
				{foreach from=$week key=day item=dayinfo}
					<td>
						{if $dayinfo.number > -1}
							{if $dayinfo.number == 0}
								{$day}
							{else}
								<a class="mngmntlink calendar_mngmntlink" href="{link action=viewday time=$dayinfo.ts}" title="{$dayinfo.ts|format_date:'%A, %B %e, %Y'}"><em>{$day}</em></a>
							{/if}
						{else}
							&#160;
						{/if}
					</td>
				{/foreach}
			</tr>
		{/foreach}
	</table>
	<a class="mngmntlink calendar_mngmntlink" href="{link action=viewmonth}">{'View Month'|gettext}</a>
	{br}
	{permissions}
		{if $permissions.create == 1}
			<div class="module-actions">
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			</div>
		{/if}
	{/permissions}
</div>
