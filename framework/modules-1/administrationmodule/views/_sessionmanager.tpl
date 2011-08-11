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
<div class="form_title">{'Manage User Sessions'|gettext}</div>
<div class="form_header">{'This page shows all of the active sessions, along with session information like login time, browser signature, etc.  You can forcibly end either a specific session or all sessions for a user account.  Ending a session will cause that user to be logged out of the site, and any content they were editting will be lost.<br /><br />Administrator sessions cannot be forcibly ended.'|gettext}</div>
<table cellpadding="4" cellspacing="0" border="0" width="100%">
	{foreach from=$sessions item=session}
	<tr>
		<td style="background-color: lightgrey">{$session->user->username}</td>
		<td style="background-color: lightgrey">{'IP'|gettext}: {$session->ip_address}</td>
		<td style="background-color: lightgrey">{'Duration'|gettext}: {foreach name=d from=$session->duration key=tag item=number}{$number}{if $smarty.foreach.d.last == false}:{/if}{/foreach}</td>
	</tr>
	<tr>
		<td colspan="3" style="padding-left: 10px; border: 1px solid lightgrey;">
			{if $session->user->is_acting_admin == 0 || ($session->user->is_acting_admin == 1 && $user->is_admin == 1 && $session->user->is_admin == 0)}
				<a class="mngmntlink administration_mngmntlink" href="{link action=session_kick ticket=$session->ticket}">{'End this session'|gettext}</a><br />
				<a class="mngmntlink administration_mngmntlink" href="{link action=session_kickuser id=$session->user->id}">{'End all session for this user.'|gettext}</a>
			{/if}
			<table>
				<tr>
					<td></td>
					<td>{'Logged In'|gettext}: </td>
					<td>{$session->start_time|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}</td>
				</tr>
				<tr>
					<td></td>
					<td>{'Last Active'|gettext}: </td>
					<td>{$session->last_active|format_date:$smarty.const.DISPLAY_DATE_FORMAT}</td>
				<tr>
					<td></td>
					<td>{'Browser'|gettext}: </td>
					<td>{$session->browser}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr></tr>
	{/foreach}
</table>