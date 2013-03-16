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
 * Upgrade Script
 *
 * @package Installation
 * @subpackage Upgrade
 */

/**
 * This is the class clear_cache
 */
class clear_cache extends upgradescript {
	protected $from_version = '0.0.0';
//	protected $to_version = '1.99.2';
    public $priority = 95; // set this to a very low priority

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return gt("Clear the Caches"); }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;
	}

	/**
	 * cleans out all the cache folders
	 * @return bool
	 */
	function upgrade() {
		// work our way through all the tmp files and remove them
		$files = array(
			BASE.'tmp/css',  // exponent minified css cache
			BASE.'tmp/minify', // minify cache
			BASE.'tmp/pixidou', // (new) pixidou cache
		    BASE.'tmp/rsscache',  // SimplePie cache
		    BASE.'tmp/views_c',  // smarty compiler cache
		    BASE.'tmp/cache',  // smarty rendering cache (not currently active)
			BASE.'tmp/img_cache', // phpThumb cache includes subfolders
			BASE.'tmp/extensionuploads', // extensions are uploaded here, includes subfolders
		);

        // delete the files.
        $removed = 0;
        $errors = 0;
		foreach ($files as $file) {
			if (file_exists($file)) {
				$files = expFile::removeFilesInDirectory($file);
				$removed += count($files['removed']);
				$errors += count($files['not_removed']);
			}
		}
		return gt("All Caches were cleared.")."<br>".($errors?$errors:gt('No'))." ".gt("files could not be removed.");
	}

}

?>
