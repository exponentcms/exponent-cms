{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Events Reminders"|gettext) module="event-reminders"}
		</div>
        <h2>{"Events Reminder Email Settings"|gettext}</h2>
	</div>
</div>
{*$form->register('addresses',gt('Other Addresses'),new listbuildercontrol($defaults,null),true,gt('Reminders'));*}
{*{control type="listbuilder" name="addresses" label="Other Addresses" values=['1'=>'Administrator']}*}
{userlistcontrol name="users" label="Users" items=$config.users}
{grouplistcontrol name="groups" label="Groups" items=$config.groups}
{control type="listbuilder" name="addresses" label="Other Addresses" values=$config.addresses}
{control type="text" name="email_title_reminder" label="Message Subject Prefix"|gettext value=$config.email_title_reminder}
{control type="text" name="email_from_reminder" label="From (Display)"|gettext value=$config.email_from_reminder}
{control type="text" name="email_address_reminder" label="From (Email Address)"|gettext value=$config.email_address_reminder}
{control type="text" name="email_reply_reminder" label="Reply-to"|gettext value=$config.email_reply_reminder}
{control type="checkbox" name="email_showdetail" label="Show detail in message?"|gettext value=1 checked=$config.email_showdetail}
{control type="textarea" name="email_signature" label="Email Signature"|gettext value=$config.email_signature}
