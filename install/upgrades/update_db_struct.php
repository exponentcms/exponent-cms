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
 * This is the class update_db_struct
 *
 * @package Installation
 * @subpackage Upgrade
 */
class update_db_struct extends upgradescript {
	protected $from_version = '2.5.0';  // version number introduced
	protected $to_version = '2.6.0';  // version number fixed
//    public $optional = false;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Convert DB entries for MySql Strict mode"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.5.1 we began to support MySql Strict mode! This script converts existing data to allow the necessary db structure changes"; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

		return $db->countObjects('section', "noindex IS NULL OR nofollow IS NULL");
	}

	/**
	 * updates null entries to 0
     *
	 * @return string
	 */
	function upgrade() {
	    global $db;

        $rec_count = $db->countObjects('section', "noindex IS NULL OR nofollow IS NULL");

        $fixSQL =  "UPDATE " . $db->tableStmt('section') . " ";
        $fixSQL .= "SET noindex=0 "; // empty subtype needed
        $fixSQL .= "WHERE noindex IS NULL";
        $db->sql($fixSQL, false);

        $fixSQL =  "UPDATE " . $db->tableStmt('section') . " ";
        $fixSQL .= "SET nofollow=0 "; // empty subtype needed
        $fixSQL .= "WHERE nofollow IS NULL";
        $db->sql($fixSQL, false);

        // finally do an agressive update to enforce the table changes
        expDatabase::install_dbtables(true);

		return ($rec_count?$rec_count:gt('No')) . " " . "Bad Records Updated.";
	}
}

?>
