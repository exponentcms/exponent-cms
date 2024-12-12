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
 * This is the class remove_bs5beta
 *
 * @package Installation
 * @subpackage Upgrade
 */
class remove_bs5beta extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.4.3';
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Remove beta Bootstrap 5 Sample Theme files"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Additional files used during the BS5 theme beta test were moved into the system and are no longer needed. This script removes those files."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
          if (file_exists(BASE.'themes/bootstrap4theme/js/tempusdominus-bootstrap-4.js') ||
              file_exists(BASE.'themes/bootstrap5theme/js/tempusdominus-bootstrap-4.js') ||
              file_exists(BASE.'themes/bootstrap5theme/js/bootbox.all.js')) {
            return true;
        }
        return false;
	}

	/**
	 * removed theme beta files
	 * @return string
	 */
	function upgrade() {
        $olddirs = array(
            'themes/bootstrap4theme',
            'themes/bootstrap5theme',
        );
        $oldfiles = array(
            'tempusdominus-bootstrap-4.js',
            'tempusdominus-bootstrap-4.scss',
            '_tempusdominus-bootstrap-4.scss',
            'bootbox.all.js',
        );
        $files_removed = 0;
        foreach ($olddirs as $dir) {
            foreach ($oldfiles as $file) {
                if (file_exists(BASE . $dir . '/js/' . $file)) {
                    if (unlink(BASE . $dir . '/js/' . $file)) $files_removed++;
                }
            }
        }

        return ($files_removed ? : gt('No')) . " " . gt("Bootstrap-5 beta theme files removed.") . " " .
            gt("You may also want to remove any similar files in your custom Bootstrap 4 or Boostrap 5 theme within the 'theme/js' folder which are named 'tempusdominus-bootstrap-4'.");
	}
}

?>
