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

// FIXME: Allow non-administrative users to manage certain
// FIXME: parts of the section hierarchy.
if ($user && $user->is_acting_admin == 1) {
	$section = null;
	if (isset($_GET['id'])) {
		// Check to see if an id was passed in get.  If so, something is seriously wrong,
		// because pagesets cannot be editted, only added (they act like section
		// factories and create other sections).
		$section = $db->selectObject('section','id='.intval($_GET['id']));
		$check_id = $section->id;
	} else if (isset($_GET['parent'])) {
		// The isset check is merely a precaution.  This action should
		// ALWAYS be invoked with a parent or id value in the GET.
		$section->parent = intval($_GET['parent']);
		$check_id = $section->parent;
	}
}

if ($check_id != -1 && expPermissions::check('manage',expCore::makeLocation('navigationmodule','',$check_id))) {
	if (!isset($section->id)) {
		// Adding pagesets only works for adding, not editting.
		$form = section::pagesetForm($section);
		$form->meta('module','navigationmodule');
		$form->meta('action','save_pagesetpage');
		// Create a template for the form output, so that the themer can
		// optionally change the form title and caption
		$template = new template('navigationmodule','_form_addPagesetPage');
		// Assign the form's rendered HTML, with the customary name 'form_html'
		$template->assign('form_html',$form->toHTML());
		$template->output();
	} else {
		// User is trying to edit a pageset page.  This is an error.
		// FIXME: Need some sort of Internal Server Error message.
		// FIXME: For now, using SITE_404_HTML.
		echo SITE_404_HTML;
	}
} else {
	// User does not have permission to manage sections.  Throw a 403
	echo SITE_403_HTML;
}

?>