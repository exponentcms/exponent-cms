<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

if (!defined('EXPONENT')) exit('');

$stuff = array(
	gt('User Management')=>array(
		'useraccounts'=>array(
			'title'=>gt('User Accounts'),
			'module'=>'administrationmodule',
			'action'=>'useraccounts',
			'icon'=>ICON_RELATIVE."userperms.png"),
		'usersessions'=>array(
			'title'=>gt('User Sessions'),
			'module'=>'administrationmodule',
			'action'=>'usersessions'),
		'groupaccounts'=>array(
			'title'=>gt('Group Accounts'),
			'module'=>'administrationmodule',
			'action'=>'groupaccounts',
			'icon'=>ICON_RELATIVE."groupperms.png"),
		'profiledefinitions'=>array(
			'title'=>gt('Profile Extensions'),
			'module'=>'userprofilemodule',
			'action'=>'index'),
		'icon'=>ICON_RELATIVE."admin/users.png"
	),
	gt('Extensions')=>array(
		'managemodules'=>array(
			'title'=>gt('Manage Modules'),
			'module'=>'administrationmodule',
			'action'=>'managemodules',
			'icon'=>ICON_RELATIVE."modules.jpg"),
		'managethemes'=>array(
			'title'=>gt('Manage Themes'),
			'module'=>'administrationmodule',
			'action'=>'managethemes'),
		'tags'=>array(
			'title'=>gt('Manage Tags'),
			'module'=>'expTag',
			'action'=>'manage'),
			//'module'=>'expTagCollection',
			//'action'=>'showall'),
		'wizards'=>array(
			'title'=>gt('Manage Wizards'),
			'module'=>'wizardmodule',
			'action'=>'manage_wizards'),
//		'managesubsystems'=>array(
//			'title'=>gt('Subsystems'),
//			'module'=>'administrationmodule',
//			'action'=>'managesubsystems'),
		'upload_extension'=>array(
			'title'=>gt('Upload New Extension'),
			'module'=>'administrationmodule',
			'action'=>'upload_extension'),
			'icon'=>ICON_RELATIVE."admin/extensions.png",
		'manage_bots'=>array(
		 	'title'=>"Manage Bots",
		 	'module'=>'bots',
		 	'action'=>'manage_bots'),
		   
	),
	gt('Database')=>array(
		'orphanedcontent'=>array(
			'title'=>gt('Archived Modules'),
			'module'=>'administrationmodule',
			'action'=>'orphanedcontent'),
		'installdatabase'=>array(
			'title'=>gt('Install Tables'),
			'module'=>'administrationmodule',
			'action'=>'installtables'),
		'trimdatabase'=>array(
			'title'=>gt('Trim Database'),
			'module'=>'administrationmodule',
			'action'=>'trimdatabase'),
		'optimizedatabase'=>array(
			'title'=>gt('Optimize Database'),
			'module'=>'administrationmodule',
			'action'=>'optimizedatabase'),
		'import'=>array(
			'title'=>gt('Import Data'),
			'module'=>'importer',
			'action'=>'list_importers'),
		'export'=>array(
			'title'=>gt('Export Data'),
			'module'=>'exporter',
			'action'=>'list_exporters'),
		'icon'=>ICON_RELATIVE."admin/database.png",
	),
	gt('Configuration')=>array(
		'configuresite'=>array(
			'title'=>gt('Configure Site'),
			'module'=>'administrationmodule',
			'action'=>'configuresite',
			'icon'=>ICON_RELATIVE."configure.png"),
		'mimetypes'=>array(
			'title'=>gt('File Types'),
			'module'=>'filemanager',
			'action'=>'admin_mimetypes',
			'icon'=>ICON_RELATIVE."filetypes.png"),
		'manage_policies'=>array(
			'title'=>gt('Workflow Policies'),
			'module'=>'workflow',
			'action'=>'admin_manage_policies',
			'icon'=>ICON_RELATIVE."workflow.png"),
		'sysinfo'=>array(
			'title'=>gt('System Info'),
			'module'=>'administrationmodule',
			'action'=>'sysinfo',
			'icon'=>ICON_RELATIVE."system-info.png"),
		'icon'=>ICON_RELATIVE."admin/config.png",
	),
	gt('Development')=>array(
		'toggledev'=>array(
				'title'=>(DEVELOPMENT == 0) ? gt('Turn Error Reporting On') : gt('Turn Error Reporting Off'),
				'module'=>'administrationmodule',
				'action'=>'toggle_dev',
				'icon'=>ICON_RELATIVE."filetypes.png"),
		'rebuildcss'=>array(
				'title'=>gt('Rebuild CSS File'),
				'module'=>'administrationmodule',
				'action'=>'remove_css',
				'icon'=>ICON_RELATIVE."configure.png"),
		'clearsmarty'=>array(
				'title'=>gt('Clear Smarty Cache'),
				'module'=>'administrationmodule',
				'action'=>'clear_smarty_cache',
				'icon'=>ICON_RELATIVE."filetypes.png"),
		'maintmode'=>array(
				'title'=>(MAINTENANCE_MODE == 0) ? gt('Turn Maintenance Mode On') : gt('Turn Maintenance Mode Off'),
				'module'=>'administrationmodule',
				'action'=>'toggle_maintenance',
				'icon'=>ICON_RELATIVE."filetypes.png"),
		'icon'=>ICON_RELATIVE."admin/developer.png",
	),
);
global $user;
if (!isset($user) || (isset($user->is_admin) && $user->is_admin == 0) ) {
 unset($stuff[gt('Database')]['import']);
}
return $stuff;

?>
