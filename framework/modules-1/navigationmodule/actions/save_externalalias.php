<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

// Update the section from the _POST data.
$section = section::updateExternalAlias($_POST,$section);

if ($check_id != -1 && expPermissions::check('manage',expCore::makeLocation('navigationmodule','',$check_id))) {
	if (isset($section->id)) {
		if ($section->parent != $old_parent) {
			// Old_parent id was different than the new parent id.  Need to decrement the ranks
			// of the old children (after ours), and then add 
			$section = section::changeParent($section,$old_parent,$section->parent);
		}
	
		// Existing section.  Update the database record.
		// The 'id=x' where clause is implicit with an updateObject
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