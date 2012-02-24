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
	if (isset($_POST['id'])) {
		$page = $db->selectObject('section_template','id='.intval($_POST['id']));
	}
	
	$page = section_template::update($_POST,$page);

	if (isset($page->id)) {
		expSession::clearAllUsersSessionCache('navigationmodule');
			
		$db->updateObject($page,'section_template');	
	} else {
		if ($page->parent != 0) {
			// May have to change the section rankings, because the user could have put us in between two previous pages
			$db->increment('section_template','rank',1,'parent='.$page->parent.' AND rank >= ' . $page->rank);
		}
		expSession::clearAllUsersSessionCache('navigationmodule');
			
		$db->insertObject($page,'section_template');
	}
	
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>
