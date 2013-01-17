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
            {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Form Email Settings"|gettext) module="form-email-settings"}
        </div>
        <h2>{"Form Email Settings"|gettext}</h2>
    </div>
</div>
{control type="checkbox" name="is_email" label="Email Form Data?"|gettext value=1 checked=$config.is_email}
{control type=text name='subject' label='Email Subject'|gettext value=$config.subject}
{group label='Email Recepients'|gettext}
    {control type="checkbox" name="select_email" label="Allow User to Select the Destination Email?"|gettext value=1 checked=$config.select_email}
    {userlistcontrol name="user_list" label="Users" items=$config.user_list}
    {grouplistcontrol name="group_list" label="Groups" items=$config.group_list}
    {control type="listbuilder" name="address_list" label="Other Addresses" values=$config.address_list}
{/group}
{group label='Auto Respond Email'|gettext}
    {control type="checkbox" name="is_auto_respond" label="Auto Respond?"|gettext value=1 checked=$config.is_auto_respond}
    {control type="text" name="auto_respond_subject" label="Auto Respond Subject"|gettext value=$config.auto_respond_subject}
    {control type="textarea" name="auto_respond_body" label="Auto Respond Body"|gettext value=$config.auto_respond_body}
{/group}