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
# $Id: manage_question.php,v 1.2 2005/04/26 03:06:00 filetreefrog Exp $
##################################################
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

$question = null;
if (isset($_GET['id'])) {
	$question = $db->selectObject('poll_question','id='.$_GET['id']);
	if ($question) {
		$loc = unserialize($question->location_data);
	}
}

if ($question) {
	if (exponent_permissions_check('manage_question',$loc) || exponent_permissions_check('manage_answer',$loc)) {
		exponent_flow_set(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);
	
		$answers = $db->selectObjects('poll_answer','question_id='.$question->id);
		if (!defined('SYS_SORTING')) require_once(BASE.'subsystems/sorting.php');
		uasort($answers,'exponent_sorting_byRankAscending');
	
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