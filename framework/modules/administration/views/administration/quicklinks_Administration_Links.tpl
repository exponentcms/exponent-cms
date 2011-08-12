{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
{get_user assign=user}
{if $user->id != '' && $user->id != 0} 
<div class="administrationmodule quicklinks yui-panel">
	<div class="hd">
		{'Administration Quicklinks'|gettext}
	</div>
	<div class="bd">		
	{permissions}
	{if $can_manage_nav == 1}<a class="sitetree" href="{link module=navigationmodule action=manage}">{'Manage Site Navigation'|gettext}</a>{/if}
		{if $permissions.administrate == 1}
		<a class="files" href="{$smarty.const.URL_FULL}framework/modules-1/filemanagermodule/actions/picker.php">{'Manage Files'|gettext}</a>
		<a class="admin" href="{link module=administrationmodule action=index}">{'Site Administration'|gettext}</a>
		{*<a id="addmodulelink" class="clicktoaddmodule" href="#">{'Add Module'|gettext}</a>*}
		<a class="recycle" href="{link module=administrationmodule action=orphanedcontent}">{'Recycle Bin'|gettext}</a>
	
	{/if}
	{/permissions}
{*	{chain module=previewmodule view=Default}		*}
	</div>



	<div class="hd">
			{$user->username}
	</div>
	<div class="bd">
	{permissions}
	<a class="changepassword" href="{link module=loginmodule action=changepass}">{'Change Password'|gettext}</a>
	<a class="editprofile" href="{link module=loginmodule action=editprofile}">{'Edit Profile'|gettext}</a>
	<a class="logout" href="{link module=loginmodule action=logout}">{'Log Out'|gettext}</a>
	{/permissions}
	</div>
</div>
{/if}
