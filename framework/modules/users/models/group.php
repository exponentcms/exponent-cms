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
 * @subpackage Models
 * @package Modules
 */
class group extends expRecord {
//    public $table = 'group';
    public $validates = array(
        'presence_of'=>array(
            'name'=>array('message'=>'Name is a required field.'),
        ),
        'uniqueness_of'=>array(
            'name'=>array('message'=>'There is already a group by that name.'),
        ),
	);

	/** exdoc
	 * This function pulls a group object form the subsystems storage mechanism,
	 * according to its ID.  For the default implementation, this is equivalent to a
	 * $db->selectObject() call, but it may not be the same for other implementations.
	 * Returns a group object, and null if no group was found.
	 *
	 * This function does NOT perform group caching like the user::getUserById
	 * function does.  Multiple calls to retrieve the same group result in multiple calls
	 * to the database.
	 *
	 * @param integer $gid The id of the group account to retrieve.
	 * @return \group
	 * @node Model:Group
	 */
	public static function getGroupById($gid) {
	//anonymous group -- NOT YET IMPLEMENTED
	/*    global $db;
		if ($gid == 0){
		   //anonymous group
		   $g->id = 0;
		   $g->name = gt('Anonymous Users - NOT YET WORKING');
		   $g->description = gt('This is a default system group for all non-logged in users. NOT YET WORKING');
		   $g->inclusive = 1;
		   return $g;
		} else {
		   return $db->selectObject('group','id='.$gid);
		}
	*/
	   global $db;
	   return $db->selectObject('group','id='.$gid);
	}

	/** exdoc
	 * This function consults the group membership data and returns a
	 * list of all users that belong to the specified group.  Returns
	 * an array of all user objects that belong to the specified group.
	 *
	 * @param Object $g The group object to obtain a member list for.
	 * @return array
	 * @node Model:Group
	 */
	public static function getUsersInGroup($gid) {
		global $db;
		if ($gid == null || !intval($gid)) {
			// Don't have enough information to consult the membership tables.
			// Return an empty array.
			return array();
		}
		// Holding array for the member users.
		$users = array();
		foreach ($db->selectObjects('groupmembership','group_id='.$gid) as $m) {
			// Loop over the membership records for this group, and append a basic user object to the holding array.
			$users[] = $db->selectObject('user','id='.$m->member_id);
		}
		// Return the list of user objects to the caller.
		return $users;
	}

	/** exdoc
	 * This function pulls a group object from the subsystems storage mechanism,
	 * according to the group name.  For the default implementation, this is equivalent
	 * to a $db->selectObject() call, but it may not be the same for other implementations.
	 * Returns a group object, and null if no group was found.
	 *
	 * This function does NOT perform group caching like the user::getUserById
	 * function does.  Multiple calls to retrieve the same group result in multiple calls
	 * to the database.
	 *
	 * @param integer $name The name of the group account to retrieve.
	 * @return \group
	 * @node Model:Group
	 */
	public static function getGroupByName($name) {
		global $db;
		return $db->selectObject('group',"name='$name'");
	}

	/** exdoc
	 * Gets a list of all group in the system.  By giving different
	 * combinations of the two boolean arguments. three different lists
	 * of groups can be returned.  Returns a list of groups, according to
	 *  the two parameters passed in.
	 *
	 * @param bool|int $allow_exclusive Whether or not to include exclusive groups in the returned list.
	 * @param bool|int $allow_inclusive Whether or not to include inclusive groups in the returned list.
	 * @return array
	 * @node Model:Group
	 */
	public static function getAllGroups($allow_exclusive=1,$allow_inclusive=1) {
		global $db;
		if ($allow_exclusive && $allow_inclusive) {
			// For both, just do a straight selectObjects call, with no WHERE criteria.
			return $db->selectObjects('group');
		} else if ($allow_exclusive) {
			// At this point, we know that $allow_inclusive was passed as false
			// So, we need to retrieve groups that are not inclusive.
			return $db->selectObjects('group','inclusive = 0');
		} else if ($allow_inclusive) {
			// At this point, we know that $allow_exclusive was passed as false
			// So, we need to retrieve groups that are inclusive.
			return $db->selectObjects('group','inclusive = 1');
		} else {
			// Both arguments were passed as false.  This is nonsensical, but why not
			// let the programmer shoot themselves in the foot.  Return an empty array.
			return array();
		}
	}

}

?>