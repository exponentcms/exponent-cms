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

$question = null;
if (isset($_POST['id'])) {
	$question = $db->selectObject('poll_question','id='.intval($_POST['id']));
	if ($question) {
		$loc = unserialize($question->location_data);
	}
}

if (expPermissions::check('manage_question',$loc)) {
	$question = poll_question::update($_POST,$question);
	$question->location_data = serialize($loc);
	
	if ($db->countObjects('poll_question',"location_data='".$question->location_data."'") == 0) {
		$question->is_active = 1;
	}
	
	if (isset($question->id)) {
		$db->updateObject($question,'poll_question');
	} else {
		$db->insertObject($question,'poll_question');
	}
	
	expHistory::back();
} else {
	echo SITE_403_HTML;
}

?>