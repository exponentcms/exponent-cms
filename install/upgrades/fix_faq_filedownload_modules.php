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
 * This is the class fix_faq_filedownload_modules
 */
class fix_faq_filedownload_modules extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.6';  // faq & filedownload names were changed in 2.0.6

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update faq and filedownloads modules with correct spelling"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.0.6, the faq and filedownload modules were plural in some cases and singular in others which prevented full integration.  This script updates existing tables."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        global $db;

		return ($db->tableExists('faqs') || $db->tableExists('filedownloads'));  // we'll just do it if the old tables exist
	}

	/**
	 * converts the faq & filedownload tables to the new naming scheme
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        if ($db->tableExists('faqs')) {
            if ($db->tableExists('faq') && !$db->countObjects('faq')) {
                $db->dropTable('faq');
            }
            if (!$db->tableExists('faq')) {
                $db->sql('RENAME TABLE '.DB_TABLE_PREFIX.'_faqs TO '.DB_TABLE_PREFIX.'_faq');
            }
            if ($db->tableExists('faqs') && !$db->countObjects('faqs')) {
                $db->dropTable('faqs');
            }
            // delete old faqs definition
            if (file_exists(BASE.'framework/modules/faq/definitions/faqs.php')) {
                unlink(BASE.'framework/modules/faq/definitions/faqs.php');
            }
        }
        if ($db->tableExists('filedownloads')) {
            if ($db->tableExists('filedownload') && !$db->countObjects('filedownload')) {
                $db->dropTable('filedownload');
            }
            if (!$db->tableExists('filedownload')) {
                $db->sql('RENAME TABLE '.DB_TABLE_PREFIX.'_filedownloads TO '.DB_TABLE_PREFIX.'_filedownload');
            }
            if ($db->tableExists('filedownloads') && !$db->countObjects('filedownloads')) {
                $db->dropTable('filedownloads');
            }
             // delete old filedownloads definition
            if (file_exists(BASE.'framework/modules/filedownloads/definitions/filedownloads.php')) {
                unlink(BASE.'framework/modules/filedownloads/definitions/filedownloads.php');
            }
        }
        return gt('faq & filedownload tables are now correctly named.');
	}
}

?>
