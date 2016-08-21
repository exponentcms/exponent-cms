{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Events Reminders"|gettext) module="event-reminders"}
		</div>
        <h2>{"Events Reminder Email Settings"|gettext}</h2>
	</div>
</div>
{control type="checkbox" postfalse=1 name="reminder_active" label="Enable Email Reminder feature?"|gettext checked=$config.reminder_active value=1}
<blockquote>
    {'Reminders feature requires setting up a server cron task such as:'|gettext}{br}
    <code>curl -G -s {$smarty.const.URL_FULL}event/send_reminders/title/CALENDAR_SEF_URL/days/14/code/CODE_FROM_BELOW</code>
    <ul>
        <li><strong>title</strong>: {'calendar sef url'|gettext}</li>
        <li><strong>code</strong>: {'security code, if set below'|gettext}</li>
        <li>days: {'number of days forward to include'|gettext} (<em>{'optional, defaults to 7'|gettext}</em>)</li>
        <li>time: {'date to begin from'|gettext} (<em>{'optional'|gettext}</em>)</li>
        <li>view: {'view template'|gettext} (<em>{'optional, defaults to send_reminders'|gettext}</em>)</li>
    </ul>
</blockquote>
{control type="text" name="reminder_code" label="Code to restrict sending Email Reminders"|gettext description="Enter an optional alphanumeric code to better secure sending reminder emails"|gettext value=$config.reminder_code}
{group label="Email Recipients"|gettext}
    {userlistcontrol name="user_list" label="Users"|gettext items=$config.user_list}
    {grouplistcontrol name="group_list" label="Groups"|gettext items=$config.group_list}
    {control type="listbuilder" name="address_list" label="Other Addresses"|gettext values=$config.address_list size=5}
{/group}
{group label="Email Details"|gettext}
    {control type="text" name="email_title_reminder" label="Message Subject Prefix"|gettext value=$config.email_title_reminder}
    {control type="text" name="email_from_reminder" label="From (Display)"|gettext value=$config.email_from_reminder}
    {*{control type="text" name="email_address_reminder" label="From (Email Address)"|gettext value=$config.email_address_reminder}*}
    {control type=email name="email_address_reminder" label="From (Email Address)"|gettext value=$config.email_address_reminder}
    {*{control type="text" name="email_reply_reminder" label="Reply-to"|gettext value=$config.email_reply_reminder}*}
    {control type=email name="email_reply_reminder" label="Reply-to"|gettext value=$config.email_reply_reminder}
    {control type="checkbox" name="email_showdetail" label="Show event details in message?"|gettext value=1 checked=$config.email_showdetail}
    {control type="textarea" name="email_signature" label="Email Signature"|gettext value=$config.email_signature}
{/group}