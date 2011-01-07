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

if (!defined('EXPONENT')) exit('');

if (exponent_permissions_checkOnModule('manage','navigationmodule')) {
	exponent_flow_set(SYS_FLOW_PROTECTED, SYS_FLOW_ACTION);
	
	$template = new template('navigationmodule','_manager',$loc);
	
	$template->assign('sections',navigationmodule::levelTemplate(0,0));
	// Templates
	$template->assign('canManageStandalones', navigationmodule::canManageStandalones());
	$template->assign('canManagePagesets', exponent_users_isAdmin());
	$tpls = $db->selectObjects('section_template','parent=0');
	$template->assign('templates',$tpls);
	$template->output();
} else {
    flash('error', SITE_403_HTML);
    expHistory::back();
}

?>
