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

global $router;

$question = null;
if (isset($_GET['id'])) {
	$question = $db->selectObject('poll_question','id='.intval($_GET['id']));
	if ($question) {
		$loc = unserialize($question->location_data);
	}
}

if ($question) {
	if (expPermissions::check('manage_question',$loc) || expPermissions::check('manage_answer',$loc)) {
//		expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);
		expHistory::set('manageable', $router->params);

		$answers = $db->selectObjects('poll_answer','question_id='.$question->id);
		$answers = expSorter::sort(array('array'=>$answers,'sortby'=>'rank', 'order'=>'ASC', 'type'=>'a'));

		$template = new template('simplepollmodule','_manageQuestion',$loc);
		$template->assign('question',$question);
		$template->assign('answers',$answers);
		$template->register_permissions(array('manage_question','manage_answer'),$loc);
		$template->output();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>