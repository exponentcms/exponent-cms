<?php

##################################################
#
# Copyright (c) 2004-2005 OIC Group, Inc.
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
# $Id: save_question.php,v 1.1 2005/04/10 23:24:02 filetreefrog Exp $
##################################################

if (!defined('EXPONENT')) exit('');

$question = null;
if (isset($_POST['id'])) {
	$question = $db->selectObject('poll_question','id='.$_POST['id']);
	if ($question) {
		$loc = unserialize($question->location_data);
	}
}

if (exponent_permissions_check('manage_question',$loc)) {
	$question = poll_question::update($_POST,$question);
	$question->location_data = serialize($loc);
	
	if ($db->countObjects('poll_question',"location_data='".$question->location_data."'") == 0) {
		$question->is_active = 1;
	}
	
	if (isset($question->id)) {
		$db->updateObject($question,'poll_question');
	} else {
		$db->insertObject($question,'poll_question');
	}
	
	exponent_flow_redirect();
} else {
	echo SITE_403_HTML;
}

?>