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

if (!defined('EXPONENT')) exit('');

if ($user && ($user->is_admin == 1 || $user->is_acting_admin == 1)) {
	$sect = null;
	$sect->parent = intval($_GET['parent']);
	$form = section::moveStandaloneForm($sect);
	expSession::clearAllUsersSessionCache('navigationmodule');
	$form->meta('action','reparent_standalone');
	$form->meta('module','navigationmodule');
	$template = new template('navigationmodule','_move_standalone');
	$template->assign('form_html',$form->toHTML());
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>