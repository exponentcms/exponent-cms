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

<div class="module administration mass-mail">
    <div class="form_header">
   		<div class="info-header">
   			<div class="related-actions">
   			    {help text="Get Help with"|gettext|cat:" "|cat:("mass mailing"|gettext) module="mass-mailer"}
   			</div>
   			<h1>{'Mass Mailer'|gettext}</h1>
   		</div>
   	</div>
    <blockquote>
        {'This form allows you to send an email with an optional attachment to site users.'|gettext}
    </blockquote>
    {form action=mass_mail_out}
        {group label="Send this Message To"|gettext}
            {control type="checkbox" class="emailall" postfalse=1 name="allusers" label="All Site Users?"|gettext value=1 description='Uncheck to allow user/group/freeform selection'|gettext}
            {control type="checkbox" postfalse=1 name="batchsend" label="Batch Send?"|gettext value=1 checked=1 description='Hide email addresses from other users'|gettext}
            {userlistcontrol class="email" name="user_list" label="Users"}
            {grouplistcontrol class="email" name="group_list" label="Groups"}
            {control type="listbuilder" class="email" name="address_list" label="Other Addresses" size=5}
        {/group}
        {group label="Message"|gettext}
            {control type="text" name="subject" label="Subject"|gettext}
            {control type="html" name="body" label="Message"|gettext}
            {control type="uploader" name="attach" label="Attachment"|gettext description='Optionally send a file attachment'|gettext}
        {/group}
        {control type="buttongroup" submit="Send"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="mass-mailer" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
        var mailall = Y.all('input.emailall');
        var emailnodes = Y.all('div.email');
        mailall.on('click',function(e){
            if (e.currentTarget.get('checked')) {
                emailnodes.setStyle('display','none');
            } else {
                emailnodes.setStyle('display','block');
            }
        });
    });
{/literal}
{/script}
