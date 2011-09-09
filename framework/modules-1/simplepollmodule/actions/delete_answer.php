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
# $Id: delete_answer.php,v 1.1 2005/04/10 23:24:01 filetreefrog Exp $
##################################################

if (!defined('EXPONENT')) exit('');

$answer = null;
$question = null;
if (isset($_GET['id'])) {
	$answer = $db->selectObject('poll_answer','id='.$_GET['id']);
	if ($answer) {
		$question = $db->selectObject('poll_question','id='.$answer->question_id);
		if ($question) {
			$loc = unserialize($question->location_data);
		}
	}
}

if ($question) {
	if (expPermissions::check('manage_answer',$loc)) {
		$db->delete('poll_answer','id='.$answer->id);
		$db->decrement('poll_answer','rank',1,'question_id='.$question->id.' AND rank > '.$answer->rank);
		expHistory::back();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?> 
