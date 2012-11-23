{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div class="module calendar monthly"> 
	<table class="calendar_monthly">
		<tbody>
			<tr>
				<td>
					<a class="mngmntlink calendar_mngmntlink" href="{link action=viewmonth time=$prevmonth}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'left.png'}" title="{'Previous Month'|gettext}" alt="{'Previous Month'|gettext}" /></a>
				</td>
				<td colspan="5">{if $moduletitle && !$config->hidemoduletitle}{$moduletitle}{/if} {$now|format_date:"%B %Y"}</td>
				<td>
					<a class="mngmntlink calendar_mngmntlink" href="{link action=viewmonth time=$nextmonth}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'right.png'}" title="{'Next Month'|gettext}" alt="{'Next Month'|gettext}" /></a>
				</td>
			</tr>
			<tr>
				<td>{'Sunday'|gettext}</td>
				<td>{'Monday'|gettext}</td>
				<td>{'Tuesday'|gettext}</td>
				<td>{'Wednesday'|gettext}</td>
				<td>{'Thursday'|gettext}</td>
				<td>{'Friday'|gettext}</td>
				<td>{'Saturday'|gettext}</td>
			</tr>
			{*{math equation="x-86400" x=$now assign=dayts}*}
            {$dayts=$now-86400}
			{foreach from=$monthly item=week key=weeknum}
				<tr class="{if $currentweek == $weeknum}calendar_currentweek{/if}">
					{*foreach name=w from=$week key=day item=events*}
					{foreach from=$week key=day item=dayinfo}
						<td class="daytitle{if $dayinfo.number == -1} notaday{/if}">
							{*{if $number != -1}{math equation="x+86400" x=$dayts assign=dayts}{/if}*}
                            {if $number != -1}{$dayts=$dayts+86400}{/if}
							{if $dayinfo.number > -1}
								<div class="daycell">{$day}</div>
							{/if}
							{if $dayinfo.number > 0}
								<a class="mngmntlink calendar_mngmntlink" href="{link action=viewday time=$dayinfo.ts}" title="{$dayinfo.ts|format_date:"%A, %B %e, %Y"}" alt="{$dayinfo.ts|format_date:"%A, %B %e, %Y"}">
								{$dayinfo.number} {plural singular=Event plural=Events count=$dayinfo.number}
								</a>
							{/if}
						</td>
					{/foreach}
				</tr>
			{/foreach}
		</tbody>
	</table>
	{permissions}
		{if $permissions.create == 1}
			<div class="module-actions">
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			</div>
		{/if}
	{/permissions}
</div>