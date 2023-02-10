{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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
    {'The password for username'|gettext} '{$username}' {'has been reset'|gettext}.
</p>
<p>
    {'If you did not request to set a new password, return to'|gettext} <a href="{link controller=users action=reset_password}">{$smarty.const.HOSTNAME}</a> {'and enter your username to (re)start the process to set your password.'|gettext}&#160;&#160;
</p>

{'Thanks!'|gettext}

