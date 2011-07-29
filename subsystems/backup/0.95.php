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
/** @define "BASE" "../.." */

// Converting EQL version 0.95 to 0.96

if (!defined('EXPONENT')) exit('');

function exponent_backup_095_clearedTable($db,$table) {
	if ($table == 'calendar') {
		$db->delete('eventdate'); // Clear eventdate as well, so that multiple imports don't double the table size
	}
}

// Field 'copy_id' was removed from 'addressbook_contact' in 0.96
function exponent_backup_095_addressbook_contact($db,$object) {
	unset($object->copy_id);
	$db->insertObject($object,'addressbook_contact');
}

// Field 'edited' was added to 'calendar' in 0.96
// Field 'editor' was added to 'calendar' in 0.96
// Field 'is_allday' was added to 'calendar' in 0.96
// Field 'is_recurring' was added to 'calendar' in 0.96
// Field 'category_id' was added to 'calendar' in 0.96
// Field 'feedback_form' was added to 'calendar' in 0.96
// Field 'feedback_email' was added to 'calendar' in 0.96
function exponent_backup_095_calendar($db,$object) {
	if (!defined('SYS_DATETIME')) include(BASE.'subsystems/datetime.php');

	// Pull edited / editor from posted / poster
	$object->editor = $object->poster;
	$object->edited = $object->posted;
	
	// Set recurrence and is_allday to false
	$object->is_recurring = 0;
	$object->is_allday = 0;
	
	// Reset eventstart and eventend to be just times, and create an eventdate.
	$eventdate = null;
	$eventdate->location_data = $object->location_data;
	// Get the time in seconds since midnight, first for event start
	
	$eventdate->date = exponent_datetime_startOfDayTimestamp($object->eventstart);
	$object->eventstart -= $eventdate->date;
	$object->eventend -= $eventdate->date;
	
	// Don't have to hangle categoriy_id, feedback_form or feedback_email
	$eventdate->event_id = $db->insertObject($object,'calendar');
	$db->insertObject($eventdate,'eventdate');
}

// Field 'view_data' was added to 'container' in 0.96
function exponent_backup_095_container($db,$object) {
	$object->view_data = serialize(null);
	$db->insertObject($object,'container');
}

// Field 'edited' was added to 'newsitem' in 0.96
// Field 'editor' was added to 'newsitem' in 0.96
function exponent_backup_095_newsitem($db,$object) {
	// Pull edited / editor from posted / poster
	$object->editor = $object->poster;
	$object->edited = $object->posted;
	$db->insertObject($object,'newsitem');
}

// Field 'sortfield' was added to 'newsmodule_config' in 0.96
// Field 'item_limit' was added to 'newsmodule_config' in 0.96
function exponent_backup_095_newsmodule_config($db,$object) {
	$object->item_limit = 5;
	$object->sortfield = 'posted';
	$db->insertObject($object,'newsmodule_config');
}

// Field 'posted' was added to 'resourceitem' in 0.96
// Field 'poster' was added to 'resourceitem' in 0.96
// Field 'edited' was added to 'resourceitem' in 0.96
// Field 'editor' was added to 'resourceitem' in 0.96
// NOTHING can be done.
//function exponent_backup_095_resourceitem($db,$object) {
//	$db->insertObject($object,'resourceitem');
//}

// Field 'keywords' was added to 'section' in 0.96
// Field 'description' was added to 'section' in 0.96
// Field 'alias_type' was added to 'section' in 0.96
// Field 'external_link' was added to 'section' in 0.96
// Field 'internal_id' was added to 'section' in 0.96
// NOTHING needs to be done
//function exponent_backup_095_section($db,$object) {
//	$db->insertObject($object,'section');
//}

// Field 'page_title' was added to 'section_template' in 0.96
// Field 'keywords' was added to 'section_template' in 0.96
// Field 'description' was added to 'section_template' in 0.96
// NOTHING needs to be done
//function exponent_backup_095_section_template($db,$object) {
//	$db->insertObject($object,'section_template');
//}

// Table 'troubleshooter' was removed in 0.96
function exponent_backup_095_troubleshooter($db,$object) {
	// do nothing
}

// Field 'is_acting_admin' was added to 'user' in 0.96
function exponent_backup_095_user($db,$object) {
	$object->is_acting_admin = $object->is_admin;
	$db->insertObject($object,'user');
}

// Field 'edited' was added to 'weblog_comment' in 0.96
// Field 'editor' was added to 'weblog_comment' in 0.96
function exponent_backup_095_weblog_comment($db,$object) {
	// Pull edited / editor from posted / poster
	$object->editor = $object->poster;
	$object->edited = $object->posted;
	$db->insertObject($object,'weblog_comment');
}

// Field 'edited' was added to 'weblog_post' in 0.96
// Field 'editor' was added to 'weblog_post' in 0.96
function exponent_backup_095_weblog_post($db,$object) {
	// Pull edited / editor from posted / poster
	$object->editor = $object->poster;
	$object->edited = $object->posted;
	$db->insertObject($object,'weblog_post');
}

// Field 'items_per_page' was added to 'weblogmodule_config' in 0.96
function exponent_backup_095_weblogmodule_config($db,$object) {
	$object->items_per_page = 15; // So that it doesn't default to 0
	$db->insertObject($object,'weblogmodule_config');
}

// Table 'calendarmodule_config' was added in 0.96
// Table 'category' was added in 0.96
// Table 'eventdate' was added in 0.96
// Table 'imagegallery_gallery' was added in 0.96
// Table 'formbuilder_address' was added in 0.96
// Table 'formbuilder_control' was added in 0.96
// Table 'formbuilder_form' was added in 0.96
// Table 'formbuilder_report' was added in 0.96
// Table 'geo_country' was added in 0.96
// Table 'geo_region' was added in 0.96
// Table 'inbox_userconfig' was added in 0.96
// Table 'rotator_item' was added in 0.96
// Table 'search' was added in 0.96
// Table 'search_extension' was added in 0.96
// Table 'sharedcore_core' was added in 0.96
// Table 'sharedcore_extension' was added in 0.96
// Table 'sharedcore_site' was added in 0.96
// Table 'imagegallery_image' was added in 0.96
// Table 'swfitem' was added in 0.96

?>
