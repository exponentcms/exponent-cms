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
if (isset($_GET['id'])) {
	$question = $db->selectObject('poll_question','id='.$_GET['id']);
}

if ($question) {
	$loc = unserialize($question->location_data);
	if (expPermissions::check('manage_question',$loc)) {
		$update_obj = null;
		$update_obj->is_active = 0;
		$db->updateObject($update_obj,'poll_question',"location_data='".$question->location_data."'");
		
		//$question->is_active = 1;
		$question->is_active = $_GET['activate'];
		$db->updateObject($question,'poll_question');
		expHistory::back();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?> 
