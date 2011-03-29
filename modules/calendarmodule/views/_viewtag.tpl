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
 
{css unique="cal" link="`$smarty.const.PATH_RELATIVE`modules/calendarmodule/assets/css/calendar.css"}

{/css}

<div class="module calendar viewtag">
	<div class="module-actions">
		<a class="monthviewlink" href="{link action=viewmonth time=$time}">{$_TR.calendar_view}</a>
		{permissions}
			{if $permissions.administrate == 1}
				&nbsp;&nbsp;|&nbsp;&nbsp;<a class="adminviewlink mngmntlink" href="{link _common=1 view='Administration' action='show_view' time=$time}">{$_TR.administration_view}</a>
			{/if}
		{/permissions}
		&nbsp;&nbsp;|&nbsp;&nbsp;{printer_friendly_link class="printer-friendly-link" text=$_TR.printer_friendly}
	</div>
	<h2>
		{if $enable_ical == true}
			<a class="icallink module-actions" href="{link action=ical}" title="{$_TR.alt_ical}" alt="{$_TR.alt_ical}">{$_TR.ical}</a>
		{/if}
		{if $moduletitle != ""}{$moduletitle}{/if}
	</h2>
		permissions}
		<div class="module-actions">
			{if $permissions.post == 1}
				<a class="add" href="{link action=edit id=0}" title={"Create Event"|gettext}>{"Create Event"|gettext}</a>
			{/if}
		</div>
	{/permissions}
	<dl class="viewweek">
	{foreach from=$items item=item}
		<dt>
			<b><a class="itemtitle" href="{link action=view id=$item->id date_id=$item->eventdate->id}">{$item->title}</a></b>
			{permissions}
				<div class="item-actions">
					{if $permissions.edit == 1}
						<a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=$item->id date_id=$item->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>
					{/if}
					{if $permissions.delete == 1}
						{if $item->is_recurring == 0}
						<a class="mngmntlink calendar_mngmntlink" href="{link action=delete id=$item->id}" onclick="return confirm('{$_TR.delete_confirm}');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
						{else}
						<a class="mngmntlink calendar_mngmntlink" href="{link action=delete_form id=$item->id date_id=$item->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
						{/if}
					{/if}
				</div>
			{/permissions}
		</dt>
		<dd>
			<strong>
			{if $item->is_allday == 1}
				{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
			{elseif $item->eventstart != $item->eventend}
				{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT} @ {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} to {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
			{else}
				{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT} @ {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
			{/if}
			</strong>
		</dd>
		<dd>
			{$item->body|summarize:html:paralinks}
		</dd>
	{foreachelse}
		<<dd><em>{$_TR.no_events}</em></dd>
	{/foreach}
	</dl>
</div>
