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

{css unique="showlogin" link="`$asset_path`css/login.css" corecss="button"}

{/css}

{css unique="showlogin-flyout" link="`$asset_path`css/flyout.css"}

{/css}

<div class="module login flyout" style="display: none;">
    {if $loggedin == false || $smarty.const.PREVIEW_READONLY == 1}
    <div{if $smarty.const.SITE_ALLOW_REGISTRATION || $smarty.const.ECOM} class="box login-form one"{/if}>
        {if $smarty.const.USER_REGISTRATION_USE_EMAIL || $smarty.const.ECOM}
            {$usertype="Customers"|gettext}
            {$label="Email Address"|gettext|cat:":"}
        {else}
            {$usertype="Users"|gettext}
            {$label="Username"|gettext|cat:":"}
        {/if}
        <h2>{"Existing"|gettext} {$usertype}</h2>
        <!--p>If you are an existing customer please log-in below to continue in the checkout process.</p-->
        {form action=login}
            {control type="text" name="username" label=$label size=25 required=1 prepend="user" focus=1}
            {control type="password" name="password" label="Password"|gettext|cat:":" size=25 required=1 prepend="key"}
            {control type="buttongroup" submit="Log In"|gettext}
            {br}
            {icon controller=users action=reset_password text='Forgot Your Password?'|gettext}
        {/form}
        {*{br}<a href="{link controller=users action=reset_password}">{'Forgot Your Password?'|gettext}</a>*}
    </div>
    {if $smarty.const.SITE_ALLOW_REGISTRATION || $smarty.const.ECOM}
        <div class="box new-user two">
            <h2>{"New"|gettext} {$usertype}</h2>
            <p>
                {if $smarty.const.ECOM}
                    {if $oicount>0}
                        {"If you are a new customer, select this option  <br />to continue with the checkout process."|gettext}{br}{br}
                        {"We will gather billing and shipping information,  <br />and you will have the option to create an account  <br />so can track your order status."|gettext}{br}{br}
                        {*<a class="{button_style}" href="{link module=cart action=customerSignup}">{"Continue Checking Out"|gettext}</a>*}
                        {icon button=true module=cart action=customerSignup text="Continue Checking Out"|gettext}
                    {else}
                        {"If you are a new customer, add an item to your cart  <br />to continue with the checkout process."|gettext}
                    {/if}
                {else}
                    {"Create a new account here."|gettext}{br}{br}
                    {*<a class="{button_style}" href="{link controller=users action=create}">{"Create an Account"|gettext}</a>*}
                    {icon button=true controller=users action=create text="Create an Account"|gettext}
                {/if}
            </p>
        </div>
    {/if}
</div>
<a class="triggerlogin" href="#" title="{'Click to open this panel'|gettext}">{'Login'|gettext}</a>
{else}
    <div>
        <strong>{'Welcome'|gettext} {$displayname}</strong>{br}{br}
        {if !$user->globalPerm('prevent_profile_change')}
            {*<a class="profile" href="{link controller=users action=edituser id=$user->id}">{'Edit Profile'|gettext}</a>{br}*}
            {icon class="profile" controller=users action=edituser id=$user->id text='Edit Profile'|gettext}
        {/if}
        {if $is_group_admin}
            {*<a class="groups" href="{link controller=users action=manage_group_memberships}">{'My Groups'|gettext}</a>{br}*}
            {icon class="groups" controller=users action=manage_group_memberships text='My Groups'|gettext}
        {/if}
        {if ((!$smarty.const.USER_NO_PASSWORD_CHANGE || $user->isAdmin()) && !$user->is_ldap)}
            {*<a class="password" href="{link controller=users action=change_password}">{'Change Password'|gettext}</a>{br}*}
            {icon class="password" controller=users action=change_password text='Change Password'|gettext}
        {/if}
        <a class="logout" href="{link action=logout}">{'Logout'|gettext}</a>{br}
        {if $smarty.const.ECOM && $oicount}
            {icon class='cart' controller=cart action=show text="Shopping Cart"|gettext} ({$oicount} {'item'|plural:$oicount}){br}
        {/if}
        {if $user->isAdmin()}
            {*<a class="{$previewclass}" href="{link controller=administration action=toggle_preview}">{$previewtext}</a>{br}*}
            {icon class=$previewclass controller=administration action=toggle_preview text=$previewtext}
        {/if}
    </div>
</div>
<a class="triggerlogin" href="#" title="{'Click to open this panel'|gettext}">&#160;&#160;&#160;{$displayname}</a>
{/if}

{script unique="flyout" jquery=1}
{literal}
    $(document).ready(function(){
        $(".triggerlogin").click(function(){
            $(".flyout").toggle("fast");
            $(this).toggleClass("active");
            if ($(this).hasClass('active'))  {
                $(this).attr('title','{/literal}{'Click to close this panel'|gettext}{literal}');
            } else {
                $(this).attr('title','{/literal}{'Click to open this panel'|gettext}{literal}');
            }
            return false;
        });
    });
{/literal}
{/script}