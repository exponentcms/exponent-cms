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

global $router;

if (expPermissions::check('manage_question',$loc) || expPermissions::check('manage_answer',$loc)) {
//	expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);
	expHistory::set('manageable', $router->params);

	$questions = $db->selectObjects('poll_question',"location_data='".serialize($loc)."'");
	for ($i = 0; $i < count($questions); $i++) {
		$questions[$i]->answer_count = $db->countObjects('poll_answer','question_id='.$questions[$i]->id);
	}
	
	$template = new template('simplepollmodule','_manageQuestions',$loc);
	$template->assign('questions',$questions);
	$template->register_permissions(array('manage_question','manage_answer'),$loc);
	$template->output();
} else {
	echo SITE_403_HTML;
}

?>