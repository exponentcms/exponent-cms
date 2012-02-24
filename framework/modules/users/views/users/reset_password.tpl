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

<div class="module users reset-password">
    <h1>{'Reset Your Password'|gettext}</h1>
    <p>
        {'To reset your password, enter your username/email address below.'|gettext}&nbsp;&nbsp;
        {'An email will be sent to the email address you provided along with instructions to reset your password.'|gettext}
    </p>
    
    {form action=send_new_password}
        {control type="text" name="username" label="Username/Email"|gettext}
		{control type=antispam}
        {control type="buttongroup" submit="Submit"|gettext}
    {/form}
</div>
