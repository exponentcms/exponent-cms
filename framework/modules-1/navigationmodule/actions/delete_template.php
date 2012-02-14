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

$page = null;
if (isset($_GET['id'])) {
	$page = $db->selectObject('section_template','id='.intval($_GET['id']));
}

if ($page) {
	function tmp_deleteLevel($parent) {
		global $db;
		$kids = $db->selectObjects('section_template','parent='.$parent);
		foreach ($kids as $kid) {
			tmp_deleteLevel($kid->id);
		}
		$db->delete('section_template','parent='.$parent);
	}

	if ($user && $user->is_acting_admin == 1) {
		$db->delete('section_template','id='.$page->id);
		if ($page->parent != 0) {
			$db->decrement('section_template','rank',1,'parent='.$page->parent.' AND rank >= '.$page->rank);
		}
		expSession::clearAllUsersSessionCache('navigationmodule');
		tmp_deleteLevel($page->id);

		expHistory::back();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>