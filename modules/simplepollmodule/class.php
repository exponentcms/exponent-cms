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
# $Id: class.php,v 1.2 2005/04/26 03:06:00 filetreefrog Exp $
##################################################

class simplepollmodule {
	function name() { return 'Simple Poll'; }
	function description() { return 'A simple poll that asks a visitor one question that has many answers.  Can manage multiple questions, although it only displays one.'; }
	function author() { return 'OIC Group, Inc.'; }
	
	function hasSources() { return true; }
	function hasContent() { return true; }
	function hasViews() { return true; }
	
	function supportsWorkflow() { return false; }
	
	function permissions($internal = '') {
		return array(
			'administrate'=>'Administrate',
			'configure'=>'Configure',
			'manage_question'=>'Manage Questions',
			'manage_answer'=>'Manage Answers'
		);
	}
	
	function show($view,$loc = null, $title = '') {
		global $db;
		$question = $db->selectObject('poll_question',"is_active = 1 AND location_data='".serialize($loc)."'");
		$answers = array();
		if ($question) {
			$answers = $db->selectObjects('poll_answer','question_id='.$question->id);
		}
		if (!defined('SYS_SORTING')) require_once(BASE.'subsystems/sorting.php');
		usort($answers,'exponent_sorting_byRankAscending');
		
		$template = new template('simplepollmodule',$view,$loc);
        $template->assign('moduletitle',$title);
		$template->assign('question',$question);
		$template->assign('answers',$answers);
		$template->assign('have_answers',count($answers));
		
		$template->register_permissions(
			array('administrate','configure','manage_question','manage_answer'),
			$loc);
		
		$template->output();
	}
	
	function deleteIn($loc) {
		global $db;
		foreach ($db->selectObjects('poll_question',"location_data='".serialize($loc)."'") as $question) {
			$db->delete('poll_answer','question_id='.$question->id);
		}
		$db->delete('poll_question',"location_data='".serialize($loc)."'");
	}
	
	function copyContent($from_loc,$to_loc) {
		global $db;
		$to_loc_ser = serialize($to_loc);
		foreach ($db->selectObjects('poll_question',"location_data='".serialize($from_loc)."'") as $question) {
			$old_id = $question->id;
			unset($question->id);
			$question->location_data = $to_loc_ser;
			$question->id = $db->insertObject($question,'poll_question');
			foreach ($db->selectObjects('poll_answer','question_id='.$old_id) as $answer) {
				unset($answer->id);
				$answer->question_id = $question->id;
				$db->insertObject($answer,'poll_answer');
			}
		}
	}
	
	function spiderContent($item = null) {
		// Do nothing, no searchable content
		return false;
	}
}

?>