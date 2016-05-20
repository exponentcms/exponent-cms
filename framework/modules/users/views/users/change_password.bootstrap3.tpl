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

<div class="module users change-password">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Changing User Passwords"|gettext) module="change-my-password"}
        </div>
        <h2>{'Change'|gettext} {if $isuser}{'your'|gettext}{else}{$u->username}'s{/if} {'password'|gettext}</h2>
        {if $isuser}
            <blockquote>{'To change your password enter your current password and then enter what you would like your new password to be'|gettext}.</blockquote>
        {/if}
    </div>
    {form action=save_change_password}
        {control type="hidden" name="uid" value=$u->id}
        {control type="hidden" name="username" value=$u->username}
        {if $isuser}
            {control type="password" name="password" label="Current Password"|gettext required=1}
        {/if}
        {control type="password" name="new_password1" class="col-sm-4" meter=1 label="Enter your new password"|gettext required=1}
        {control type="password" name="new_password2" label="Confirm your new password"|gettext required=1}
        {br}
        {control type="buttongroup" submit="Change My Password"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
