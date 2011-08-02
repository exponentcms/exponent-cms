<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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
/** @define "BASE" ".." */

/* exdoc
 * The definition of this constant lets other parts of the system know 
 * that the subsystem has been included for use.
 * @node Subsystems:Workflow
 */
//define('SYS_WORKFLOW',1);

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_REVOKE_NONE',		0);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_REVOKE_POSTER',		1);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_REVOKE_APPROVERS',	2);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_REVOKE_OTHERS',		3);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_REVOKE_ALL',		4);

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_APPROVE_EDIT',		5);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_APPROVE_APPROVE',	6);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_APPROVE_DENY',		7);

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_POSTED',			0);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_EDITED',			1);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_APPROVED_APPROVED',	2);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_APPROVED_EDITED',		3);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_APPROVED_DENIED',		4);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_APPROVED_FINAL',		5);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_DELETED',		6);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_RESTARTED',		7);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_IMPLICIT_APPROVAL',	8);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_RESTORED',		9);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_POSTED_ADMIN',		10);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define('SYS_WORKFLOW_ACTION_APPROVED_ADMIN',		11);

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_dataDefinitions($tabledef) {
	return array(
		'_wf_revision'=>array_merge($tabledef,array(
			'wf_major'=>array(
				DB_FIELD_TYPE=>DB_DEF_INTEGER),
			'wf_minor'=>array(
				DB_FIELD_TYPE=>DB_DEF_INTEGER),
			'wf_original'=>array(
				DB_FIELD_TYPE=>DB_DEF_ID),
			'wf_state_data'=>array(
				DB_FIELD_TYPE=>DB_DEF_STRING,
				DB_FIELD_LEN=>1000),
			'wf_approved'=>array(
				DB_FIELD_TYPE=>DB_DEF_BOOLEAN),
			'wf_type'=>array(
				DB_FIELD_TYPE=>DB_DEF_INTEGER),
			'wf_updated'=>array(
				DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
			'wf_comment'=>array(
				DB_FIELD_TYPE=>DB_DEF_STRING,
				DB_FIELD_LEN=>5000),
			'wf_user_id'=>array(
				DB_FIELD_TYPE=>DB_DEF_ID)
		)),
		'_wf_info'=>array(
			'real_id'=>array(
				DB_FIELD_TYPE=>DB_DEF_ID),
			'location_data'=>array(
				DB_FIELD_TYPE=>DB_DEF_STRING,
				DB_FIELD_LEN=>200),
			'current_major'=>array(
				DB_FIELD_TYPE=>DB_DEF_INTEGER),
			'current_minor'=>array(
				DB_FIELD_TYPE=>DB_DEF_INTEGER),
			'open_slots'=>array(
				DB_FIELD_TYPE=>DB_DEF_INTEGER),
			'updated'=>array(
				DB_FIELD_TYPE=>DB_DEF_TIMESTAMP),
			'current_state_data'=>array(
				DB_FIELD_TYPE=>DB_DEF_STRING,
				DB_FIELD_LEN=>1000),
			'policy_id'=>array(
				DB_FIELD_TYPE=>DB_DEF_ID)
		)
	);
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_installWorkflowTables($existingname,$tabledef) {
	return exponent_workflow_alterWorkflowTables($existingname,$tabledef);
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_alterWorkflowTables($existingname,$newdatadef) {
	global $db;

	$return = array();

	$defs = exponent_workflow_dataDefinitions($newdatadef);
	
	if (!$db->tableExists($existingname.'_wf_revision')) {
		$tmp = $db->createTable($existingname.'_wf_revision',$defs['_wf_revision'],array(DB_TABLE_COMMENT=>'Workflow Revisions table for '.$existingname));
		$return[$existingname.'_wf_revision'] = $tmp[$existingname.'_wf_revision'];
	} else {
		$tmp = $db->alterTable($existingname.'_wf_revision',$defs['_wf_revision'],array(DB_TABLE_COMMENT=>'Workflow Revisions table for '.$existingname));
		$return[$existingname.'_wf_revision'] = $tmp[$existingname.'_wf_revision'];
	}
	
	if (!$db->tableExists($existingname.'_wf_info')) {
		$tmp = $db->createTable($existingname.'_wf_info',$defs['_wf_info'],array(DB_TABLE_COMMENT=>'Workflow Summary table for '.$existingname));
		$return[$existingname.'_wf_info'] = $tmp[$existingname.'_wf_info'];
	} else {
		$tmp = $db->alterTable($existingname.'_wf_info',$defs['_wf_info'],array(DB_TABLE_COMMENT=>'Workflow Summary table for '.$existingname));
		$return[$existingname.'_wf_info'] = $tmp[$existingname.'_wf_info'];
	}
	
	return $return;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_isWorkflowTable($name) {
	return (substr($name,-8,8) == '_wf_info' || substr($name,-12,12) == '_wf_revision');
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_originalTable($name) {
	if (substr($name,-8,8) == '_wf_info') return substr($name,0,-8);
	else if (substr($name,-11,11) == '_wf_revision') return substr($name,0,-11);
	return '';
}

// USED?
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_clearRevisions($existingname,$major) {
	global $db;
	$db->delete($existingname.'_wf_revision','wf_major='.$major.' && wf_minor != 0');
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_getPolicy($module,$source) {
	if (ENABLE_WORKFLOW){
		global $db;
		$assoc = $db->selectObject('approvalpolicyassociation',"module='$module' AND source='$source' AND is_global=0");
		if (!$assoc) return exponent_workflow_getDefaultPolicy($module);
		else {
			$policy = $db->selectObject('approvalpolicy','id='.$assoc->policy_id);
			return $policy;
		}
	}else{
		return null;
	}
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_getDefaultPolicy($module) {
	global $db;
	$assoc = $db->selectObject('approvalpolicyassociation',"module='$module' AND is_global=1");
	
	if ($assoc) {
		$policy = $db->selectObject('approvalpolicy','id='.$assoc->policy_id);
		return $policy;
	}
	return null;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_moduleUsesDefaultPolicy($module,$source) {
	global $db;
	$assoc = $db->selectObject("approvalpolicyassociation","module='$module' AND source='$source' AND is_global=0");
	return ($assoc == null);
}

// Returns true if passed state == approved
// ### STEP A ###
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_checkApprovalState($state,$policy) {
	if ($policy == null) return true; // faked implicit approval.
	$requirement = $policy->required_approvals;
	foreach ($state[1] as $id=>$approval) {
		if ($approval) $requirement--;
	}
	return ($requirement <= 0);
}

//------------------------
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_post($object,$table,$loc,$userdata = null) {
	global $db,$user;
	
	$policy = exponent_workflow_getPolicy($loc->mod,$loc->src);
	
	$is_post = false;
	
	if (isset($object->id)) {
		// Updating an existing
		$object->approved = 2;
		$fake = null;
		$fake->approved = 2;
		$fake->id = $object->id;
		$db->updateObject($fake,$table);
		$object->wf_original = $object->id;
		
		// SET ACTIONTYPE FOR RUNACTIONS
		$object->wf_type = SYS_WORKFLOW_ACTION_EDITED;
	} else {
		$is_post = true;
		$object->approved = 0;
		$object->wf_original = $db->insertObject($object,$table);
		$object->wf_type = SYS_WORKFLOW_ACTION_POSTED;
	}
	
	$object->wf_major = $db->max($table."_wf_revision","wf_major","wf_original","wf_original=".$object->wf_original);
	if ($object->wf_major == null) $object->wf_major = 0;
	$object->wf_minor = 1;
	$state = array(
		array($user->id+0),
		array($user->id=>1)
	);
	$object->wf_state_data = serialize($state);
	$object->wf_user_id = $user->id;
	
	// Now check approval right off the bat.  Admin is always exempt from workflow
	if (exponent_workflow_checkApprovalState($state,$policy) || $user->is_acting_admin == 1) {
		$object->wf_major++;
		$object->wf_minor = 0;
		
		$real_object = exponent_workflow_convertToObject($object);
		$real_object->approved = 1;
		$object->wf_updated = time();
		$db->updateObject($real_object,$table);
		
		// Call spidering for implicit / admin approval.
		if (is_callable(array($loc->mod,"spiderContent"))) call_user_func(array($loc->mod,"spiderContent"),$real_object);
		
		if ($user->is_acting_admin == 1) {
			$object->wf_type = SYS_WORKFLOW_ACTION_POSTED_ADMIN;
		} else {
			$object->wf_type = SYS_WORKFLOW_ACTION_IMPLICIT_APPROVAL;
		}
		
	} else {
		$info = exponent_workflow_updateInfoFromRevision($object,null);
		$info->location_data = $object->location_data;
		$info->policy_id = $policy->id;
		$info->open_slots = $policy->max_approvers;
		$info->updated = time();
		$db->insertObject($info,$table."_wf_info");
		
		$object->wf_updated = time();
	}
	unset($object->id);
	$db->insertObject($object,$table."_wf_revision");
	
	exponent_workflow_deleteOldRevisions($table,$object->wf_original);
	exponent_sessions_clearAllUsersSessionCache(); 
	
	// Now that we are almost done, we need to call the onWorkflow stuff.
	if (is_callable(array($table,'onWorkflowPost'))) {
		if (!isset($real_object)) {
			$real_object = exponent_workflow_convertToObject($object);
			$real_object->id = $object->wf_original;
		}
		call_user_func(array($table,'onWorkflowPost'),$real_object,$is_post,$userdata);
	}
	
	if ($policy != null) {
		// run actions, either EDIT or POST or IMPLICIT_APPROVAL
		exponent_workflow_runActions($policy,$object->wf_type,$object);
	} else {
		// Catch-all redirect - in case its a new post, implicitly approved, with no policy
		exponent_flow_redirect();
	}
}

// ### STEP A.5 ###
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_processApproval($id,$datatype,$response,$comment="") {
	global $db;
	
	$info = $db->selectObject($datatype."_wf_info","real_id=".$id);
	$latest = $db->selectObject($datatype."_wf_revision","wf_original=".$id." AND wf_major=".$info->current_major." AND wf_minor=".$info->current_minor);
	$policy = $db->selectObject("approvalpolicy","id=".$info->policy_id);
	$state = unserialize($latest->wf_state_data);
	
	$latest->wf_minor++;
	$latest->wf_comment = $comment;

	$dataobj = new $datatype(); // usually a static class, but we cant do that with var class name
	
	global $user;
	$revoketype = SYS_WORKFLOW_REVOKE_NONE;
	$latest->wf_type = -1;
	$latest->wf_user_id = $user->id;
	
	// FIXME - need to check for repeat approvers / poster
	if (!in_array($user->id+0,$state[0])) {
		$state[0][] = $user->id+0;
		$info->open_slots = $policy->max_approvers + 1 - count($state[0]);
	}
	
	switch($response) {
		case SYS_WORKFLOW_APPROVE_EDIT:
			$revoketype = $policy->on_edit;
			$latest->wf_type = SYS_WORKFLOW_ACTION_APPROVED_EDITED;
			$latest = call_user_func(array($datatype,"update"),$_POST,$latest);
			// Update the comment, also entered on the form.
			#$latest->wf_comment = $_POST['wf_comment'];
			$state[1][$user->id] = 1;
			break;
		case SYS_WORKFLOW_APPROVE_APPROVE:
			$revoketype = $policy->on_approve;
			$latest->wf_type = SYS_WORKFLOW_ACTION_APPROVED_APPROVED;
			$state[1][$user->id] = 1;
			break;
		case SYS_WORKFLOW_APPROVE_DENY:
			$revoketype = $policy->on_deny;
			$state[1][$user->id] = 0;
			if ($policy->delete_on_deny == 1) {
				$latest->wf_type = SYS_WORKFLOW_ACTION_DELETED;
				exponent_workflow_deleteRevisionPath($datatype,$latest->wf_original);
			} else if ($user->is_acting_admin == 1) {
				// Admin denials always end up in deletion.  It saves them the extra step.
				$latest->wf_type = SYS_WORKFLOW_ACTION_DELETED;
				exponent_workflow_deleteRevisionPath($datatype,$latest->wf_original);
			} else {
				$latest->wf_type = SYS_WORKFLOW_ACTION_APPROVED_DENIED;
				#$latest->wf_comment = $comment;
			}
			break;
	}
	$state = exponent_workflow_revoke($state,$revoketype);
	$latest->wf_state_data = serialize($state);
	
	$info = exponent_workflow_updateInfoFromRevision($latest,$info);
	global $user;
	if (exponent_workflow_checkApprovalState($state,$policy) || $user->is_acting_admin == 1) {
		// Final approval given.
		exponent_workflow_handleApprovedRevision($latest,$datatype,$info);
	} else {
		if ($latest->wf_type != SYS_WORKFLOW_ACTION_DELETED) {
			// only handle revisions if we have not deleted the revision Path
			exponent_workflow_handleRevision($latest,$datatype,$info);
		}
		// run actions for $latest->wf_type
		exponent_workflow_runActions($policy,$latest->wf_type,$latest);
	}
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_handleApprovedRevision($revision,$datatype,$info) {
	global $db;
	
	$real = exponent_workflow_convertToObject($revision);
	$real->approved = 1;
	
	$revision->wf_minor = 0;
	$revision->wf_major++;
	
	$db->updateObject($real,$datatype,"id=".$real->id);
	
	// Update search index.
	$loc = unserialize($info->location_data);
	if (is_callable(array($loc->mod,"spiderContent"))) call_user_func(array($loc->mod,"spiderContent"),$real);
	
	global $user;
	unset($revision->id);
	$revision->wf_updated = time();
	$revision->wf_user_id = $user->id;
	$db->insertObject($revision,$datatype."_wf_revision");
	
	// Delete the info object.
	$db->delete($datatype."_wf_info","real_id=".$info->real_id);
	
	// run actions for ACTION_APPROVED_FINAL
	$policy = $db->selectObject("approvalpolicy","id=".$info->policy_id);
	if ($user->is_acting_admin == 1) {
		$action = SYS_WORKFLOW_ACTION_APPROVED_ADMIN;
	} else {
		$action = SYS_WORKFLOW_ACTION_APPROVED_FINAL;
	}
	exponent_workflow_runActions($policy,$action,$revision);
	
	return $revision;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_handleRevision($revision,$datatype,$info) {
	global $db, $user;
	
	unset($revision->id);
	$revision->wf_updated = time();
	$revision->wf_user_id = $user->id;
	
	$db->insertObject($revision,$datatype."_wf_revision");
	
	// Update the info object.
	$db->updateObject($info,$datatype."_wf_info","real_id=".$info->real_id);
	
	// run no actions - already handled by whoever called us.
	
	return $revision;
}

// ### STEP B,D,E ###
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_revoke($state,$type) {
	global $user; // for use in OTHERS
	switch ($type) {
		case SYS_WORKFLOW_REVOKE_ALL: // revoke everyone
			for ($i = 0; $i < count($state[0]); $i++) {
				$state[1][$state[0][$i]] = 0;
			}
			break;
		case SYS_WORKFLOW_REVOKE_OTHERS: // revoke everybody else (poster / approver)
			for ($i = 0; $i < count($state[0]); $i++) {
				if ($state[0][$i] != $user->id) $state[1][$state[0][$i]] = 0;
			}
			break;
		case SYS_WORKFLOW_REVOKE_POSTER: // revoke just the poster
			$state[1][$state[0][0]] = 0;
			break;
		case SYS_WORKFLOW_REVOKE_APPROVERS: // revoke all other approvers
			for ($i = 1; $i < count($state[0]); $i++) {
				if ($state[0][$i] != $user->id) $state[1][$state[0][$i]] = 0;
			}
			break;
		case SYS_WORKFLOW_REVOKE_NONE:
			break;
	}
	return $state;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_deleteRevisionPath($datatype,$id) {
	global $db;
	$info = $db->selectObject($datatype.'_wf_info','real_id='.$id);
	$revision = $db->selectObject($datatype.'_wf_revision','wf_original='.$id.' AND wf_major='.$info->current_major.' AND wf_minor='.$info->current_minor);

	$db->delete($datatype.'_wf_info','real_id='.$id);
	$db->delete($datatype.'_wf_revision','wf_original='.$id.' AND wf_major='.$info->current_major);
	$orig = $db->selectObject($datatype,"id=".$id);
	
	if ($orig->approved == 0) {
		// Never been posted live.  Delete it
		$db->delete($datatype,'id='.$id);
	} else {
		// Revision path deleted was an edit.  Restore original to fully-approved state.
		$orig->approved = 1;
		$db->updateObject($orig,$datatype);
	}
	
	// Run the actions for SYS_WORKFLOW_ACTION_DELETED
	$policy = $db->selectObject('approvalpolicy','id='.$info->policy_id);
	exponent_workflow_runActions($policy,SYS_WORKFLOW_ACTION_DELETED,$revision);
}

function exponent_workflow_deleteOldRevisions($datatype,$id) {
	if (WORKFLOW_REVISION_LIMIT > 0) {
		// User has specified that we delete older revisions
		global $db;
		$max_revision = $db->max($datatype.'_wf_revision','wf_major','wf_original','wf_original='.$id);
		$min_revision = $db->min($datatype.'_wf_revision','wf_major','wf_original','wf_original='.$id);
		if ($max_revision == null) {
			return;
		}
		if ($max_revision - $min_revision > WORKFLOW_REVISION_LIMIT) {
			$db->delete($datatype.'_wf_revision','wf_original='.$id.' AND wf_major < '.($max_revision - WORKFLOW_REVISION_LIMIT));
		}
	}
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_restoreRevision($datatype,$real_id,$major) {
	global $db, $user;
	
	$info = $db->selectObject($datatype."_wf_info","real_id=".$real_id);
	$revision = $db->selectObject($datatype."_wf_revision","wf_original=".$real_id." AND wf_major=$major AND wf_minor=0");
	
	if ($info) {
		// If in approval, delete all hanging revisions
		$db->delete($datatype."_wf_info","real_id=".$real_id);
		$db->delete($datatype."_wf_revision","wf_original=".$real_id." AND wf_major=".$info->current_major ." AND wf_minor != 0");
	}
	$real = exponent_workflow_convertToObject($revision);
	$real->approved = 1;
	$db->updateObject($real,$datatype,"id=".$real->id); // Update original
	
	// Save new 'restore' revision
	$revision->wf_comment = "Restore from version " . $revision->wf_major . ".0";
	$revision->wf_major = $db->max($datatype."_wf_revision","wf_major","wf_original","wf_original=".$real_id) + 1;
	$revision->wf_type = SYS_WORKFLOW_ACTION_RESTORED;
	$revision->wf_updated = time();
	$revision->wf_user_id = $user->id;
	
	unset($revision->id);
	
	
	$db->insertObject($revision,$datatype."_wf_revision");
}

// Convert a revision back into the original object
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_convertToObject($revision) {
	//$object = clone $revision;
	
	$object = exponent_core_copyObject($revision);
	#$object = $revision;
	unset($object->wf_major);
	unset($object->wf_minor);
	unset($object->wf_state_data);
	unset($object->wf_approved);
	$object->id = $object->wf_original;
	unset($object->wf_original);
	unset($object->wf_updated);
	unset($object->wf_comment);
	unset($object->wf_type);
	unset($object->wf_user_id);
	return $object;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_updateInfoFromRevision($revision,$info) {
	$info->real_id = $revision->wf_original;
	$info->current_state_data = $revision->wf_state_data;	
	$info->current_major = $revision->wf_major;
	$info->current_minor = $revision->wf_minor;
	$info->location_data = $revision->location_data;
	return $info;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_getInfoTables() {
	global $db;
	$infotables = array();
	foreach ($db->getTables() as $table) {
		if (substr($table,-8,8) == "_wf_info") {
			$infotables[] = str_replace(DB_TABLE_PREFIX.'_',"",$table);
		}
	}
	return $infotables;
}

// For restarting when a policy is changed through the policy manager
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_restartRevisionPath($revision,$type,$newpolicy,$info) {
	global $db,$user;
	
	$state = unserialize($revision->wf_state_data);
	$state = exponent_workflow_revoke($state,SYS_WORKFLOW_REVOKE_ALL);
	
	$revision->wf_state_data = serialize($state);
	$info->current_state_data = serialize($state);
	
	$revision->wf_minor++;
	$info->current_minor = $revision->wf_minor;
	
	$revision->wf_user_id = $user->id;
	
	$info->open_slots = $newpolicy->max_approvers + 1 - count($state[0]);
	
	$policy = $db->selectObject("approvalpolicy","policy_id=".$info->policy_id);
	return exponent_workflow_handleRevision($revision,$type,$info);
	// run the restart action;
	exponent_workflow_runActions($policy,SYS_WORKFLOW_ACTION_RESTART,$revision);
}

// For re-evaluating when a policy is changed through the policy manager
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_evaluateRevisionPath($revision,$type,$newpolicy,$info) {
	$state = unserialize($revision->wf_state_data);
	$approved = exponent_workflow_checkApprovalState($state,$newpolicy);
	
	$info->open_slots = $newpolicy->max_approvers + 1 - count($state[0]);
	
	if ($approved) {
		return exponent_workflow_handleApprovedRevision($revision,$type,$info);
	} else {
		return exponent_workflow_handleRevision($revision,$type,$info);
	}
	return $revision;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_form($datatype,$id) {
	global $db;
	$info = $db->selectObject($datatype."_wf_info","real_id=".$id);
	$latest = $db->selectObject($datatype."_wf_revision","wf_original=".$id." AND wf_major=".$info->current_major." AND wf_minor=".$info->current_minor);
	
	$form = call_user_func(array($datatype,"form"),$latest);
	
	// INSERT comment box
	$form->registerBefore("submit","wf_comment","Comments",new texteditorcontrol());
	$form->registerBefore("wf_comment",uniqid(""),"", new htmlcontrol("<hr size='1' /><br />"));
	
	return $form;
}

// Run all the actions for a specific action hook
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_runActions($policy,$action_type,$revision) {
	global $db;
	$actions = $db->selectObjects("workflowaction","policy_id=".$policy->id." AND type=$action_type");
//	if (!defined('SYS_SORTING')) include_once(BASE.'subsystems/sorting.php');
	include_once(BASE.'subsystems/sorting.php');
	usort($actions,"exponent_sorting_byRankAscending");
	foreach ($actions as $action) {
		if (is_readable(BASE."subsystems/workflow/actions/".$action->method.".php")) {
			include_once(BASE."subsystems/workflow/actions/".$action->method.".php");
		}
	}
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_getActions($policy_id) {
	global $db;
	
	$actions = array();
	foreach ($db->selectObjects("workflowaction","policy_id=".$policy_id) as $action) {
		if (!isset($actions[$action->type])) {
		}
		$actions[$action->type][$action->rank] = $action;
	}
	
	$keys = array_keys($actions);
	for ($i = 0; $i < count($keys); $i++) {
		ksort($actions[$keys[$i]]);
	}
	// No sorting is done.
	return $actions;
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_workflow_getAvailableActions() {
	$actions = array();
	if (is_readable(BASE."subsystems/workflow/actions")) {
		$dh = opendir(BASE."subsystems/workflow/actions");
		while (($file = readdir($dh)) !== false) {
			if (is_readable(BASE."subsystems/workflow/actions/$file") && substr($file,-4,4) == ".php") {	
				$action = substr($file,0,-4);
				$actions[$action] = $action;
			}
		}
	}
	return $actions;
}

?>