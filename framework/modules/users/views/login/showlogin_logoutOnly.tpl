{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

{css unique="showlogin-logoutonly" corecss="button"}

{/css}

{if $loggedin == true || $smarty.const.PREVIEW_READONLY == 1}
    <div class="login logout">
        {*<a class="{button_style}" href="{link action=logout}">{'Logout'|gettext}</a>*}
        {icon button=true action=logout text='Logout'|gettext}
    </div>
{/if}
