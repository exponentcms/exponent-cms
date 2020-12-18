{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

<p>
    {'We received a request to reset the account password at'|gettext} {$smarty.const.URL_FULL} {'for username'|gettext} '{$username}'.
</p>
<p>
    {'Please follow this link to reset your password'|gettext}:
    <a href="{link controller=users action=confirm_password_reset token=$token->token uid=$token->uid}">{'Reset Password'|gettext}</a>
</p>
<p>
    {'If you did not request a password reset, please disregard and delete this email.'|gettext}
    {'The password reset request expires after 2 hours.'|gettext}
<p>