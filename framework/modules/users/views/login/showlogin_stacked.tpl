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

{css unique="showlogin" link="`$asset_path`css/login.css" corecss="button"}

{/css}

<div class="module login stacked">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h2>{$moduletitle}</h2>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {if $loggedin == false}
        <div>
            {form action=login}
                {control type="text" name="username" label="Username"|gettext|cat:":" size=25 required=1 prepend="user"}
                {control type="password" name="password" label="Password"|gettext|cat:":" size=25 required=1 prepend="key"}
                {control type="buttongroup" submit="Log In"|gettext|cat:"!"}
                {br}{icon controller=users action=reset_password text='Forgot Your Password?'|gettext}
            {/form}
        </div>
    {else}
        <h2>{$displayname}</h2>
        <div class="logout">
            {*<a class="{button_style}" href="{link action=logout}">{"Log Out"|gettext}</a>*}
            {icon button=true action=logout text="Log Out"|gettext}
        </div>
    {/if}
</div>





