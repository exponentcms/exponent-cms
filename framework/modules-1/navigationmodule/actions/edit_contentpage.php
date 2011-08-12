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

//if ($user->is_acting_admin == 1 /*TODO: section admin*/) {
	$section = null;
	if (isset($_GET['id'])) {
		// Check to see if an id was passed in get.  If so, retrieve that section from
		// the database, and perform an edit on it.
		$section = $db->selectObject('section','id='.intval($_GET['id']));
		$check_id = $section->id;
	} elseif (isset($_GET['parent'])) {
		// The isset check is merely a precaution.  This action should
		// ALWAYS be invoked with a parent or id value in the GET.
		$section->parent = $_GET['parent'];
		$check_id = $section->parent;
		//$section->parent = $db->selectObject('section','parent='.intval($_GET['parent']));
	}

	if (exponent_permissions_check('manage',exponent_core_makeLocation('navigationmodule','',$check_id))) {	
		$form = section::form($section);
		$form->meta('module','navigationmodule');
		$form->meta('action','save_contentpage');
		// Create a template for the form output, to allow the themer to optionally
		// change the form titles and captions, and to aide in translation.
		$template = new template('navigationmodule','_form_editContentPage');
		// Assign the concentional 'is_edit' flag to let the view show different text to the
		// use in case of a create and an edit operation.
		$template->assign('is_edit',isset($section->id));
		// Assign the form/s rendered HTML to the template, using the customary
		// name of 'form_html'
		$template->assign('form_html',$form->toHTML());
		$template->output();
	} else {
		// User does not have permission to manage sections.  Throw a 403
		echo SITE_403_HTML;
	}
//}
?>
