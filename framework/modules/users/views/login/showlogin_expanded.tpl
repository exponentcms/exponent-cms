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

<div class="login expanded">
    {if $smarty.const.PREVIEW_READONLY == 1}
        <em>{$logged_in_users}:</em>
        <br/>
    {/if}
    {if $loggedin == true || $smarty.const.PREVIEW_READONLY == 1}
        {'Welcome'|gettext|cat:', %s'|sprintf:$displayname}<br/>
        {if !$user->globalPerm('prevent_profile_change')}
            {*<a class="profile" href="{link controller=users action=edituser id=$user->id}">{'Edit Profile'|gettext}</a>*}
            {icon class="profile" controller=users action=edituser id=$user->id text='Edit Profile'|gettext}
            &#160;|&#160;
        {/if}
        {if $is_group_admin}
            {*<a class="groups" href="{link controller=users action=manage_group_memberships}">{'My Groups'|gettext}</a>*}
            {icon class="groups" controller=users action=manage_group_memberships text='My Groups'|gettext}
            &#160;|&#160;
        {/if}
        {if ((!$smarty.const.USER_NO_PASSWORD_CHANGE || $user->isAdmin()) && !$user->is_ldap)}
            {*<a class="password" href="{link controller=users action=change_password}">{'Change Password'|gettext}</a>*}
            {icon class="password" controller=users action=change_password text='Change Password'|gettext}
        {/if}
        &#160;|&#160;
        {*<a class="logout" href="{link action=logout}">{'Logout'|gettext}</a>*}
        {icon button=true action=logout text='Logout'|gettext}
        {if $smarty.const.ECOM && $oicount}
            &#160;|&#160;{icon class=cart controller=cart action=show text="Shopping Cart"|gettext} ({$oicount} {'item'|plural:$oicount})
        {/if}
        {br}
    {/if}
    {if $smarty.const.PREVIEW_READONLY == 1}
        <hr size="1"/>
        <em>{'Anonymous visitors see this'|gettext}:</em>
        {*{br}*}
    {/if}
    {if $loggedin == false || $smarty.const.PREVIEW_READONLY == 1}
        {form action=login}
            {control type="text" name="username" label='' placeholder='Username'|gettext size=15 required=1 prepend="user"}
            {control type="password" name="password" label='' placeholder='Password'|gettext size=15 required=1 prepend="key"}
            {control type="buttongroup" submit="Log In"|gettext}
            {br}
            {if $smarty.const.SITE_ALLOW_REGISTRATION == 1}
                {*<a href="{link controller=users action=create}">{'Create Account'|gettext}</a>*}
                {icon controller=users action=create text='Create Account'|gettext}
                &#160;|&#160;
            {/if}
            {*<a href="{link controller=users action=reset_password}">{'Forgot Your Password?'|gettext}</a>*}
            {icon controller=users action=reset_password text='Forgot Your Password?'|gettext}
        {/form}
    {/if}
</div>
