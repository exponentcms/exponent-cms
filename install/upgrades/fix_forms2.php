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
 * This is the class fix_forms2
 *
 * @package Installation
 * @subpackage Upgrade
 */
class fix_forms2 extends upgradescript {
	protected $from_version = '2.1.1';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.4.2';  // forms data sef_url was added in 2.4.2

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update saved form data records with sef url field"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Originally form data did not include an sef url.  This script updates existing form data records."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

        // find any saved tables without sef urls
        foreach ($db->selectObjects('forms', "is_saved=1") as $sf) {
            $form = new forms($sf->id);
            if (!$db->selectColumn('forms_' . $form->table_name, "sef_url") ) {
                return true;
            }
   	    }

        return false;
	}

	/**
	 * fixes existing form data tables
	 * @return string
	 */
	function upgrade() {
	    global $db;

        // fix any saved tables without sef urls
        $tables_converted = 0;
        foreach ($db->selectObjects('forms',"is_saved=1") as $sf) {
            $form = new forms($sf->id);
//            if (!$db->selectColumn('forms_' . $form->table_name,"sef_url") ) {
                $form->updateTable();  // update the data table to add sef_url field
                foreach ($form->getRecords() as $rec) {
                    $form->updateRecord($rec);  // update the data record to create sef_url
                }
                $tables_converted++;
//            }
	    }

        return ($tables_converted?$tables_converted:gt('No'))." ".gt("Form tables had sef urls added to their saved data records.");
	}
}

?>
