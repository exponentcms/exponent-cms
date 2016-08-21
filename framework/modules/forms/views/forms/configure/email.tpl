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
            {help text="Get Help with"|gettext|cat:" "|cat:("Form Email Settings"|gettext) module="form-email-settings"}
        </div>
        <h2>{"Form Email Settings"|gettext}</h2>
    </div>
</div>
{control type="checkbox" name="is_email" label="Email Form Submissions?"|gettext value=1 checked=$config.is_email description='Sends form responses to selected addresses based on Report single-record view configuration'|gettext focus=1}
{control type=text name='subject' label='Email Subject'|gettext value=$config.subject}
{group label='Email Recipients'|gettext}
    {control type="checkbox" name="select_email" label="User Selected Email Destination?"|gettext value=1 checked=$config.select_email description='Allows the user to choose from one or all of any recipients selected below'|gettext}
    {group label='User Selected Email Recipients'|gettext}
        {control type="checkbox" name="select_dropdown" label="Use dropdown instead of radio buttons?"|gettext value=1 checked=$config.select_dropdown description='Type of control used to display user selectible recipients'|gettext}
        {control type="checkbox" name="select_exclude_all" label='Exclude the \'All Addresses\' Choice?'|gettext value=1 checked=$config.select_exclude_all description='Restricts choice to a single recipient'|gettext}
    {/group}
    {userlistcontrol name="user_list" label="Users"|gettext items=$config.user_list}
    {grouplistcontrol name="group_list" label="Groups"|gettext items=$config.group_list}
    {control type="listbuilder" name="address_list" label="Other Addresses"|gettext values=$config.address_list size=5}
{/group}
