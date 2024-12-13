{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

<div class="module users reset-password">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help with"|gettext|cat:" "|cat:("Resetting User Passwords"|gettext) module="reset-my-password"}
        </div>
        <h2>{'Reset'|gettext} {if $isuser}{'your'|gettext}{else}{$u->username}'s{/if} {'password'|gettext}</h2>
        <blockquote>{'To reset your password enter a new password'|gettext}.</blockquote>
    </div>
    {form action=change_password_token}
        {control type="hidden" name="uid" value=$u->id}
        {control type="hidden" name="token" value=$token}
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
