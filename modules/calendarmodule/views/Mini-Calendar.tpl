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
 
{css unique="cal" link="`$smarty.const.PATH_RELATIVE`modules/calendarmodule/assets/css/calendar.css"}

{/css}

<div class="calendarmodule mini-cal"> 
	<table class="mini-cal">
	<caption><a class="nav doublearrow" href="{link action=viewmonth time=$prevmonth view='Mini-calendar'}" title="{$_TR.alt_previous}">&laquo;</a> {$now|format_date:"%B"} <a class="nav doublearrow" href="{link action=viewmonth time=$nextmonth view='Mini-Calendar'}" title="{$_TR.alt_next}">&raquo;</a></caption>

			<tr class="daysoftheweek">
				{if $smarty.const.DISPLAY_START_OF_WEEK == 0}
				<th scope="col" abbr="{$_TR.sunday}" title="{$_TR.sunday}">{$_TR.sunday}</th>
				{/if}
				<th scope="col" abbr="{$_TR.monday}" title="{$_TR.monday}">{$_TR.monday}</th>
				<th scope="col" abbr="{$_TR.tuesday}" title="{$_TR.tuesday}">{$_TR.tuesday}</th>
				<th scope="col" abbr="{$_TR.wednesday}" title="{$_TR.wednesday}">{$_TR.wednesday}</th>
				<th scope="col" abbr="{$_TR.thursday}" title="{$_TR.thursday}">{$_TR.thursday}</th>
				<th scope="col" abbr="{$_TR.friday}" title="{$_TR.friday}">{$_TR.friday}</th>
				<th scope="col" abbr="{$_TR.saturday}" title="{$_TR.saturday}">{$_TR.saturday}</th>
				{if $smarty.const.DISPLAY_START_OF_WEEK != 0}
				<th scope="col" abbr="{$_TR.sunday}" title="{$_TR.sunday}">{$_TR.sunday}</th>
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
					&nbsp;
				{/if}
				</td>
			{/foreach}
		</tr>
	{/foreach}
	</table>
	<a class="mngmntlink calendar_mngmntlink" href="{link action=viewmonth}">{$_TR.view_month}</a>
	<br />
	{permissions level=$smarty.const.UILEVEL_NORMAL}
	{if $permissions.post == 1}
	<a class="mngmntlink calendar_mngmntlink" href="{link action=edit}" title="{$_TR.alt_create}">{$_TR.create}</a><br />
	{/if}
	{if $in_approval != 0 && $canview_approval_link == 1}
	<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=summary}" title="{$_TR.alt_approval}">{$_TR.view_approval}</a><br />
	{/if}
	{if $config->enable_categories == 1}
	{if $permissions.manage_categories == 1}
	<br />
	<a href="{link module=categories orig_module=calendarmodule action=manage}" class="mngmntlink cats">{$_TR.manage_categories}</a>
	{else}
	<br />
	<a class="cats" href="#" onclick="window.open('{$smarty.const.PATH_RELATIVE}popup.php?module=categories&m={$__loc->mod}&action=view&src={$__loc->src}','legend','width=200,height=200,title=no,status=no'); return false" title="{$_TR.alt_view_cat}" alt="{$_TR.alt_view_cat}">{$_TR.view_categories}</a>
	{/if}
	{/if}
	{/permissions}
</div>
