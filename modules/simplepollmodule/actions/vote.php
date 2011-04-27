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
# $Id: vote.php,v 1.1 2005/04/10 23:24:02 filetreefrog Exp $
##################################################

if (!defined('EXPONENT')) exit('');

$answer = null;
$question = null;
if (isset($_POST['choice'])) {
	$answer = $db->selectObject('poll_answer','id='.$_POST['choice']);
	if ($answer) {
		$question = $db->selectObject('poll_question','id='.$answer->question_id);
	}
}

if ($answer && $question) {
	$config = $db->selectObject('simplepollmodule_config',"location_data='".$question->location_data."'");
	if (!$config) {
		$config->anonymous_timeout = 5*3600;
		$config->thank_you_message = 'Thank you for voting.';
		$config->already_voted_message = 'You have already voted in this poll.';
		$config->voting_closed_message = 'Voting has been closed for this poll.';
	}
	
	// Check to see if voting is even allowed:
	if ($question->open_voting == 1) {
	
		// Time blocking
		$timeblock = null;
		if ($user) {
			$timeblock = $db->selectObject('poll_timeblock','user_id='.$user->id.' AND question_id='.$answer->question_id);
		} else {
			$timeblock = $db->selectObject('poll_timeblock',"ip_hash='".md5($_SERVER['REMOTE_ADDR'])."' AND question_id=".$answer->question_id);
		}
		
		if ($timeblock == null || $timeblock->lock_expires < time() && $timeblock->lock_expires != 0) {	
			$answer->vote_count++;
			$db->updateObject($answer,'poll_answer');
			
			// Update the timeblock
			$timeblock->question_id = $answer->question_id;
			if ($user) {
				$timeblock->lock_expires = 0;
				$timeblock->user_id = $user->id;
				$timeblock->ip_hash = '';
			} else {
				$timeblock->lock_expires = time()+$config->anonymous_timeout;
				$timeblock->user_id = 0;
				$timeblock->ip_hash = md5($_SERVER['REMOTE_ADDR']);
			}
			
			if (isset($timeblock->id)) {
				$db->updateObject($timeblock,'poll_timeblock');
			} else {
				$db->insertObject($timeblock,'poll_timeblock');
			}
			
			echo nl2br($config->thank_you_message);
			
			if ($question->open_results) {
				$_GET['id'] = $question->id;
				include(BASE.'modules/simplepollmodule/actions/results.php');
			}
		} else {
			echo nl2br($config->already_voted_message);
		}
	} else {
		echo nl2br($config->voting_closed_message);
	}
} else {
	echo SITE_404_HTML;
}

?>