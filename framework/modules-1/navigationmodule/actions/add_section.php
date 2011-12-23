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

// First, retrieve the parent section from the database.
$parent = null;
if (isset($_GET['parent'])) {
	// Sanitize the parent parameter up here to make things more clear and straightforward.
	$_GET['parent'] = intval($_GET['parent']);
	
	// May have been passed a '0', indicating that we want a top-level section
	if ($_GET['parent'] <= 0) {
		// Set $parent->id to passed value, so that $parent is not null.  The view will use this information
		// to output the appropriate messages to the user.
		$parent->id = $_GET['parent'];
	} else {
		// Passed a non-zero parent id - Adding a subsection.  Try to read
		// the parent from the database.
		$parent = $db->selectObject('section','id='.$_GET['parent']);
	}
}

// Check to see that A) a parent ID was passed in GET, and B) the id was valid
if ($parent) {
	if (expPermissions::check('manage',expCore::makeLocation('navigationmodule','',$parent->id))) {
		// For this action, all we need to do is output a basically
		// non-variable template the asks the user what type of page
		// they want to add to the site Navigation.
		
		$template = new template('navigationmodule','_add_whichtype');
		// We do, however need to know if there are any Pagesets.
		$template->assign('havePagesets',($db->countObjects('section_template','parent=0') && $parent->id >= 0));
		// We also need to know if there are any standalone pages.
		$template->assign('haveStandalone',($db->countObjects('section','parent=-1') && $parent->id >= 0));
		// Assign the parent we were passed, so that it can propagated along to the actual form action.
        $template->assign('parent',$parent);
		$template->assign('isAdministrator',($user && ($user->is_admin || $user->is_acting_admin) ? 1 : 0));
		$template->output();
	} else {
		// Current user is not allowed to manage sections.  Throw a 403.
		echo SITE_403_HTML;
	}
} else {
	// Passed parent id was invalid.  Throw a 404
	echo SITE_404_HTML;
}

?>