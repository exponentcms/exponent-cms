<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * This is the class fix_table_names
 */
class fix_table_names extends upgradescript {
	protected $from_version = '1.99.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.0.9';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Attempts to rename mixed case table names"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "On some server filesystems (Windows), the table names may lose their case.  This script attempts to rename those tables."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        return true;
	}

	/**
	 * attempts to rename the mixed case tables
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $tablenames = array (
            'content_expcats'=>'content_expCats',
            'content_expcomments'=>'content_expComments',
            'content_expdefinablefields'=>'content_expDefinableFields',
            'content_expdefinablefields_value'=>'content_expDefinableFields_value',
            'content_expfiles'=>'content_expFiles',
            'content_expratings'=>'content_expRatings',
            'content_expsimplenote'=>'content_expSimpleNote',
            'content_exptags'=>'content_expTags',
            'expcats'=>'expCats',
            'expcomments'=>'expComments',
            'expdefinablefields'=>'expDefinableFields',
            'expexpealerts'=>'expeAlerts',
            'expexpealerts_temp'=>'expeAlerts_temp',
            'expfiles'=>'expFiles',
            'expratings'=>'expRatings',
            'exprss'=>'expRss',
            'expsimplenote'=>'expSimpleNote',
            'exptags'=>'expTags',
        );

        $renamed = 0;
        foreach ($tablenames as $oldtablename=>$newtablename) {
            if (!$db->tableExists($oldtablename)) {
                $db->sql('RENAME TABLE '.DB_TABLE_PREFIX.$oldtablename.' TO '.DB_TABLE_PREFIX.$newtablename);
                $renamed++;
            }
        }
        return $renamed.' '.gt('tables were correctly renamed.');
	}
}

?>
