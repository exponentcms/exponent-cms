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

<div class="module administration mass-mail">
    <h1>{'Mass Mailer'|gettext}</h1>
    <block>
        {'This form allows you to send an email with optional attachment to site users.'|gettext}
    </block>
    {form action=mass_mail_out}
        <h2>{'Send this Message To'|gettext}</h2>
        {control type="checkbox" class="emailall" postfalse=1 name="allusers" label="All Site Users?"|gettext value=1}
        {control type="checkbox" postfalse=1 name="batchsend" label="Batch Send (Hide other user emails)?"|gettext value=1 checked=1}
        {userlistcontrol class="email" name="user_list" label="Users"}
        {grouplistcontrol class="email" name="group_list" label="Groups"}
        {control type="listbuilder" class="email" name="address_list" label="Other Addresses"}
        <hr>
        {control type="text" name="subject" label="Subject"|gettext}
        {control type="html" name="body" label="Message"|gettext}
        {control type="uploader" name="attach" label="Attachment"|gettext}
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
