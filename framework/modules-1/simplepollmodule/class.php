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
/** @define "BASE" "../.." */

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
			'manage'=>'Manage',
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
			$answers = $db->selectObjects('poll_answer','question_id='.$question->id,'rank');
		}
//		$answers = expSorter::sort(array('array'=>$answers,'sortby'=>'rank', 'order'=>'ASC'));

		$template = new template('simplepollmodule',$view,$loc);
        $template->assign('moduletitle',$title);
		$template->assign('question',$question);
		$template->assign('answers',$answers);
		$template->assign('have_answers',count($answers));

        $config = $db->selectObject('simplepollmodule_config',"location_data='".$question->location_data."'");
        $template->assign('config',$config);

		$template->register_permissions(
			array('manage','configure','manage_question','manage_answer'),
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
	
	static function spiderContent($item = null) {
		// Do nothing, no searchable content
		return false;
	}
}

?>