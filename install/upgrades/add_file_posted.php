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
 * This is the class add_file_posted
 */
class add_file_posted extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.1.1';  // file posted dates began to be set in 2.1.1

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update files with valid posted date and mimetype"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.1.1, uploaded files date stamps were not set, but we can now sort by date.  And HTML5 recognizes new mimetypes.  This script updates existing files."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // we'll just do it ine very instance instead of testing if user profile extensions are active
	}

	/**
	 * updates all expFiles to populate 'posted' field
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $count = 0;
        $_types = array(
            'jpg'=>'image/jpeg',
            'jpeg'=>'image/jpeg',
            'gif'=>'image/gif',
            'png'=>'image/png',
            'mp3'=>'audio/mpeg',
            'ogg'=>'audio/ogg',
            'flv'=>'video/x-flv',
            'f4v'=>'video/mp4',
            'mp4'=>'video/mp4',
            'ogv'=>'video/ogg',
            '3gp'=>'video/3gpp',
            'webm'=>'video/webm',
            'pdf'=>'application/pdf',
        );
        foreach ($db->selectObjects('expFiles') as $file) {
            if (empty($file->posted) || empty($file->mimetype)) {
                if (empty($file->posted)) {
                    if (file_exists(BASE . $file->directory . $file->filename)) {
                        $file->posted = $file->last_accessed = filemtime(BASE . $file->directory . $file->filename);
                    } else {
                        $file->posted = $file->last_accessed = time();
                    }
                }
                if (empty($file->mimetype)) {
                    $_fileData = pathinfo(BASE . $file->directory . $file->filename);
                    if (array_key_exists($_fileData['extension'],$_types)) $file->mimetype = $_types[$_fileData['extension']];
                }
                $db->updateObject($file,'expFiles');
                $count++;
            }
	    }

        return ($count?$count:gt('No')).' '.gt('files had their posted date or mimetype set.');
	}
}

?>
