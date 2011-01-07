<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

/* exdoc
 * The definition of this constant lets other parts
 * of the system know that the Sorting Subsystem
 * has been included for use.
 * @node Subsystems:Sorting
 */
define("SYS_SORTING",1);

/* exdoc
 * Object sorting comparison function -- sorts by rank in ascending order.
 * @node Subsystems:Sorting
 */
function exponent_sorting_byRankAscending($a,$b) {
	return ($a->rank < $b->rank ? -1 : 1);
}

/* exdoc
 * Object sorting comparison function -- sorts by rank in descending order.
 * @node Subsystems:Sorting
 */
function exponent_sorting_byRankDescending($a,$b) {
	return ($a->rank > $b->rank ? -1 : 1);
}

/* exdoc
 * Object sorting comparison function -- sorts by name in ascending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_byNameAscending($a,$b) {
	return strnatcasecmp($a->name,$b->name);
}

/* exdoc
 * Object sorting comparison function -- sorts by name in descending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_byNameDescending($a,$b) {
	return (strnatcasecmp($a->name,$b->name) == -1 ? 1 : -1);
}

/* exdoc
 * Object sorting comparison function -- sorts by title in ascending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_byTitleAscending($a,$b) {
	return strnatcasecmp($a->title,$b->title);
}

/* exdoc
 * Object sorting comparison function -- sorts by title in descending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_byTitleDescending($a,$b) {
	return (strnatcasecmp($a->title,$b->title) == -1 ? 1 : -1);
}

function exponent_sorting_byLastNameAscending($a,$b) {
        return strnatcasecmp($a->lastname,$b->lastname);
}

function exponent_sorting_byFirstNameAscending($a,$b) {
        return strnatcasecmp($a->firstname,$b->firstname);
}
/* exdoc
 * Object sorting comparison function -- sorts by username attribute in ascending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_byUserNameAscending($a,$b) {
	return strnatcasecmp($a->username,$b->username);
}

/* exdoc
 * Object sorting comparison function -- sorts by username attribute in descending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_byUserNameDescending($a,$b) {
	return (strnatcasecmp($a->username,$b->username) == -1 ? 1 : -1);
}

/* exdoc
 * Object sorting comparison function -- sorts by posted attribute in ascending order.
 * @node Subsystems:Sorting
 */
function exponent_sorting_byPostedAscending($a,$b) {
	return ($a->posted < $b->posted ? -1 : 1);
}

/* exdoc
 * Object sorting comparison function -- sorts by posted attribute in descending order.
 * @node Subsystems:Sorting
 */
function exponent_sorting_byPostedDescending($a,$b) {
	return ($a->posted > $b->posted ? -1 : 1);
}

function exponent_sorting_byEditedAscending($a,$b) {
	return ($a->edited < $b->edited ? -1 : 1);
}
function exponent_sorting_byEditedDescending($a,$b) {
    return ($a->edited > $b->edited ? -1 : 1);
}
/* exdoc
 * Object sorting comparison function -- sorts by updated attribute in ascending order.
 * @node Subsystems:Sorting
 */
function exponent_sorting_byUpdatedAscending($a,$b) {
	return ($a->updated < $b->updated ? -1 : 1);
}

/* exdoc
 * Object sorting comparison function -- sorts by updated attribute in descending order.
 * @node Subsystems:Sorting
 */
function exponent_sorting_byUpdatedDescending($a,$b) {
	return ($a->updated > $b->updated ? -1 : 1);
}

/* exdoc
 * Object sorting comparison function -- sorts by name method return value in ascending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_moduleByNameAscending($a,$b) {
	return strnatcasecmp($a->name(),$b->name());
}

/* exdoc
 * Object sorting comparison function -- sorts by name method return value in descending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_moduleByNameDescending($a,$b) {
	return (strnatcasecmp($a->name(),$b->name()) == -1 ? 1 : -1);
}

/* exdoc
 * Class name sorting comparison function -- sorts by name method return value in ascending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_moduleClassByNameAscending($a,$b) {
	return strnatcasecmp(call_user_func(array($a,"name")),call_user_func(array($b,"name")));
}

/* exdoc
 * Class name sorting comparison function -- sorts by name method return value in descending order.
 * Uses a natural order, case-insensitive comparison algorithm (strnatcasecmp)
 * @node Subsystems:Sorting
 */
function exponent_sorting_moduleClassByNameDescending($a,$b) {
	return (strnatcasecmp(call_user_func(array($a,"name")),call_user_func(array($b,"name"))) == -1 ? 1 : -1);
}

/* exdoc
 * Workflow revision sorting comparison function -- sorts by major and minor in ascending order.
 * @node Subsystems:Sorting
 */
function exponent_sorting_workflowRevisionAscending($a,$b) {
	return strnatcmp($a->wf_major.".".$a->wf_minor,$b->wf_major.".".$b->wf_minor);
}

/* exdoc
 * Workflow revision sorting comparison function -- sorts by major and minor in descending order.
 * @node Subsystems:Sorting
 */
function exponent_sorting_workflowRevisionDescending($a,$b) {
	return (strnatcmp($a->wf_major.".".$a->wf_minor,$b->wf_major.".".$b->wf_minor) == -1 ? 1 : -1);
}

?>
