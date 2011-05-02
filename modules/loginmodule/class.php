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

class loginmodule {
	function name() { return exponent_lang_loadKey('modules/loginmodule/class.php','module_name'); }
	function author() { return 'OIC Group, Inc'; } 
	function description() { return exponent_lang_loadKey('modules/loginmodule/class.php','module_description'); }
	
	function hasContent() { return false; }
	function hasSources() { return false; }
	function hasViews()   { return true; }
	
	function supportsWorkflow() { return false; }
	
	function permissions($internal = '') {
		return array();
	}
	
	function deleteIn($loc) {
		// Do nothing, no content
	}
	
	function copyContent($from_loc,$to_loc) {
		// Do nothing, no content
	}

	function show($view,$loc=null,$title='') {
		global $user;
		
		$template = new template('loginmodule',$view,$loc);
		$template->assign('title',$title);
		
		$template->assign('moduletitle',$title);
		
		
		if (exponent_sessions_loggedIn() && $user->username != "anonymous") {
			global $db;
			$template->assign('loggedin',1);
			$template->assign('user',$user);
			// Generate display name as username if the first and last name fields are blank.
			$display_name = $user->firstname . ' ' .$user->lastname;
			if (trim($display_name) == '') {
				$display_name = $user->username;
			}
			$template->assign('displayname',$display_name);
			// Need to check for groups and whatnot
			if ($db->countObjects('groupmembership','member_id='.$user->id.' AND is_admin=1')) {
				$template->assign('is_group_admin',1);
			} else {
				$template->assign('is_group_admin',0);
			}
		} else {
			$template->assign('isecom',in_array('storeController',listActiveControllers()));
			$template->assign('loggedin',0);
		}
		$template->output($view);
	}
	
	function spiderContent($item = null) {
		// Do nothing, no content
		return false;
	}
	
}
?>
