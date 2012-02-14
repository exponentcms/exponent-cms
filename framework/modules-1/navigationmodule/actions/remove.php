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

//if ($user->is_acting_admin == 1) {
if (expPermissions::check('manage',expCore::makeLocation('navigationmodule','',intval($_GET['id'])))) {
	$section = $db->selectObject('section','id='.intval($_GET['id']));
	if ($section) {
		navigationmodule::removeLevel($section->id);
		$db->decrement('section','rank',1,'rank > ' . $section->rank . ' AND parent='.$section->parent);
		$section->parent = -1;
		$db->updateObject($section,'section');

		expSession::clearAllUsersSessionCache('navigationmodule');
			
		expHistory::back();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>
