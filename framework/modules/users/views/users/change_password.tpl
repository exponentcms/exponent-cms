{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<div class="module users change-password">
		<div class="info-header">
			<div class="related-actions">
				{help text="Get Help with Changing User Passwords" module="change-my-password"}
			</div>
			<h1>Change {if $isuser}your{else}{$u->username}'s{/if} password</h1>
		</div>
    <p>To change your password enter your current password and then enter what you would like your new password to be.</p>
    
    {form action=save_change_password}
        {control type="hidden" name="uid" value=$u->id}
        {if $isuser}
        {control type="password" name="password" label="Current Password"}
        {/if}
        {control type="password" name="new_password1" label="Enter your new password"}
        {control type="password" name="new_password2" label="Confirm your new password"}
        {control type="buttongroup" submit="Change My Password" cancel="Cancel"}
    {/form}
    
</div>
