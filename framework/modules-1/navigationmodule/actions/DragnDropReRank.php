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

$move = $_REQUEST['move'];
$target = $_REQUEST['target'];
$type = $_REQUEST['type'];


$targSec = $db->selectObject("section","id=".$target);
$check_id = $targSec->parent;
$moveSec = $db->selectObject("section","id=".$move);

if (expPermissions::check('manage',expCore::makeLocation('navigationmodule','',$check_id))) {
	if ($type == "append"){
		//save the old parent in case we are changing the depth of the moving section
		$oldParent = $moveSec->parent;
		
		//assign the parent of the moving section to the ID of the target section
		$moveSec->parent = $targSec->id;
		
		//set the rank of the moving section to 0 since it will appear first in the new order
		$moveSec->rank = 0;
		
		//select all children currently of the parent we're about to append to
		$targSecChildren = $db->selectObjects("section","parent=".$targSec->id." ORDER BY rank");
		
		//update the ranks of the children to +1 higher to accomodate our new ranl 0 section being moved in.
		$newrank=1;
		foreach ($targSecChildren as $value) {
			if($value->id!=$moveSec->id){
				$value->rank = $newrank;
				$db->updateObject($value,'section');
				$newrank++;
			}
		}
		
		$db->updateObject($moveSec,'section');
		
		if ($oldParent != $moveSec->parent){
			//we need to re-rank the children of the parent that the miving section has just left
			$chilOfLastMove = $db->selectObjects("section","parent=".$oldParent." ORDER BY rank");
			for ($i=0; $i<count($chilOfLastMove);$i++) {
				$chilOfLastMove[$i]->rank = $i;
				$db->updateObject($chilOfLastMove[$i],'section');
			}
			
		}
		
		echo $moveSec->name . " was appended to " . $targSec->name;
		eDebug($oldParent);
		eDebug($moveSec->parent);
		
	} elseif ($type == "after"){
		if ($targSec->parent == $moveSec->parent){
			//are we moving up...
			if ($targSec->rank < $moveSec->rank){
				$moveSec->rank = $targSec->rank+1;
				$moveNextSiblings = $db->selectObjects("section","id!=".$moveSec->id." AND parent=".$targSec->parent." AND rank>".$targSec->rank." ORDER BY rank");
				$rerank=$moveSec->rank+1;
				eDebug($targSec);
				eDebug($moveSec);
				foreach ($moveNextSiblings as $value) {
					if($value->id!=$moveSec->id){
						$value->rank=$rerank;
						eDebug($value);
						$db->updateObject($value,'section');
						$rerank++;
					}
				}
				$db->updateObject($targSec,'section');
				$db->updateObject($moveSec,'section');
			//or are we moving down...
			} else {
				$targSec->rank = $targSec->rank-1;
				$moveSec->rank = $targSec->rank+1;
				$movePreviousSiblings = $db->selectObjects("section","id!=".$moveSec->id." AND parent=".$targSec->parent." AND rank<=".$targSec->rank." ORDER BY rank");
				$rerank=0;
				foreach ($movePreviousSiblings as $value) {
					if($value->id!=$moveSec->id){
						$value->rank=$rerank;
						$db->updateObject($value,'section');
						$rerank++;
					}
				}
				$db->updateObject($targSec,'section');
				$db->updateObject($moveSec,'section');
				eDebug($movePreviousSiblings);
				eDebug($targSec);
				eDebug($moveSec);
				
			}
		} else {
			//store ranks frome the depth we're moving from.  Used to re-rank the level depth the moving section is moving from.
			$oldRank = $moveSec->rank;
			$oldParent = $moveSec->parent;
			
			//select all children of the target sections parent with a rank higher than it's own
			$moveNextSiblings = $db->selectObjects("section","parent=".$targSec->parent." AND rank>".$targSec->rank." ORDER BY rank");
			
			//update moving sections rank and parent
			$moveSec->rank = $targSec->rank+1;
			$moveSec->parent = $targSec->parent;

			eDebug($targSec);
			eDebug($moveSec);


			//$rerank=$moveSec->rank+1;
			foreach ($moveNextSiblings as $value) {
				$value->rank=$value->rank+1;
				eDebug($value);
				$db->updateObject($value,'section');
			}
			
			$db->updateObject($moveSec,'section');

			//handle re-ranking of previous parent
			
			$oldSiblings = $db->selectObjects("section","parent=".$oldParent." AND rank>".$oldRank." ORDER BY rank");
			$rerank=0;
			foreach ($oldSiblings as $value) {
				if($value->id!=$moveSec->id){
					$value->rank=$rerank;
					$db->updateObject($value,'section');
					$rerank++;
				}
			}
			
			if ($oldParent != $moveSec->parent){
				//we need to re-rank the children of the parent that the miving section has just left
				$chilOfLastMove = $db->selectObjects("section","parent=".$oldParent." ORDER BY rank");
				for ($i=0; $i<count($chilOfLastMove);$i++) {
					$chilOfLastMove[$i]->rank = $i;
					$db->updateObject($chilOfLastMove[$i],'section');
				}

			}
		}
	}

	navigationmodule::checkForSectionalAdmins($move);	
	expSession::clearAllUsersSessionCache('navigationmodule');
	
} else {
	echo SITE_403_HTML;
}

?>
