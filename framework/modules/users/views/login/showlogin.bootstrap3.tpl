{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{messagequeue}
<div class="login default row">
    {if $checkout}
        {$breadcrumb = [
            0 => [
                "title" => "{'Summary'|gettext}",
                "link"  => makeLink(['controller'=>'cart','action'=>'show'])
            ],
            1 => [
                "title" => "{'Sign In'|gettext}",
                "link"  => ""
            ],
            2 => [
                "title" => "{'Shipping/Billing'|gettext}",
                "link"  => ""
            ],
            3 => [
                "title" => "{'Confirmation'|gettext}",
                "link"  => ""
            ],
            4 => [
                "title" => "{'Complete'|gettext}",
                "link"  => ""
            ]
        ]}
        {breadcrumb items=$breadcrumb active=1 style=flat}
    {/if}
    {if $loggedin == false || $smarty.const.PREVIEW_READONLY == 1}
        <div class="login-form one col-sm-6">
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
                {br}
                {control type="buttongroup" wide=true size=large submit="Log In"|gettext}
                {br}
                {icon wide=true size=large controller=users action=reset_password text='Forgot Your Password?'|gettext}
            {/form}
        </div>
        {if $smarty.const.SITE_ALLOW_REGISTRATION || $smarty.const.ECOM}
            <div class="new-user two col-sm-6">
                <h2>{"New"|gettext} {$usertype}</h2>
                <p>
                    {if $smarty.const.ECOM}
                        {if $oicount>0}
                            {"If you are a new customer, select this option to continue with the checkout process."|gettext}{br}{br}
                            {"We will gather billing and shipping information, and you will have the option to create an account so can track your order status."|gettext}{br}{br}
                            {icon button=true wide=true size=large color=green class="shopping-cart" module=cart action=customerSignup text="Continue Checking Out"|gettext}
                        {else}
                            {"If you are a new customer, add an item to your cart to continue with the checkout process."|gettext}{br}{br}
                            {$backlink = makeLink(expHistory::getBack(1))}
                            {icon button=true wide=true size=large class=reply link=$backlink text="Keep Shopping"|gettext}
                        {/if}
                    {else}
                        {"Create a new account here."|gettext}{br}{br}
                        {icon button=true wide=true size=large controller=users action=create text="Create an Account"|gettext}
                    {/if}
                </p>
            </div>
        {/if}
    {else}
        {if !$smarty.const.ECOM}
            <div class="col-sm-12 logout">
                {icon button=true wide=true size=large action=logout text='Logout'|gettext}
            </div>
        {/if}
    {/if}
</div>
