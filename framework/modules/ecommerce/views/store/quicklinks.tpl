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
 
{css unique="store-quicklinks" link="`$asset_path`css/cart.css"}

{/css}

<div class="module store quick-links">
    <h2>{$moduletitle|default:"Store Links"|gettext}</h2>
    {if $user->id != '' && $user->id != 0}
        <strong class="attribution">Welcome {attribution user=$user display=firstlast}</strong>
    {/if}
    <ul>
        <li><a class="viewcart" href="{link controller=cart action=show}" rel="nofollow">{'View My Cart'|gettext}</a></li>
        {if $itemcount > 0}
            <li>
                <a class="checkoutnow" href="{securelink controller=cart action=checkout}" rel="nofollow">{'Checkout Now'|gettext}</a>
            </li>
        {/if}
        {if $user->id != '' && $user->id != 0}
            <li><a class="profile" href="{link module=users action=viewuser}">{'View My Account'|gettext}</a></li>
            <li><a class="addressbook" href="{link module=address action=myaddressbook}">{'Manage My Addresses'|gettext}</a></li>
            <li><a class="vieworders" href="{link module=order action=ordersbyuser}">{'View My Orders'|gettext}</a></li>
            <li><a class="password" href="{link controller=users action=change_password}">{'Change My Password'|gettext}</a></li>
            <li><a class="logout" href="{link controller=login action=logout}">{'Log Out'|gettext}</a></li>
        {else}
            <li><a class="login" href="{link controller=login action=loginredirect}" rel="nofollow">{'Login'|gettext}</a></li>
        {/if}
    </ul>
</div>
