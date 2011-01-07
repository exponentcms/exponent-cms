<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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
}

echo $old_parent;

// Update the section from the _POST data.
$section = section::update($_POST,$section);
if ($check_id == -1) {
	$check_id = $section->parent;
}

if (($check_id != -1 && 
	exponent_permissions_check('manage',exponent_core_makeLocation('navigationmodule','',$check_id)) ||
	$user->is_acting_admin == 1
	)) {

	// make sure the SEF name is valid
        global $router;
        if (empty($section->sef_name)) $section->sef_name = $router->encode($section->name);
        if (!section::isValidName($section->sef_name)) expValidator::failAndReturnToForm('You have invalid characters in the SEF Name field.');
        if (section::isDuplicateName($section)) expValidator::failAndReturnToForm('The name specified in the SEF Name field is a duplicate of an existing page.');

	if (isset($section->id)) {
		if ($section->parent != $old_parent) {
			// Old_parent id was different than the new parent id.  Need to decrement the ranks
			// of the old children (after ours), and then add 
			$section = section::changeParent($section,$old_parent,$section->parent);
		}
	
		// Existing section.  Update the database record.
		// The 'id=x' where clause is implicit with an updateObject
		$db->updateObject($section,'section');
	} else {
		// Since this is new, we need to increment ranks, in case the user
		// added it in the middle of the level.
		$db->increment('section','rank',1,'rank >= ' . $section->rank . ' AND parent=' . $section->parent);
		// New section.  Insert a new database record.
		$section->id = $db->insertObject($section,'section');
	}
	
	exponent_sessions_clearAllUsersSessionCache('navigationmodule');
	navigationmodule::checkForSectionalAdmins($section->id);
	// Go back to where we came from.  Probably the navigation manager.
	exponent_flow_redirect();
} else {
	echo SITE_403_HTML;
}

?>
