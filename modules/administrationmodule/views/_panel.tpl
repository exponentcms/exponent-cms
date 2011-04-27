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

{if $permissions.administrate == 1}
	<div id="expadminpanel" class="administrationmodule panel">
		<div class="hd">
			Administration Panel 
		</div>
		<div class="bd" style="text-align:left;">
				{permissions}
					<a class="sitetree" href="{link module=navigationmodule action=manage}">Manage Site Navigation</a>
					<a class="files" href="{$smarty.const.URL_FULL}modules/filemanagermodule/actions/picker.php">Manage Files</a>
					<a class="admin" href="{link module=administrationmodule action=index}">Site Administration</a>
					<a id="addmodulelink" class="clicktoaddmodule" href="#">Add Module</a>
					<a class="recycle" href="{link module=administrationmodule action=orphanedcontent}">Recycle Bin</a>
				{/permissions}

			{get_user assign=user}
			{if $user->id != '' && $user->id != 0} 
				{permissions}
				<a class="changepassword" href="{link module=loginmodule action=changepass}">Change Password</a>
				<a class="editprofile" href="{link module=loginmodule action=editprofile}">Edit Profile</a>
				<a class="logout" href="{link module=loginmodule action=logout}">Log Out</a>
				{/permissions}
				{chain module=previewmodule view=Default}
			{/if}
		</div>
	</div>
	
{script yuimodules="'container','animation'" unique="adminpanelpop"}
{literal}
	
	function adminpanelinit(){
		eXp.adminpanel = new YAHOO.widget.Panel("expadminpanel", { width:"200px", visible:false, xy:[10,10], close:false} );
		eXp.adminpanel.render();
		eXp.adminpanel.show();

		var addmes = YAHOO.util.Dom.getElementsByClassName("addmodule","a");
		var togglelink = YAHOO.util.Dom.get("addmodulelink",true);


		YAHOO.util.Event.on(togglelink,"click",addmoduletoggle);

		function addmoduletoggle() {
			if(YAHOO.util.Dom.getStyle(addmes[0],"display") == "none"){
				YAHOO.util.Dom.setStyle(addmes,"display","block");
			} else {
				YAHOO.util.Dom.setStyle(addmes,"display","none");
			}
		}



	}

	YAHOO.util.Event.onContentReady("expadminpanel", adminpanelinit);
	

{/literal}
{/script}

{/if}
