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
 * This is the class update_ealerts
 */
class update_ealerts extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.8';  // ealert subscriber table was changed in 2.0.8

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update eAlert subscribers table"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.0.8, the eAlert subscribers table was handled differently.  This script migrates the old table format to the new one."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

		return $db->countObjects('expeAlerts_subscribers');  // only needed if there if old subscriber table is populated
	}

	/**
	 * converts the old ealert subscriber table to the new format
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        if ($db->tableExists('expeAlerts_subscribers')) {
            if ($db->tableExists('user_subscriptions') && !$db->countObjects('user_subscriptions')) {
                $db->dropTable('user_subscriptions');
            }
            if (!$db->tableExists('user_subscriptions')) {
                $db->sql('RENAME TABLE '.DB_TABLE_PREFIX.'_expeAlerts_subscribers TO '.DB_TABLE_PREFIX.'_user_subscriptions');
            }
            if ($db->tableExists('expeAlerts_subscribers') && !$db->countObjects('expeAlerts_subscribers')) {
                $db->dropTable('expeAlerts_subscribers');
            }
        }
        return gt('E-Alert subscribers were migrated.');
	}
}

?>
