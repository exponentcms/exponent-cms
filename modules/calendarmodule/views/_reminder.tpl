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

{literal}
<style type="text/css">
	.caption {text-align: center;font-weight: bold;border-top: 1px solid;border-bottom: 1px solid;}
	.viewweek caption {line-height: 2.5em;text-align: center;font-weight: bold;border-top: 1px solid;}
	.viewweek td {padding: .1em .1em .1em 0;}
	.viewweek {border: none;width:100%;list-style: none;margin: 0;padding: 0;}
	.viewweek dt {line-height: 2em; border-top: 1px solid;}
</style>
{/literal}
 
<div class="calendarmodule cal-default"> 
	<h2>
	{if $moduletitle != ""}{$moduletitle}{/if}
	</h2>
	<center><h4>
	{if $totaldays == 1}
		<a href="{link module=calendarmodule action=viewmonth time=$start}">{$_TR.view1} {$start|format_date:"%B %e, %Y"}</a>
	{else}
		<a href="{link module=calendarmodule action=viewmonth time=$start}">{$_TR.view1}{$_TR.view2}{$totaldays}{$_TR.view3} {$start|format_date:"%B %e, %Y"}</a>
	{/if}
	</h4></center>
	<dl class="viewweek">
		{foreach from=$days item=events key=ts}
			{if $counts[$ts] != 0}
				<dt>
					<strong>
						<a class="itemtitle calendar_mngmntlink" href="{link module=calendarmodule action=viewday time=$ts}">{$ts|format_date:"%A, %b %e"}</a>
					</strong>
				</dt>
				{foreach from=$events item=event}
					{assign var=catid value=$event->category_id}
					<dd>
						<strong>
							<a class="itemtitle calendar_mngmntlink" href="{link module=calendarmodule action=view id=$event->id date_id=$event->eventdate->id}">{$event->title}</a>
						</strong>							
						<div><span style="color: {$categories[$catid]->color}">
							&nbsp-&nbsp 
							{if $event->is_allday == 1}
								All Day
							{else}
								{if $event->eventstart != $event->eventend}
									{$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} to {$event->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
								{else}
									{$event->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
								{/if}
							{/if}
							{if $showdetail == 1}
								&nbsp-&nbsp{$event->body|summarize:"html":"paralinks"}
							{/if}
							{br}
						</span></div>
					</dd>
				{/foreach}
			{/if}
		{/foreach}
	</dl>
</div>
