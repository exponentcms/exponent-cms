{*
 *
 * Copyright (c) 2004-2006 OIC Group, Inc.
 * Copyright (c) 2006 Maxim Mueller
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * Exponent is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU
 * General Public License along with Exponent; if
 * not, write to:
 *
 * Free Software Foundation, Inc.,
 * 59 Temple Place,
 * Suite 330,
 * Boston, MA 02111-1307  USA
 *
 *}
<div class="calendarmodule view">

	{include file="`$smarty.const.BASE`modules/common/views/_permission_icons.tpl"}

	{permissions level=$smarty.const.UILEVEL_PERMISSIONS}
	<div class="itemactions">
		{if $permissions.edit == 1 || $item->permissions.edit == 1}
			{if $item->approved == 1}
			<a href="{link action=edit id=$item->id date_id=$item->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.png" title="{$_TR.alt_edit}" alt="{$_TR.alt_edit}" /></a>&nbsp;
			{else}
			<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}edit.disabled.png" title="{$_TR.alt_edit_disabled}" alt="{$_TR.alt_edit_disabled}" />
			{/if}
		{/if}
	{if $permissions.delete == 1 || $item->permissions.delete == 1}
		{if $item->approved == 1}
			{if $item->is_recurring == 0}
			<a href="{link action=delete id=$item->id}" onclick="return confirm('{$_TR.delete_confirm}');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
			{else}
			<a href="{link action=delete_form date_id=$item->eventdate->id id=$item->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.png" title="{$_TR.alt_delete}" alt="{$_TR.alt_delete}" /></a>
			{/if}
		{else}
		<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}delete.disabled.png" title="{$_TR.alt_delete_disabled}" alt="{$_TR.alt_delete_disabled}" />
		{/if}
	{/if}
	{if $permissions.manage_approval == 1}
		<a class="mngmntlink calendar_mngmntlink" href="{link module=workflow datatype=calendar m=CalendarModule s=$__loc->src action=revisions_view id=$item->id}" title="{$_TR.alt_revisions}" alt="{$_TR.alt_revisions}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}revisions.png" title="{$_TR.alt_revisions}" alt="{$_TR.alt_revisions}" /></a>
	{/if}
	</div>
	{/permissions}

	<h1>{$item->title}</h1>
	{if $item->is_allday == 1}
	{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT}, {$_TR.all_day}
	{else}
	{$item->eventstart|format_date:$smarty.const.DISPLAY_DATE_FORMAT} {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} - {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
	{/if}
	<div class="bodycopy">
		{$item->body}
	</div>
	<div class="moduleactions">
		{$form}
		<a class="mngmntlink calendar_mngmntlink" href="{link action=viewweek time=$item->eventstart}" title="{$_TR.alt_view_week}" alt="{$_TR.alt_view_week}">{$_TR.view_week}</a>&nbsp;|&nbsp;
		<a class="mngmntlink calendar_mngmntlink" href="{link action=viewmonth time=$item->eventstart}" title="{$_TR.alt_view_month}" alt="{$_TR.alt_view_month}">{$_TR.view_month}</a><br />
	</div>
</div>


