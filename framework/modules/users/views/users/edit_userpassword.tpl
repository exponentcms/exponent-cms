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

<div class="module user manage-user-password">
    <h1>{'Change Password for'|gettext} {$u->username}</h1>
    {form action=update_userpassword}
        {control type="hidden" name="id" value=$u->id}
        {control type="password" name="new_password1" meter=1 label="Type New Password"|gettext required=1 focus=1}
        {control type="password" name="new_password2" label="Retype Password"|gettext required=1}
        {control type="buttongroup" submit="Change Password"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
