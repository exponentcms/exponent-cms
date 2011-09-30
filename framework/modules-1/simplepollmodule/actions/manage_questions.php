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
# $Id: manage_questions.php,v 1.1 2005/04/10 23:24:02 filetreefrog Exp $
##################################################

if (!defined('EXPONENT')) exit('');

if (expPermissions::check('manage_question',$loc) || expPermissions::check('manage_answer',$loc)) {
	expHistory::flowSet(SYS_FLOW_PROTECTED,SYS_FLOW_ACTION);
	
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