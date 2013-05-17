<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

/**
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class upgrade_simplepoll
 */
class upgrade_simplepoll extends upgradescript {
	protected $from_version = '0.0.0';
	protected $to_version = '2.1.1';  // simplepollmodule was deprecated in v2.0.9, but fully in v2.1.1
//    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Upgrade the Simple Poll module to a Controller"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "The Simple Poll module was upgraded to a Controller in v2.0.9.  This Script converts Simple Poll modules to the new format and then deletes old simplepollmodule files."; }

    /**
   	 * This routine should perform additional test(s) to see if upgrade script should be run (files/tables exist, etc...)
   	 * @return bool
   	 */
   	function needed() {
        return true;
//        if (expUtil::isReallyWritable(BASE."framework/modules-1/simplepollmodule/")) {
//   		    return true;  // the old files still exist
//        } else return false;
   	}

	/**
	 * converts all simplepollmodule modules/items into simplePoll (controller) modules/items and deletes simplepollmodule files
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		// convert each simplepollmodule reference to a simplePoll Controller reference
	    $srs = $db->selectObjects('sectionref',"module = 'simplepollmodule'");
	    foreach ($srs as $sr) {
		    $sr->module = 'simplePoll';
		    $db->updateObject($sr,'sectionref');
	    }
	    $gps = $db->selectObjects('grouppermission',"module = 'simplepollmodule'");
        foreach ($gps as $gp) {
	        $gp->module = 'simplePoll';
	        $db->updateObject($gp,'grouppermission',"module = 'simplepollmodule' AND source = '".$gp->source."' AND permission = '".$gp->permission."'",'gid');
        }
        $ups = $db->selectObjects('userpermission',"module = 'simplepollmodule'");
        foreach ($ups as $up) {
            $up->module = 'simplePoll';
            $db->updateObject($up,'userpermission',"module = 'simplepollmodule' AND source = '".$up->source."' AND permission = '".$up->permission."'",'uid');
        }

		// convert each simplepollmodule_config to a simplePoll Controller expConfig
	    $modules_converted = 0;
	    $cns = $db->selectObjects('container',"internal LIKE '%simplepollmodule%'");
	    foreach ($cns as $cn) {
            $oldconfig = $db->selectObject('simplepollmodule_config', "location_data='".$cn->internal."'");
		    $cloc = expUnserialize($cn->internal);
	        $cloc->mod = 'simplePoll';
		    $cn->internal = serialize($cloc);
		    $cn->view = 'showall';
		    $cn->action = 'showall';
	        $db->updateObject($cn,'container');

            $newconfig = new expConfig();
            if (!empty($oldconfig)) {
                if (!empty($oldconfig->thank_you_message)) {
                    $newconfig->config['thank_you_message'] = 'Thank you for voting.';
                }
                if (!empty($oldconfig->already_voted_message)) {
                    $newconfig->config['already_voted_message'] = 'You have already voted in this poll.';
                }
                if (!empty($oldconfig->voting_closed_message)) {
                    $newconfig->config['voting_closed_message'] = 'Voting has been closed for this poll.';
                }
                if (!empty($oldconfig->anonymous_timeout)) {
                    $newconfig->config['anonymous_timeout'] = '5';
                }
            }
            if ($newconfig->config != null) {
                $newmodinternal = expUnserialize($cn->internal);
//                $newmod = explode("Controller",$newmodinternal->mod);
//                $newmodinternal->mod = $newmod[0];
                $newmodinternal->mod = expModules::getModuleName($newmodinternal->mod);
                $newconfig->location_data = $newmodinternal;
                $newconfig->save();
            }

	        $modules_converted += 1;
	    }

        // need to replace old module modstate with new SimplePoll module
        $ms = $db->selectObject('modstate',"module='simplepollmodule'");
        if (!empty($ms) && !$db->selectObject('modstate',"module='simplePollController'")) {
            $ms->module = 'simplePoll';
            $db->updateObject($ms,'modstate',"module='simplepollmodule'",'module');
        }

		// convert questions, answers, & time-blocks
        $questions_converted = 0;
		$questions = $db->selectArrays('poll_question',"1");
		foreach ($questions as $qi) {
            $qid = $qi['id'];
            unset ($qi['id']);
            $active = $qi['is_active'];
            unset ($qi['is_active']);
			$question = new simplepoll_question($qi);
			$loc = expUnserialize($qi['location_data']);
			$loc->mod = "simplePoll";
            $question->location_data = serialize($loc);
            $question->active = $active;
            if (empty($question->question)) { $question->question = 'Untitled'; }
            $question->save();

            $oldanswers = $db->selectArrays('poll_answer', "question_id='".$qid."'");
            foreach ($oldanswers as $oi) {
                unset ($oi['id']);
                unset ($oi['question_id']);
                $newanswer = new simplepoll_answer($oi);
                $newanswer->simplepoll_question_id = $question->id;
                $newanswer->update();
            }

            $oldblocks = $db->selectArrays('poll_timeblock', "question_id='".$qid."'");
            foreach ($oldblocks as $ob) {
                unset ($ob['id']);
                unset ($ob['question_id']);
                $newblock = new simplepoll_answer($ob);
                $newblock->simplepoll_question_id = $question->id;
                $newblock->update();
            }

			$questions_converted += 1;
		}

		// delete simplepollmodule tables
        $db->dropTable('poll_question');
        $db->dropTable('poll_answer');
        $db->dropTable('poll_timeblock');
		$db->dropTable('simplepollmodule_config');
        // delete simplepollmodule definitions/models
        $oldfiles = array (
            'framework/core/definitions/poll_question.php',
            'framework/core/definitions/poll_answer.php',
            'framework/core/definitions/poll_timeblock.php',
            'framework/core/definitions/simplepollmodule_config.php',
            'framework/core/models-1/poll_question.php',
            'framework/core/models-1/poll_answer.php',
            'framework/core/models-1/simplepollmodule_config.php',
        );
		// check if the old file exists and remove it
        foreach ($oldfiles as $file) {
            if (file_exists(BASE.$file)) {
                unlink(BASE.$file);
            }
        }
		// delete simplepollmodule files
        if (expUtil::isReallyWritable(BASE."framework/modules-1/simplepollmodule/")) {
            expFile::removeDirectory(BASE."framework/modules-1/simplepollmodule/");
        }

		return ($modules_converted?$modules_converted:gt('No'))." ".gt("Simple Poll modules were upgraded.")."<br>".($questions_converted?$questions_converted:gt('No'))." ".gt("Poll Questions were converted.")."<br>".gt("and simplepoll files were then deleted.");
	}
}

?>
