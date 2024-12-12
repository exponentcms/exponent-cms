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
 * This is the class fix_forms4
 *
 * @package Installation
 * @subpackage Upgrade
 */
class fix_forms4 extends upgradescript {
	protected $from_version = '2.7.0';  // forms search was added in 2.7.0
	protected $to_version = '2.7.1';  // forms search was fixed in 2.7.1

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update form and form search records"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Form location data was malformed and Extra Form search records accumulated each time a form record was saved.  This script updates existing form records and form search records."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

        // find any saved tables which are searchable
        if ($db->countObjects('forms', "is_saved=1 AND is_searchable=1")) {
            return true;
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
        $records_converted = 0;
        foreach ($db->selectObjects('forms',"is_saved=1") as $sf) {
            $form = new forms($sf->id);
            foreach ($form->getRecords() as $rec) {
                $form->updateRecord($rec);  // update the data record to create sef_url
                $records_converted++;
            }
	    }
        $db->delete('search', 'ref_module="forms"');
        $fc = new formsController();
        $records_added = $fc->addContentToSearch();

        return ($records_converted ? : gt('No')) . " " . gt("Form records were corrected.") . " " . gt("and") . "<br>" .
            ($records_added ? : gt('No')) . " " . gt("Form search records were updated.");
	}
}

?>
