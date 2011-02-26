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

<div class="calendarmodule cal-summary">	
	<h2>
		{if $enable_ical == true}
			<a class="icallink itemactions" href="{link action=ical}" title="{$_TR.alt_ical}" alt="{$_TR.alt_ical}">{$_TR.ical}</a>
		{/if}
		{if $moduletitle != ""}{$moduletitle}{/if}
	</h2>
</div>
<div class="linklistmodule quick-links">
	<ul>
	{assign var=more_events value=0}	
	{assign var=item_number value=0}	
	{foreach from=$items item=item}
{if (!$__viewconfig.num_events || $item_number < $__viewconfig.num_events) }	
		<li>
		<div class="itemtitle cal_itemtitle">
		<a href="{link action=view id=$item->id date_id=$item->eventdate->id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
		{permissions level=$smarty.const.UILEVEL_PERMISSIONS}
			{if $permissions.administrate == 1 || $item->permissions.administrate == 1}
				<a class="mngmntlink calendar_mngmntlink" href="{link action=userperms int=$item->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}userperms.png" title="{$_TR.alt_userperm_one}" alt="{$_TR.alt_userperm_one}" /></a>
				<a class="mngmntlink calendar_mngmntlink" href="{link action=groupperms int=$item->id _common=1}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}groupperms.png" title="{$_TR.alt_groupperm_one}" alt="{$_TR.alt_groupperm_one}" /></a>
			{/if}
		{/permissions}
		{permissions level=$smarty.const.UILEVEL_NORMAL}
			{if $permissions.edit == 1 || $item->permissions.edit == 1}
				{if $item->approved == 1}
					<a class="mngmntlink calendar_mngmntlink" href="{link action=edit id=$item->id date_id=$item->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>
				{else}
					<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.disabled.png" title="{$_TR.alt_edit_disabled}" alt="{$_TR.alt_edit_disabled}" />
				{/if}
			{/if}
			{if $permissions.delete == 1 || $item->permissions.delete == 1}
				{if $item->approved == 1}
					{if $item->is_recurring == 0}
						<a class="mngmntlink calendar_mngmntlink" href="{link action=delete id=$item->id}" onclick="return confirm('{$_TR.delete_confirm}');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
					{else}
						<a class="mngmntlink calendar_mngmntlink" href="{link action=delete_form id=$item->id date_id=$item->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
					{/if}
				{else}
					<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.disabled.png" title="{$_TR.alt_delete_disabled}" alt="{$_TR.alt_delete_disabled}" />
				{/if}
			{/if}
			{if $permissions.manage_approval == 1}
				<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=revisions_view id=$item->id}" title="{$_TR.alt_revisions}"><img class="mngmnt_icon" src="{$smarty.const.ICON_RELATIVE}revisions.png" title="{$_TR.alt_revisions}" alt="{$_TR.alt_revisions}"/></a>
			{/if}
		{/permissions}
		</div>
		{if $item->is_allday == 1}
			{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT}
		{else}
			{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT} @ {$item->eventstart|format_date:"%l:%M %p"}
		{/if}
		</li>
		{assign var=item_number value=$item_number+1}
{else}
	{assign var=more_events value=1}	
{/if}
	{foreachelse}
		<li align="center"><i>{$_TR.no_event}</i></li>
	{/foreach}
	</ul>
</div>
<div class="calendarmodule cal-summary">	
	<p>
		{if $more_events == 1}
			<a class="mngmntlink monthviewlink" href="{link _common=1 view='Upcoming Events' action='show_view' time=$time}">{$_TR.more_events}</a>{br}
		{/if}
		{permissions level=$smarty.const.UILEVEL_NORMAL}
			{if $permissions.post == 1}
				<a class="addevent mngmntlink" href="{link action=edit id=0}" title="{$_TR.alt_create}">{$_TR.create}</a>{br}
			{/if}
			{if $in_approval != 0 && $canview_approval_link == 1}
				<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=calendarmodule s=$__loc->src action=summary}" title="{$_TR.alt_approval}" alt="{$_TR.alt_approval}">{$_TR.approval}</a>{br}
			{/if}
			{if $config->enable_categories == 1}
				{if $permissions.administrate == 1}
					<a class="mngmntlink cats" href="{link module=categories orig_module=calendarmodule action=manage}">{$_TR.manage_categories}</a>{br}
				{else}
					{*<a class="cats" href="#" onclick="window.open('{$smarty.const.PATH_RELATIVE}popup.php?module=categories&m={$__loc->mod}&action=view&src={$__loc->src}','legend','width=200,height=200,title=no,status=no'); return false" title="{$_TR.alt_view_cat}" alt="{$_TR.alt_view_cat}">{$_TR.view_categories}</a>{br}*}
				{/if}
			{/if}
			{if $permissions.administrate == 1}
				<a class="adminviewlink mngmntlink" href="{link _common=1 view='Administration' action='show_view' time=$time}">{$_TR.administration_view}</a>
			{/if}
		{/permissions}
	</p>
</div>
