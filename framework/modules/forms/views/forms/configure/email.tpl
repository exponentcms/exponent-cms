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
            {help text="Get Help with"|gettext|cat:" "|cat:("Form Email Settings"|gettext) module="form-email-settings"}
        </div>
        <h2>{"Form Email Settings"|gettext}</h2>
    </div>
</div>
{control type="checkbox" name="is_email" label="Email Form Submissions?"|gettext value=1 checked=$config.is_email description='Sends form responses to selected addresses based on Report single-record view configuration'|gettext}
{control type=text name='subject' label='Email Subject'|gettext value=$config.subject}
{group label='Email Recepients'|gettext}
    {control type="checkbox" name="select_email" label="User Selected Email Destination?"|gettext value=1 checked=$config.select_email description='Allows the user to choose from one or all of any recepients selected below'|gettext}
    {userlistcontrol name="user_list" label="Users" items=$config.user_list}
    {grouplistcontrol name="group_list" label="Groups" items=$config.group_list}
    {control type="listbuilder" name="address_list" label="Other Addresses" values=$config.address_list size=5}
{/group}
