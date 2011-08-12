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

// if unchanged, $check_id will return the SITE_403_HTML message.
// Prevents internal alias to standalone page [do we really need to do this?]
// and provides failsafe if args for 'id' or 'parent' are missing -tz
$check_id = -1;

// Bail in case someone has visited us directly, or the Exponent framework is
// otherwise not initialized.
if (!defined('EXPONENT')) exit('');

$section = null;
if (isset($_GET['id'])) {
	// Check to see if an id was passed in get.  If so, retrieve that section from
	// the database, and perform an edit on it.
	$section = $db->selectObject('section','id='.intval($_GET['id']));
    $check_id = $section->parent;
} else if (isset($_GET['parent'])) {
	// The isset check is merely a precaution.  This action should
	// ALWAYS be invoked with a parent or id value in the GET.
	$section->parent = intval($_GET['parent']);
    $check_id = $section->parent;
}

// FIXME: Allow non-administrative users to manage certain
// FIXME: parts of the section hierarchy.
	
if ($check_id != -1 && $user && 
	($user->is_acting_admin == 1 ||
    exponent_permissions_check('manage',exponent_core_makeLocation('navigationmodule','',$check_id)))) {
	$form = section::internalAliasForm($section);
	$form->meta('module','navigationmodule');
	$form->meta('action','save_internalalias');
	// Create a template for the form's output, to allow the themer to optionally
	// change the form's title and caption.  This will also help with translation.
	$template = new template('navigationmodule','_form_editInternalAlias');
	// Assign the customary 'is_edit' flag with the template, to allow the view to
	// display different text to the user when they are editing an alias and when they
	// are creating a new alias.
	$template->assign('is_edit',isset($section->id));
	// Assign the form's rendered HTML output to the template, using the
	// conventional name of 'form_html'
	$template->assign('form_html',$form->toHTML());
	$template->output();
} else {
	// User is not authorized to manage sections.  Throw a 403
	echo SITE_403_HTML;
}

?>
