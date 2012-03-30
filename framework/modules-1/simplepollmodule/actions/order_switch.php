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
if (isset($_GET['qid'])) {
	$question = $db->selectObject('poll_question','id='.intval($_GET['qid']));
	if ($question) {
		$loc = unserialize($question->location_data);
	}
}

if ($question) {
	if (expPermissions::check('manage_answer',$loc)) {
		$db->switchValues('poll_answer','rank',$_GET['a'],$_GET['b'],'question_id='.$question->id);
		expHistory::back();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>