{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
 * Written and Designed by James Hunt
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
    {if $moduletitle}<h2>{$moduletitle}</h2>{/if}
    {if $user->id != '' && $user->id != 0}
        <strong class="attribution">Welcome {attribution user=$user display=firstlast}</strong>
    {/if}
    
    <ul>
        <li><a class="viewcart" href="{link controller=cart action=show}" rel="nofollow">View My Cart</a></li>
    
    {if $itemcount > 0}
        <li>
            <a class="checkoutnow" href="{securelink controller=cart action=checkout}" rel="nofollow">Checkout now</a>
        </li>
    {/if}

    {if $user->id != '' && $user->id != 0}
        <li><a class="addressbook" href="{link module=address action=myaddressbook}">Manage My Address Book</a></li>
        <li><a class="vieworders" href="{link module=order action=ordersbyuser}">View my Orders</a></li>
        <li><a class="password" href="{link controller=users action=change_password}">Change My Password</a></li>
        <li><a class="logout" href="{link module=loginmodule action=logout}">Log Out</a></li>
    {else}
        <li><a class="login" href="{link module=loginmodule action=loginredirect}" rel="nofollow">Login</a></li>
    {/if}
    </ul>
</div>


