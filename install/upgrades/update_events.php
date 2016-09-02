<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * This is the class update_events
 */
class update_events extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.3.2';  // orders table grows extremely large with every user visit to an ecommerce site
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Prune abandoned event records"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "An event record is associated with an eventdate record for every recurrence. In some cases all event dates were deleted, but the event record still exists which can cause search anomalies.  This script prunes abandoned event records."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

		return $db->countObjectsBySql("SELECT COUNT(*) as c FROM `".$db->prefix."event` WHERE `id` NOT IN (SELECT `event_id` FROM `".$db->prefix."eventdate`)");  // only needed if there if issues
	}

	/**
	 * prunes orphan records from event table
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		$events_count = $db->countObjectsBySql("SELECT COUNT(*) as c FROM `".$db->prefix."event` WHERE `id` NOT IN (SELECT `event_id` FROM `".$db->prefix."eventdate`)");
		$db->delete("event","`id` NOT IN (SELECT `event_id` FROM `".$db->prefix."eventdate`)");
		// let's update the search index to reflect the current events
		searchController::spider();
		return ($events_count?$events_count:gt('No'))." ".gt("orphaned Events")." ".gt("were found and removed from the database.");
	}
}

?>
