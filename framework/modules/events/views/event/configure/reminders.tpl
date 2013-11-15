{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
<code>curl -G -s {$smarty.const.URL_FUL}/event/send_reminders/title/THE_CALENDAR_SEF_URL/days/14/code/THE_CODE_FROM_BELOW</code>
</blockquote>
{control type="text" name="reminder_code" label="Code to restrict sending Email Reminders"|gettext description="Enter an optional alphanumeric code to better secure sending reminder emails"|gettext value=$config.reminder_code}
{group label="Email Recepients"|gettext}
    {userlistcontrol name="user_list" label="Users" items=$config.user_list}
    {grouplistcontrol name="group_list" label="Groups" items=$config.group_list}
    {control type="listbuilder" name="address_list" label="Other Addresses" values=$config.address_list size=5}
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