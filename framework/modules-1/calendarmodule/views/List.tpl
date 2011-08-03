{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div class="module calendar list"> 
	<a class="monthviewlink" href="{link _common=1 view=Default action=show_view time=$time}">{$_TR.calendar_view}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="listviewlink">{$_TR.list_view}</span><br />
	<a href="#" onclick="window.open('popup.php?module=calendarmodule&src={$__loc->src}&view=Monthly List&template=printerfriendly&time={$time}','printer','title=no,scrollbars=no,width=800,height=600'); return false">{$_TR.printer_friendly}</a>
	{br}{br}
	<a class="mngmntlink calendar_mngmntlink" href="{link action=show_view _common=1 view='Monthly List' time=$prev_timestamp}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}left.png" title="{$_TR.previous}" alt="{$_TR.previous}" /></a>
	<b>{$time|format_date:"%B %Y"}</b>
	<a class="mngmntlink calendar_mngmntlink" href="{link action=show_view _common=1 view='Monthly List' time=$next_timestamp}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}right.png" title="{$_TR.next}" alt="{$_TR.next}" /></a>
	{br}{br}
	{foreach from=$days item=items key=ts}
		{if_elements array=$items}
			<div class="sectiontitle">
			{$ts|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
			</div>
			{assign var=none value=1}
			{foreach from=$items item=item}
				{assign var=none value=0}
				<div class="paragraph">
					<a class="mngmntlink calendar_mngmntlink" href="{link action=view id=$item->id date_id=$item->eventdate->id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
					{if $item->is_allday == 0}&nbsp;{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} - {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}{/if}
					{if $permissions.edit == 1 || $item->permissions.edit == 1 || $permissions.delete == 1 || $item->permissions.delete == 1 || $permissions.administrate == 1 || $item->permissions.administrate == 1}
						<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					{/if}
					{permissions}
						<div class="item-actions">
							{if $permissions.edit == 1 || $item->permissions.edit == 1}
								{icon action=edit record=$item date_id=$item->eventdate->id title="Edit this Event"|gettext}
							{/if}
							{if $permissions.delete == 1 || $item->permissions.delete == 1}
								{if $item->is_recurring == 0}
									{icon action=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
								{else}
									{icon action=delete_form class=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
								{/if}
							{/if}
						</div>
					{/permissions}
				</div>
				{br}
			{/foreach}
			{if $none == 1}
				<div class="paragraph"><strong>{$_TR.no_events}</strong></div>
			{/if}
			{br}
		{/if_elements}
	{/foreach}
	{permissions}
		{if $permissions.post == 1}
			<div class="module-actions">
				{icon class="add" action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			</div>
		{/if}
	{/permissions}
</div>
