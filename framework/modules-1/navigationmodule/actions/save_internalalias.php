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

// Bail in case someone has visited us directly, or the Exponent framework is
// otherwise not initialized.
if (!defined('EXPONENT')) exit('');

$check_id = -1;
$section = null;
$old_parent = null;
if (isset($_POST['id'])) {
	// Saving an existing content page.  Read it from the database.
	$section = $db->selectObject('section','id='.intval($_POST['id']));
	if ($section) {
		$old_parent = $section->parent;
		$check_id = $section->id;
	}
} else {
	$check_id = $_POST['parent'];
}

if ($check_id != -1 && exponent_permissions_check('manage',exponent_core_makeLocation('navigationmodule','',$check_id))) {
	// Update the section from the _POST data.
	$section = section::updateInternalAlias($_POST,$section);
	if ($section->active == 0) {
		// User tried to link to an inactive section.  This makes little or no sense in
		// this context, so throw them back to the edit form, with an error message.
		$_POST['_formError'] = gt('You cannot link to an inactive section.  Inactive sections are shown with "(" and ")" around their names in the selection list.');
		expSession::set('last_POST',$_POST);
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit('');
	}
	
	if (isset($section->id)) {
		if ($section->parent != $old_parent) {
			// Old_parent id was different than the new parent id.  Need to decrement the ranks
			// of the old children (after ours), and then add 
			$section = section::changeParent($section,$old_parent,$section->parent);
		}
	
		// Existing section.  Update the database record.
		// The 'id=x' WHERE clause is implicit with an updateObject
		expSession::clearAllUsersSessionCache('navigationmodule');
			
		$db->updateObject($section,'section');
	} else {
		// Since this is new, we need to increment ranks, in case the user
		// added it in the middle of the level.
		$db->increment('section','rank',1,'rank >= ' . $section->rank . ' AND parent=' . $section->parent);
		// New section.  Insert a new database record.
		
		expSession::clearAllUsersSessionCache('navigationmodule');
			
		$db->insertObject($section,'section');
	}
	
	// Go back to where we came from.  Probably the navigation manager.
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>