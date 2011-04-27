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
# $Id: results.php,v 1.2 2005/04/26 03:06:00 filetreefrog Exp $
##################################################

if (!defined('EXPONENT')) exit('');

$question = null;
if (isset($_GET['id'])) {
	$question = $db->selectObject('poll_question','id='.$_GET['id']);
}

if ($question) {
	if ($question->open_results == 1) {
		$total = 0;
		$answers = $db->selectObjects('poll_answer','question_id='.$question->id);
		for ($i = 0; $i < count($answers); $i++) {
			$total += $answers[$i]->vote_count;
		}
		
		if (!defined('SYS_SORTING')) require_once(BASE.'subsystems/sorting.php');
		if (!function_exists('exponent_sorting_byVoteCountDescending')) {
			function exponent_sorting_byVoteCountDescending($a,$b) {
				return ($a->vote_count > $b->vote_count ? -1 : 1);
			}
		}
		uasort($answers,'exponent_sorting_byVoteCountDescending');
		
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
