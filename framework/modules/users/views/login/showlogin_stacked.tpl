{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{css unique="showlogin-expanded" corecss="button,forms"}

{/css}

<div class="module login stacked">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h2>{$moduletitle}</h2>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {if $loggedin == false}
        <div>
            {form action=login}
                {control type="text" name="username" label="Username"|gettext|cat:":" size=25 required=1}
                {control type="password" name="password" label="Password"|gettext|cat:":" size=25 required=1}
                {control type="buttongroup" submit="Log In"|gettext|cat:"!"}
            {/form}
        </div>
    {else}
        <h2>{$displayname}</h2>
        <div class="logout">
            <a class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE}"
               href="{link action=logout}">{"Log Out"|gettext}</a>
        </div>
    {/if}
</div>





