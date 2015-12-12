<?php

##################################################
#
# Copyright (c) 2004-2015 OIC Group, Inc.
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
 * This is the class clean_file_db
 */
class clean_file_db extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update file database by removing missing files and adding new files"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Due to various reasons, the file database may not accurately reflect the actual files on the server.  This script updates the database based on existing files."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // we'll just do it ine very instance instead of testing if user profile extensions are active
	}

	/**
	 * updates all expFiles to remove bad records, missing files, and adds all /files into db table
	 * @return bool
	 */
	function upgrade() {
	    global $db;

		$bad_count = 0;
        $new_count = 0;
		$rem_count = 0;

		// first remove files with incorrect/full directory in db (should only be relative to root
		foreach (expFile::selectAllFiles() as $file) {
			if (strpos($file->directory, BASE)) {
				$delfile = new expFile($file->id);
				$delfile->delete();
				$bad_count++;
			}
		}

		// next remove missing files found in db
		//FIXME should we check to see if they are being used in content_expFiles->expfiles_id??
		foreach (expFile::selectAllFiles() as $file) {
			if (!is_file(BASE . $file->directory . $file->filename)) {
				$delfile = new expFile($file->id);
				$delfile->delete();
				$rem_count++;
			}
		}

		// finally add existing files not in db
		$allfiles = expFile::listFlat(BASE.'files',true,null,array(),BASE);
		foreach ($allfiles as $path => $file) {
			if ($file[0] != '.') {
				$npath = preg_replace('/' . $file . '/', '', $path, 1);
				$dbfile = $db->selectObject('expFiles', "filename='" . $file . "' AND directory='" . $npath . "'");
				if (empty($dbfile)) {
					$newfile = new expFile(array('filename' => $file, 'directory' => $npath));
					$newfile->posted = $newfile->last_accessed = filemtime(BASE . $path);
					$newfile->save();
					$new_count++;
				}
			}
		}

		return ($bad_count?$bad_count:gt('No'))." ".gt("files with bad paths were removed").", ".($new_count?$new_count:gt('No')).' '.gt('files were added and.').' '.($rem_count?$rem_count:gt('No')).' '.gt('files were removed from the database.');
	}
}

?>
