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

<div class="loginmodule expanded">
{if $smarty.const.PREVIEW_READONLY == 1}
<i>{$logged_in_users}:</i><br />
{/if}
{if $loggedin == true || $smarty.const.PREVIEW_READONLY == 1}
{$_TR.welcome|sprintf:$displayname}<br />
<a href="{link controller=users action=edituser id=$user->id}">{$_TR.edit_profile}</a>&nbsp;|&nbsp;
{if $is_group_admin}
<a href="{link action=mygroups}">{$_TR.my_groups}</a>&nbsp;|&nbsp;
{/if}
<a href="{link controller=users action=change_password}">{$_TR.change_password}</a>&nbsp;|&nbsp;
<a href="{link action=logout}">{$_TR.logout}</a><br />
{/if}
{if $smarty.const.PREVIEW_READONLY == 1}
<hr size="1" />
<i>{$_TR.anon_users}:</i><br />
{/if}
{if $loggedin == false || $smarty.const.PREVIEW_READONLY == 1}
<form method="post" action="{$smarty.const.URL_FULL}index.php">
<input type="hidden" name="action" value="login" />
<input type="hidden" name="module" value="loginmodule" />
<input type="text" class="text" name="username" id="login_username" size="15" />
<input type="password" class="text" name="password" id="login_password" size="15" />
<input type="submit" class="button" value="{$_TR.login}" /><br />
{if $smarty.const.SITE_ALLOW_REGISTRATION == 1}
<a href="{link controller=users action=create}">{$_TR.create_account}</a>&nbsp;|&nbsp;
<a href="{link controller=users action=reset_password}">{$_TR.forgot_password}</a>
{/if}
</form>
{/if}
</div>
