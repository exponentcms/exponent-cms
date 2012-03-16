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
	$question = $db->selectObject('poll_question','id='.intval($_GET['id']));
}

if ($question) {
	$loc = unserialize($question->location_data);
	if (expPermissions::check('manage_question',$loc)) {
		$db->delete('poll_answer','question_id='.$question->id);
		$db->delete('poll_question','id='.$question->id);
		expHistory::back();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?> 
