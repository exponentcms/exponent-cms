{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
 
{css unique="manage_groups" corecss="tables"}

{/css}
 
<div class="module users manage-sessions">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Managing User Sessions"|gettext) module="manage-sessions"}
        </div>
        <h1>{"Manage User Sessions"|gettext}</h1>
    </div>
    <p>
        {'This page shows all of the active sessions, along with session information like login time, browser signature, etc.'|gettext}&nbsp;&nbsp;
        {'You can forcibly end either a specific session or all sessions for a user account.'|gettext}&nbsp;&nbsp;
        {'Ending a session will cause that user to be logged out of the site, and any content they were editing will be lost.'|gettext}
        {br}{br}
        <em>{'Administrator sessions cannot be forcibly ended'|gettext}.</em>
    </p>
	<p><a href="{link module='users' action='manage_sessions' id=$filter}">{if $filter != 0}{'Show all Sessions'|gettext}{else}{'Restrict list to Logged-In Users'|gettext}{/if}</a></p>
    <table cellpadding="4" cellspacing="0" border="0" width="100%">
	    {foreach from=$sessions item=session}
	    <tr>
		    <td style="background-color: lightgrey"><strong>{$session->user->username}</strong></td>
		    <td style="background-color: lightgrey">IP: {$session->ip_address}</td>
		    {*<td style="background-color: lightgrey">Duration: {foreach name=d from=$session->duration key=tag item=number}{$number}{if $smarty.foreach.d.last == false}:{/if}{/foreach}</td>*}
			<td style="background-color: lightgrey">{'Duration'|gettext}: {foreach name=d from=$session->duration key=tag item=number}{$number} {$tag}{if $smarty.foreach.d.last == false}, {/if}{/foreach}</td>
	    </tr>
	    <tr>
		    <td colspan="3" style="padding-left: 10px; border: 1px solid lightgrey;">
			    {if $session->user->is_acting_admin == 0 || ($session->user->is_acting_admin == 1 && $user->is_admin == 1 && $session->user->is_admin == 0)}
				    <a href="{link controller=users action=kill_session ticket=$session->ticket}">{'End this session'|gettext}</a><br />
				    <a href="{link controller=users action=boot_user id=$session->user->id}">{'End all sessions for this user'|gettext}</a>
			    {/if}
			    <table>
				    <tr>
					    <td>{'Logged In'|gettext}: </td>
					    <td>{$session->start_time|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT}</td>
				    </tr>
				    <tr>
					    <td width="100">{'Last Active'|gettext}: </td>
					    <td>{$session->last_active|format_date}</td>
				    <tr>
					    <td>{'Browser'|gettext}: </td>
					    <td>{$session->browser}</td>
				    </tr>
			    </table>
		    </td>
	    </tr>
	    <tr></tr>
	    {/foreach}
    </table>
</div>
