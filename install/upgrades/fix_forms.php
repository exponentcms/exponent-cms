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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class fix_forms
 */
class fix_forms extends upgradescript {
	protected $from_version = '2.1.1';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.1.2';  // formsController default action name was changed in 2.1.2

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update forms module with new default action name and fix save data table name"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "When the new forms module was released it wouldn't correctly create a tablename to save data.  This script updates existing forms."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        return true;
	}

	/**
	 * fixes existing forms modules and form data table names
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        // fix the default action of 'enter_data' with new method name 'enterdata'
        $actions_converted = 0;
        foreach ($db->selectObjects('container',"internal LIKE '%forms%' AND (view='enter_data' OR action='enter_data')") as $co) {
            $co->view = $co->action = 'enterdata';
            $db->updateObject($co,'container');
            $actions_converted++;
	    }

        // fix any saved tables with bad names
        $tables_converted = 0;
        foreach ($db->selectObjects('forms',"is_saved=1") as $sf) {
            $form = new forms($sf->id);
            if (is_int($form->table_name)) {
                $table_name = preg_replace('/[^A-Za-z0-9]/', '_', $form->title);
                // rename an existing table
                if ($db->tableExists('forms_' . $form->table_name)) {
                    $db->sql('RENAME TABLE '.DB_TABLE_PREFIX.'_forms_'.$form->table_name.' TO '.DB_TABLE_PREFIX.'_forms_'.$$table_name);
                }
                $form->table_name = $table_name;
            }
            $sf->table_name = $form->updateTable();  // create/update the data table
            $db->updateObject($sf,'forms');
            $tables_converted++;
	    }

        return ($actions_converted?$actions_converted:gt('No'))." ".gt("Forms modules were fixed.")."<br>".
            ($tables_converted?$tables_converted:gt('No'))." ".gt("Forms tables were corrected.");
	}
}

?>
