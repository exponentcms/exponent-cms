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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

$question = null;
if (isset($_GET['id'])) {
	$question = $db->selectObject('poll_question','id='.intval($_GET['id']));
}

if ($question) {
	if ($question->open_results == 1) {
		$total = 0;
		$answers = $db->selectObjects('poll_answer','question_id='.$question->id,'rank');
//		$answers = expSorter::sort(array('array'=>$answers,'sortby'=>'vote_count', 'order'=>'DESC', 'type'=>'a'));
		for ($i = 0; $i < count($answers); $i++) {
			$total += $answers[$i]->vote_count;
		}

		$template = new template('simplepollmodule','_results');
		$template->assign('vote_total',$total);
		$template->assign('question',$question);
		$template->assign('answers',$answers);
		$template->output();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?> 
