<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# Exponent is distributed in the hope that it
# will be useful, but WITHOUT ANY WARRANTY;
# without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR
# PURPOSE.  See the GNU General Public License
# for more details.
#
# You should have received a copy of the GNU
# General Public License along with Exponent; if
# not, write to:
#
# Free Software Foundation, Inc.,
# 59 Temple Place,
# Suite 330,
# Boston, MA 02111-1307  USA
#
# $Id: save_answer.php,v 1.1 2005/04/10 23:24:02 filetreefrog Exp $
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
	if (exponent_permissions_check('manage_answer',$loc)) {
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