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

$answer = null;
$question = null;
if (isset($_GET['id'])) {
	$answer = $db->selectObject('poll_answer','id='.intval($_GET['id']));
	if ($answer) {
		$question = $db->selectObject('poll_question','id='.$answer->question_id);
		if ($question) {
			$loc = unserialize($question->location_data);
		}
	}
}

if ($question) {
	if (expPermissions::check('manage_answer',$loc)) {
		$db->delete('poll_answer','id='.$answer->id);
		$db->decrement('poll_answer','rank',1,'question_id='.$question->id.' AND rank > '.$answer->rank);
		expHistory::back();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?> 
