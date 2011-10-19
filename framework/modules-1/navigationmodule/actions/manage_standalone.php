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
global $router;

if ($user && $user->is_acting_admin == 1) {
//	expHistory::flowSet(SYS_FLOW_PROTECTED, SYS_FLOW_ACTION);
	expHistory::set('manageable', $router->params);

	$template = new template('navigationmodule','_manager_standalone',$loc);
	
	$sections = $db->selectObjects('section','parent=-1');
	$template->assign('sections',$sections);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>