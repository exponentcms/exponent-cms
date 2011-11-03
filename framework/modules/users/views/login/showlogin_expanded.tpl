{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Phillip Ball
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

<div class="login expanded">
	{if $smarty.const.PREVIEW_READONLY == 1}
		<i>{$logged_in_users}:</i><br />
	{/if}
	{if $loggedin == true || $smarty.const.PREVIEW_READONLY == 1}
		{'Welcome'|gettext|cat:', %s'|sprintf:$displayname}<br />
		<a href="{link controller=users action=edituser id=$user->id}">{'Edit Profile'|gettext}</a>&nbsp;|&nbsp;
		{if $is_group_admin}
			<a href="{link controller=users action=manage_group_memberships}">{'My Groups'|gettext}</a>&nbsp;|&nbsp;
		{/if}
		<a href="{link controller=users action=change_password}">{'Change Password'|gettext}</a>&nbsp;|&nbsp;
		<a href="{link action=logout}">{'Logout'|gettext}</a><br />
	{/if}
	{if $smarty.const.PREVIEW_READONLY == 1}
		<hr size="1" />
		<i>{'Anonymous visitors see this'|gettext}:</i><br />
	{/if}
	{if $loggedin == false || $smarty.const.PREVIEW_READONLY == 1}
		<form method="post" action="{$smarty.const.URL_FULL}index.php">
			<input type="hidden" name="action" value="login" />
			<input type="hidden" name="controller" value="login" />
			<input type="text" class="text" name="username" id="login_username" size="15" />
			<input type="password" class="text" name="password" id="login_password" size="15" />
			<input type="submit" class="button" value="{'Login'|gettext}" /><br />
			{if $smarty.const.SITE_ALLOW_REGISTRATION == 1}
				<a href="{link controller=users action=create}">{'Create Account'|gettext}</a>&nbsp;|&nbsp;
				<a href="{link controller=users action=reset_password}">{'Retrieve Password'|gettext}</a>
			{/if}
		</form>
	{/if}
</div>
