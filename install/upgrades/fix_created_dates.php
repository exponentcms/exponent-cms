<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * This is the class fix_created_dates
 *
 * @package Installation
 * @subpackage Upgrade
 */
class fix_created_dates extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.7.0';  // created dates were broken in 2.6.0 through patch 2

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Update Blog, File Download and News posts with a valid create and edited date"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.6.0, the created and edited date stamps were not set.  This script updates existing posts."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
    function needed() {
        global $db;

        $count = 0;
        foreach (array('blog', 'filedownload', 'news') as $table) {
            $count += $db->countObjects($table, "created_at = 0");
        }
        return ($count > 0);  // we'll only do it there are bad records
    }

	/**
	 * checks all blog, filedownload and news items to populate 'created_at' and 'edited_at' fields
	 * @return string
	 */
	function upgrade() {
	    global $db;

        $count = 0;
        foreach (array('blog', 'filedownload', 'news') as $table) {
            foreach ($db->selectObjects($table, "created_at = 0") as $post) {
                if ($post->publish != 0) {
                    $post->created_at = $post->publish;
                } elseif($post->edited_at != 0) {
                    $post->created_at = $post->edited_at;
                } else {
                    $post->created_at = time();
                }
                if ($post->edited_at == 0) {  // in case it's not yet set
                    $post->edited_at = $post->created_at;
                }
                $db->updateObject($post, $table);
                $count++;
            }
        }

        return ($count?$count:gt('No')).' '.gt('old posts had their created date set.');
	}
}

?>
