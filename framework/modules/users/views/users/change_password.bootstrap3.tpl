{*
 * Copyright (c) 2004-2015 OIC Group, Inc.
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
            {control type="password" name="password" label="Current Password"|gettext}
        {/if}
        <div class="row">
            {control class="col-sm-4" type="password" name="new_password1" label="Enter your new password"|gettext}
            <div class="col-sm-4" style="padding-top: 8px;">
                <div class="pwstrength_viewport_progress"></div>
            </div>
        </div>
        {control type="password" name="new_password2" label="Confirm your new password"|gettext}
        {br}
        {control type="buttongroup" submit="Change My Password"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="showlogin" jquery='pwstrength-bootstrap-1.2.5'}
{literal}
    $(document).ready(function () {
        "use strict";
        var options = {};
        options.ui = {
            container: ".change-password",
            showVerdictsInsideProgressBar: true,
            showErrors: true,
            viewports: {
                progress: ".pwstrength_viewport_progress",
                errors: ".pwstrength_viewport_progress",
            }
        };
        $('#new_password1').pwstrength(options);
    });
{/literal}
{/script}
