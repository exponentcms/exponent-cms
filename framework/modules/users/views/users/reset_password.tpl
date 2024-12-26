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
        <blockquote>
            {'To reset your password, enter your username or email address below.'|gettext}&#160;&#160;
            {'An email will be sent to the email address you provided along with instructions to reset your password.'|gettext}
        </blockquote>
    </div>

    {form action=send_new_password}
        {control type="text" name="username" label="Username/Email"|gettext}
		{control type=antispam}
        {control type="buttongroup" submit="Submit"|gettext}
    {/form}
</div>
