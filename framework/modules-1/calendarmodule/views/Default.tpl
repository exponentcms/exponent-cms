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

<div class="module calendar default">
	<div class="module-actions">
		<span class="monthviewlink">{'Calendar View'|gettext}</span>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="listviewlink" href="{link _common=1 view='Monthly List' action='show_view' time=$time}">{'List View'|gettext}</a>
		{permissions}
			{if $permissions.manage == 1}
				&nbsp;&nbsp;|&nbsp;&nbsp;<a class="adminviewlink mngmntlink" href="{link _common=1 view='Administration' action='show_view' time=$time}">{'Administration View'|gettext}</a>
			{/if}
			&nbsp;&nbsp;|&nbsp;&nbsp;
			{printer_friendly_link text='Printer-friendly'|gettext}
			{br}
		{/permissions}
	</div>
	<h1>
		{if $enable_ical == true}
			<a class="icallink module-actions" href="{link action=ical}" title="{'iCalendar Feed'|gettext}" alt="{'iCalendar Feed'|gettext}"> </a>
		{/if}
		{if $moduletitle}{$moduletitle}{/if}
	</h1>
	{permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
	<table id="calendar" summary="{$moduletitle|default:'Calendar'|gettext}">
	<caption>
	&laquo;&nbsp;
	<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth time=$prevmonth3}" title="{$prevmonth3|format_date:"%B %Y"}">{$prevmonth3|format_date:"%b"}</a>&nbsp;&nbsp;&laquo;&nbsp;
	<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth time=$prevmonth2}" title="{$prevmonth2|format_date:"%B %Y"}">{$prevmonth2|format_date:"%b"}</a>&nbsp;&nbsp;&laquo;&nbsp;
	<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth time=$prevmonth}" title="{$prevmonth|format_date:"%B %Y"}">{$prevmonth|format_date:"%b"}</a>&nbsp;&nbsp;&laquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<b>{$time|format_date:"%B %Y"}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;
	<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth time=$nextmonth}" title="{$nextmonth|format_date:"%B %Y"}">{$nextmonth|format_date:"%b"}</a>&nbsp;&nbsp;&raquo;&nbsp;
	<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth time=$nextmonth2}" title="{$nextmonth2|format_date:"%B %Y"}">{$nextmonth2|format_date:"%b"}</a>&nbsp;&nbsp;&raquo;&nbsp;
	<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth time=$nextmonth3}" title="{$nextmonth3|format_date:"%B %Y"}">{$nextmonth3|format_date:"%b"}</a>&nbsp;&nbsp;&raquo;
	</caption>

		<tr class="daysoftheweek">
			{if $smarty.const.DISPLAY_START_OF_WEEK == 0}
			<th scope="col" abbr="{'Sunday'|gettext}" title="'Sunday'|gettext}">{'Sunday'|gettext}</th>
			{/if}
			<th scope="col" abbr="{'Monday'|gettext}" title="{'Monday'|gettext}">{'Monday'|gettext}</th>
			<th scope="col" abbr="{'Tuesday'|gettext}" title="{'Tuesday'|gettext}">{'Tuesday'|gettext}</th>
			<th scope="col" abbr="{'Wednesday'|gettext}" title="{'Wednesday'|gettext}">{'Wednesday'|gettext}</th>
			<th scope="col" abbr="{'Thursday'|gettext}" title="{'Thursday'|gettext}">{'Thursday'|gettext}</th>
			<th scope="col" abbr="{'Friday'|gettext}" title="{'Friday'|gettext}">{'Friday'|gettext}</th>
			<th scope="col" abbr="{'Saturday'|gettext}" title="{'Saturday'|gettext}">{'Saturday'|gettext}</th>
			{if $smarty.const.DISPLAY_START_OF_WEEK != 0}
			<th scope="col" abbr="{'Sunday'|gettext}" title="{'Sunday'|gettext}">{'Sunday'|gettext}</th>
			{/if}
		</tr>
		{math equation="x-86400" x=$now assign=dayts}
		{foreach from=$monthly item=week key=weeknum}
			{assign var=moredata value=0}
			{foreach name=w from=$week key=day item=events}
				{assign var=number value=$counts[$weeknum][$day]}
				{if $number > -1}{assign var=moredata value=1}{/if}
			{/foreach}
			{if $moredata == 1}
			<tr class="week{if $currentweek == $weeknum} currentweek{/if}">
			{foreach name=w from=$week key=day item=items}
				{assign var=number value=$counts[$weeknum][$day]}
				<td {if $dayts == $today}class="today" {elseif $number == -1}class="notinmonth" {else}class="oneday" {/if}>
					{if $number > -1}
						{if $number == 0}
							<span {if $dayts == $today}class="number today"{else}class="number"{/if}>
								{$day}
							</span>
						{else}
							<a class="number" href="{link action=viewday time=$dayts}" title="{$dayts|format_date:'%A, %B %e, %Y'}">{$day}</a>
						{/if}
					{/if}
					{foreach name=e from=$items item=item}
						<div {if $dayts == $today}class="calevent today"{else}class="calevent"{/if}>
							<a class="mngmntlink calendar_mngmntlink" href="{link action=view id=$item->id date_id=$item->eventdate->id}"
							   title="{if $item->is_allday == 1}{'All Day'|gettext}{elseif $item->eventstart != $item->eventend}{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} {'to'|gettext} {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}{else}{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}{/if} - {$item->body|summarize:"html":"para"}">{$item->title}</a>
							{permissions}
								<div class="item-actions">
									{if $permissions.edit == 1}
										{icon img="edit.png" action=edit record=$item date_id=$item->eventdate->id title="Edit this Event"|gettext}
									{/if}
									{if $permissions.delete == 1}
										{if $item->is_recurring == 0}
											{icon img="delete.png" action=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
										{else}
											{icon img="delete.png" action=delete_form record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
										{/if}
									{/if}
								</div>	
							{/permissions}
						</div>						
					{/foreach}				
					{if $number != -1}{math equation="x+86400" x=$dayts assign=dayts}{/if}
				</td>
			{/foreach}
			</tr>
			{/if}
		{/foreach}
	</table>
</div>
