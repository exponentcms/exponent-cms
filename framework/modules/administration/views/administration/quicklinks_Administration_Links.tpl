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
{get_user assign=user}
{if $user->id != '' && $user->id != 0} 
<div class="administrationmodule quicklinks yui-panel">
	<div class="hd">
			{gettext str=$_TR.quicklinks}
	</div>
	<div class="bd">		
	{permissions}
	{if $can_manage_nav == 1}<a class="sitetree" href="{link module=navigationmodule action=manage}">{$_TR.manage_site}</a>{/if}
		{if $permissions.administrate == 1}
		<a class="files" href="{$smarty.const.URL_FULL}modules/filemanagermodule/actions/picker.php">{$_TR.manage_files}</a>
		<a class="admin" href="{link module=administrationmodule action=index}">{$_TR.site_administration}</a>
		{*<a id="addmodulelink" class="clicktoaddmodule" href="#">{$_TR.add_module}</a>*}
		<a class="recycle" href="{link module=administrationmodule action=orphanedcontent}">{$_TR.recycle_bin}</a>
	
	{/if}
	{/permissions}
	{chain module=previewmodule view=Default}		
	</div>



	<div class="hd">
			{$user->username}
	</div>
	<div class="bd">
	{permissions}
	<a class="changepassword" href="{link module=loginmodule action=changepass}">{$_TR.change_password}</a>
	<a class="editprofile" href="{link module=loginmodule action=editprofile}">{$_TR.edit_profile}</a>
	<a class="logout" href="{link module=loginmodule action=logout}">{$_TR.logout}</a>
	{/permissions}
	</div>
</div>
{/if}
