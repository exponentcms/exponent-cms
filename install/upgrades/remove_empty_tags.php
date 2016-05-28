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
 * This is the class remove_empty_tags
 */
class remove_empty_tags extends upgradescript {
	protected $from_version = '2.3.0';
//	protected $to_version = '2.0.8';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Remove empty tags"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In previous versions, empty tags may have been created.  This script removes empty tags and references to them."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

        return $db->selectObjects('expTags', "title=''") != null ? true : false;
	}

	/**
	 * removes all empty tags and references to them
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $count = $db->countObjects('expTags', "title=''");
		$db->delete('expTags', "title=''");
		$db->delete('content_expTags', "NOT EXISTS (SELECT 1 FROM " . $db->prefix . "expTags WHERE " . $db->prefix . "expTags.id = " . $db->prefix . "content_expTags.exptags_id)");

        return ($count?$count:gt('No')).' '.gt('empty tags and their references were removed');
	}
}

?>
