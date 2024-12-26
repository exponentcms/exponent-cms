<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
 * This is the class update_tags
 *
 * @package Installation
 * @subpackage Upgrade
 */
class update_tags extends upgradescript {
	protected $from_version = '2.5.0';  // version number introduced
	protected $to_version = '2.5.1';  // version number fixed
//    public $optional = false;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Convert bad tag entries for v2.5.0"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.5.0 we stored tags with an index subtype which prevented having more than one tag per item! This script removes subtype entries"; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

		return $db->countObjects('content_expTags', "subtype REGEXP '[0-9]'");
	}

	/**
	 * swaps removes subtype from expTag content entries
     *
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $tag_count = $db->countObjects('content_expTags', "subtype REGEXP '[0-9]'");
        $fixSQL =  "UPDATE " . $db->tableStmt('content_expTags') . " ";
        $fixSQL .= "SET subtype='' "; // empty subtype needed
        $fixSQL .= "WHERE subtype REGEXP '[0-9]'";
        $db->sql($fixSQL, false);

		return ($tag_count?$tag_count:gt('No')) . " " . "Bad Tags Fixed.";
	}
}

?>
