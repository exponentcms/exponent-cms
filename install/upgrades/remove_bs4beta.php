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
 * This is the class remove_bs4beta
 *
 * @package Installation
 * @subpackage Upgrade
 */
class remove_bs4beta extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.4.3';
    public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Remove beta Bootstrap 4 Sample Theme files"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Additional theme files used during the BS4 theme beta test were moved into the system and are no longer needed. This script removes those files."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        if (expUtil::isReallyWritable(BASE. 'themes/bootstrap4theme/framework')) {
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
            'themes/bootstrap4theme/controls',
            'themes/bootstrap4theme/framework',
            'themes/bootstrap4theme/modules',
            'themes/bootstrap4theme/plugins',
        );
        foreach ($olddirs as $dir) {
            if (expUtil::isReallyWritable(BASE . $dir)) {
                expFile::removeDirectory(BASE . $dir);
            }
        }

		return gt("Bootstrap 4 beta theme files removed.");
	}
}

?>
