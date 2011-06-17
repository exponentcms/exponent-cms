<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the clear_cache class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */

/**
 * This is the class clear_cache
 *
 * @subpackage Upgrade
 * @package Installation
 */
class clear_cache extends upgradescript {
	protected $from_version = '1.99.0';
//	protected $to_version = '1.99.2';

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	function name() { return "Clear the Caches"; }

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
		if (!defined('SYS_FILES')) include_once(BASE.'subsystems/files.php');
		$files = array(
			BASE.'tmp/mail',  // not used??  FIXME
			BASE.'tmp/pods',  // not used??  FIXME
			BASE.'tmp/css',  // exponent minified css cache
			BASE.'tmp/minify', // minify cache
			BASE.'tmp/pixidou', // (new) pixidou cache
		    BASE.'tmp/rsscache',  // magpierss cache
		    BASE.'tmp/views_c',  // smarty cache
		    BASE.'tmp/cache',  // alternate smarty cache
		);

        // delete the files.
        $removed = 0;
        $errors = 0;
		foreach ($files as $file) {
			$files = exponent_files_remove_files_in_directory($file);
			$removed += count($files['removed']);
			$errors += count($files['not_removed']);
		}
		
		// phpThumb cache includes subfolders
//		if (file_exists(BASE.'tmp/img_cache')) $this->cleardir_recursive(BASE.'tmp/img_cache');
		if (file_exists(BASE.'tmp/img_cache')) exponent_files_remove_files_in_directory(BASE.'tmp/img_cache');

		return "Caches were cleared.<br>".$errors." files could not be removed.";
	}

	/**
	 * recursively clear a directories contents, but leave the directory
	 * @param $dir
	 */
	function cleardir_recursive($dir) {
		$files = scandir($dir);
		array_shift($files);    // remove '.' from array
		array_shift($files);    // remove '..' from array
		foreach ($files as $file) {
			if (substr($file, 0, 1) != '.') {  // don't remove dot files
				$file = $dir . '/' . $file;
				if (is_dir($file)) {
					$this->cleardir_recursive($file);
					rmdir($file);
				} else {
					unlink($file);
				}
			}
		}
		// rmdir($dir);
	}
}

?>
