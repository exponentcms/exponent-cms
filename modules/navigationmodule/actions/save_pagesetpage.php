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

$section = section::updatePageset($_POST,null);
// make sure the SEF name is valid
global $router;
if (empty($section->sef_name)) $section->sef_name = $router->encode($section->name);
if (!section::isValidName($section->sef_name)) expValidator::failAndReturnToForm('You have invalid characters in the SEF Name field.');
if (section::isDuplicateName($section)) expValidator::failAndReturnToForm('The name specified in the SEF Name field is a duplicate of an existing page.');

if (exponent_permissions_check('manage',exponent_core_makeLocation('navigationmodule','',$section->parent))) {
	// Still have to do some pageset processing, mostly handled by a handy
	// member method of the navigationmodule class.
	
	// Since this is new, we need to increment ranks, in case the user
	// added it in the middle of the level.
	$db->increment('section','rank',1,'rank >= ' . $section->rank . ' AND parent=' . $section->parent);
	
	// New section (Pagesets always are).  Insert a new database
	// record, and save the ID for the processing methods that need them.
	$section->id = $db->insertObject($section,'section');
	// Process the pageset, to add sections and subsections, as well as default content
	// that the pageset writer added to each element of the set.
	
	exponent_sessions_clearAllUsersSessionCache('navigationmodule');
			
	navigationmodule::process_section($section,$_POST['pageset']);
	
	// Go back to where we came from.  Probably the navigation manager.
	exponent_flow_redirect();
} else {
	echo SITE_403_HTML;
}

?>
