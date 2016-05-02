<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * This is the class change_permissions2
 */
class change_permissions2 extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.2.1';
    public $optional = true;
    public $priority = 81; // set this to a very low priority after change_permissions

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "(Alternate) Update/secure all file and folder permissions (allow group write permissions)"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In some instances the folder and file permissions are too permissive, yet world/group read=only is too restrictive. This alternate script changes all folder/file permissions to world read-only, and no execute (except /cgi-bin)!"; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        return true; // we will always run
	}

    /**
     * function will chmod dirs and files recursively
     * @param type $start_dir
     */
    function chmod_recursive($start_dir, $dir_perms = 0755, $file_perms = 0644) {
        if (is_dir($start_dir)) {
            $fh = opendir($start_dir);
            while (($file = readdir($fh)) !== false) {
                // skip hidden files and dirs and recursing if necessary
                if (strpos($file, '.')=== 0) continue;

                $filepath = $start_dir . '/' . $file;
                if ( is_dir($filepath) ) {
                    if ($file == 'cgi-bin')
                        break;
                    chmod($filepath, $dir_perms);
                    self::chmod_recursive($filepath);
                } else {
                    chmod($filepath, $file_perms);
                }
            }
            closedir($fh);
        }
    }

	/**
	 * Searches for and updates file/folder permissions globally
     *
	 * @return bool
	 */
	function upgrade() {
        self::chmod_recursive(BASE, 0775, 0664);
        return (gt('All Folder and File permissions were changed to world read-only.'));
	}

}

?>
