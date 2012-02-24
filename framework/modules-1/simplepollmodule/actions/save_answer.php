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
$answer = null;
if (isset($_POST['id'])) {
	$answer = $db->selectObject('poll_answer','id='.$_POST['id']);
	if ($answer) {
		$question = $db->selectObject('poll_question','id='.$answer->question_id);
		if ($question) {
			$loc = unserialize($answer->location_data);
		}
	}
} else if (isset($_POST['question_id'])) {
	$question = $db->selectObject('poll_question','id='.$_POST['question_id']);
	if ($question) {
		$loc = unserialize($answer->location_data);
	}
}

if ($question) {
	if (expPermissions::check('manage_answer',$loc)) {
		$answer = poll_answer::update($_POST,$answer);
		
		if (isset($answer->id)) {
			$db->updateObject($answer,'poll_answer');
		} else {
			$answer->question_id = $question->id;
			$answer->rank = $db->max('poll_answer','rank','question_id','question_id='.$question->id);
			if ($answer->rank == null) {
				$answer->rank = 0;
			} else {
				$answer->rank++;
			}
			$db->insertObject($answer,'poll_answer');
		}
		expHistory::back();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>