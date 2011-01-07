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

<div class="module users edit">
{if $edit_user->id == ""}
    <h1>Create a New User Account</h1>
{else}
    <h1>Edit User {$edit_user->username}</h1>
{/if}    
{form action=update}
    {if $edit_user->id == "" || $edit_user->id == 0}
	    {if $smarty.const.USER_REGISTRATION_USE_EMAIL == 0}
		    {control type=text name=username label="Username" value=$edit_user->username}
	    {else}
		    {control type=text name=email label="Email Address" value=$edit_user->email}
	    {/if}
	    {control type=password name=pass1 label=Password}
	    {control type=password name=pass2 label="Confirm Password"}
	{else}
	    {control type="hidden" name="id" value=$edit_user->id}
    {/if}
    {if $smarty.const.USER_REGISTRATION_USE_EMAIL == 0}{control type=text name=email label="Email Address" value=$edit_user->email}{/if}
    {control type=text name=firstname label="First Name" value=$edit_user->firstname}
    {control type=text name=lastname label="Last Name" value=$edit_user->lastname}
    {*control type=checkbox name="recv_html" label="I prefer HTML Email" value=1 checked=$edit_user->recv_html*}
    {if exponent_users_isAdmin() == 1}
	    {control type=checkbox name=is_acting_admin value=1 label="Make this user an Administrator?" checked=$edit_user->is_acting_admin}
    {/if}
    
    {foreach from=$extensions item=extension}
        {include file="`$smarty.const.BASE`framework/modules/users/views/extensions/`$extension`.tpl"}
    {/foreach}
    {control type="buttongroup" submit="Submit" cancel="Cancel"}
{/form}
</div>
