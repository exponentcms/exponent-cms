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
<div class="form_title">{$_TR.form_title}</div>
<div class="form_header">{$_TR.form_header}</div>
<table cellpadding="4" cellspacing="0" border="0" width="100%">
	{foreach from=$sessions item=session}
	<tr>
		<td style="background-color: lightgrey">{$session->user->username}</td>
		<td style="background-color: lightgrey">{$_TR.ip}: {$session->ip_address}</td>
		<td style="background-color: lightgrey">{$_TR.duration}: {foreach name=d from=$session->duration key=tag item=number}{$number}{if $smarty.foreach.d.last == false}:{/if}{/foreach}</td>
	</tr>
	<tr>
		<td colspan="3" style="padding-left: 10px; border: 1px solid lightgrey;">
			{if $session->user->is_acting_admin == 0 || ($session->user->is_acting_admin == 1 && $user->is_admin == 1 && $session->user->is_admin == 0)}
				<a class="mngmntlink administration_mngmntlink" href="{link action=session_kick ticket=$session->ticket}">{$_TR.end_one}</a><br />
				<a class="mngmntlink administration_mngmntlink" href="{link action=session_kickuser id=$session->user->id}">{$_TR.end_all}</a>
			{/if}
			<table>
				<tr>
					<td></td>
					<td>{$_TR.logged_in}: </td>
					<td>{$session->start_time|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}</td>
				</tr>
				<tr>
					<td></td>
					<td>{$_TR.last_active}: </td>
					<td>{$session->last_active|format_date:$smarty.const.DISPLAY_DATE_FORMAT}</td>
				<tr>
					<td></td>
					<td>{$_TR.browser}: </td>
					<td>{$session->browser}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr></tr>
	{/foreach}
</table>