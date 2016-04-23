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

{css unique="showlogin"}
    .kv-scorebar-border {
        margin: 0;
        margin-top: 3px;
    }
{/css}

<div class="module user manage-user-password">
    <h1>{'Change Password for'|gettext} {$u->username}</h1>
    {form action=update_userpassword}
        {control type="hidden" name="id" value=$u->id}
        {control type="password" name="new_password1" label="Type New Password"|gettext focus=1}
        {control type="password" name="new_password2" label="Retype Password"|gettext}
        {control type="buttongroup" submit="Change Password"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="showlogin" jquery='strength-meter'}
{literal}
    $("#new_password1").strength({
        toggleMask: false,
        mainTemplate: '<div class="kv-strength-container">{input}<div class="kv-meter-container">{meter}</div></div>',
    }).on('strength.change', function(event) {
        if (event.target.value.length < {/literal}{$smarty.const.MIN_PWD_LEN}{literal})
            $("#new_password1").strength('paint', 0);
    });
{/literal}
{/script}
