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

if ($user && $user->is_acting_admin == 1) {
	$page = null;
	if (isset($_GET['id'])) {
		$page = $db->selectObject('section_template','id='.intval($_GET['id']));
	}
	if ($page == null) {
		$page->parent = (isset($_GET['parent']) ? intval($_GET['parent']) : 0);
	}
	
	$form = section_template::form($page);
	$form->meta('module','navigationmodule');
	$form->meta('action','save_template');
	
	$template = new template('navigationmodule','_form_editTemplate',$loc);
	$template->assign('form_html',$form->toHTML());
	$template->assign('is_top',($page->parent == 0 ? 1 : 0));
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>