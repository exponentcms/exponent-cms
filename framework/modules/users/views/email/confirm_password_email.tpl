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

<p>
    {'The password for username'|gettext} '{$username}' {'has been reset'|gettext}.  {'Your new password is'|gettext}{br}{br}
    {$newpass}
</p>
<p>
    {'To use the new password, return to'|gettext} <a href="http://{$smarty.const.URL_FULL}/login.php">{$smarty.const.HOSTNAME}</a> {'and log in using your username and this password.'|gettext}&#160;&#160;
    {'After you log in you can use the \'Change Password\' feature to set the password to one of your choosing.'|gettext}
</p>

{'Thanks!'|gettext}

