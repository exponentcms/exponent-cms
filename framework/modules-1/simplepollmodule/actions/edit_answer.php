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
if (isset($_GET['id'])) {
	$answer = $db->selectObject('poll_answer','id='.intval($_GET['id']));
	if ($answer) {
		$question = $db->selectObject('poll_question','id='.$answer->question_id);
	}
} else if (isset($_GET['question_id'])) {
	$question = $db->selectObject('poll_question','id='.intval($_GET['question_id']));
}

if ($question) {
	$loc = unserialize($question->location_data);
	if (expPermissions::check('manage_answer',$loc)) {
		$form = poll_answer::form($answer);
		$form->location($loc);
		$form->meta('action','save_answer');
		
		if ($answer == null && isset($_GET['question_id'])) {
			$form->meta('question_id',$_GET['question_id']);
		}
		
		$template = new template('simplepollmodule','_editAnswer',$loc);
		$template->assign('form_html',$form->toHTML());
		$template->output();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>