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
 * This is the class clean_file_db
 */
class clean_file_db extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	public $optional = true;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update file database by updating info, removing missing files and adding new files"; }

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
        $rem_count = 0;
        $attach_count = 0;
        $new_count = 0;
        $chg_count = 0;

		// first remove files with incorrect/full directory in db (should only be relative to root)
		foreach (expFile::selectAllFiles() as $file) {
			if (strpos($file->directory, BASE)) {
				$delfile = new expFile($file->id);
				$delfile->delete();
				$bad_count++;
			}
		}

		// next remove missing files still in db
        foreach (expFile::selectAllFiles() as $file) {
            if (!is_file(BASE . $file->directory . $file->filename)) {
                $delfile = new expFile($file->id);
                $delfile->delete();
                $rem_count++;
            }
        }

        // next remove missing attachment references in db
        foreach ($db->selectObjects('content_expFiles') as $ifile) {
            $incfile = new expFile($ifile->expfiles_id);
            if (empty($incfile)) {  // attachment doesn't exist
                if ($db->countObjects('content_expFiles', "expfiles_id='" . $incfile->expfiles_id . "'")) {
                    $attach_count += $db->countObjects('content_expFiles', "expfiles_id='" .  $file->id . "'");
                    $db->delete('content_expFiles', "expfiles_id='" .  $file->id . "'"); // remove missing attachments
                }
            }
        }

		// finally add existing files not in db and update existing files
		$allfiles = expFile::listFlat(BASE.'files',true,null,array(),BASE);
		foreach ($allfiles as $path => $file) {
			if ($file[0] != '.') {
			    $nfile = str_replace(array('(',')'),array('\(','\)'),$file);
				$npath = preg_replace('/' . $nfile . '/', '', $path, 1);  //fixme doesn't account for regex characters like (1)
				$dbfile = $db->selectObject('expFiles', "filename='" . $nfile . "' AND directory='" . $npath . "'");
				if (empty($dbfile)) {
					$newfile = new expFile(array('filename' => $file, 'directory' => $npath));
					$newfile->posted = $newfile->last_accessed = filemtime(BASE . $path);
					$newfile->save();
					$new_count++;
				} else {
                    $changed = false;
                    $file = new expFile($dbfile->id);
				    // update filesize, mimetype, and image size
                    $_fileInfo = expFile::getImageInfo(BASE . $file->directory . $file->filename);
                    $file->is_image = !empty($_fileInfo['is_image']) ? $_fileInfo['is_image'] : false;
                    // check/update fule size
                    if (!empty($_fileInfo['fileSize']) && $file->filesize != $_fileInfo['fileSize']) {
                        $file->filesize = !empty($_fileInfo['fileSize']) ? $_fileInfo['fileSize'] : 0;
                        $changed = true;
                    }
                    // check/update mime type
                    if (!empty($_fileInfo['mime']) && $file->mimetype != $_fileInfo['mime']) {
                        $file->mimetype = $_fileInfo['mime'];
                        $changed = true;
                    }
                    // check/update image dimensions
                    if (!empty($_fileInfo['is_image']) && ($file->image_width != $_fileInfo[0] || $file->image_height != $_fileInfo[1])) {
                        $file->image_width = $_fileInfo[0];
                        $file->image_height = $_fileInfo[1];
                        $changed = true;
                    }
                    if ($changed) {
                        $file->update();
                        $chg_count++;
                    }
                }
			}
		}

		return ($new_count?$new_count:gt('No')).' '.gt('files were added').', '.($chg_count?$chg_count:gt('No')).' '.gt('files were updated').', '.($attach_count?$attach_count:gt('No')).' '.gt('missing attachments were removed').', '.($bad_count?$bad_count:gt('No'))." ".gt("files with bad paths were removed").", ".gt('and').' '.($rem_count?$rem_count:gt('No')).' '.gt('missing files were removed from the database.');
	}
}

?>
