<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * This is the class update_profile_paths
 */
class add_blog_published extends upgradescript {
	protected $from_version = '1.99.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.6';  // publish/unpublish dates were added in 2.0.6

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	function name() { return "Update blog posts with valid publish date"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "Prior to v2.0.6, blog post date stamps were the created date, but now have a publish/unpublish date like news.  This script updates existing blog posts."; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // we'll just do it ine very instance instead of testing if user profile extensions are active
	}

	/**
	 * coverts all headline modules/items into text modules/items and deletes headline controller files
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $count = 0;
        foreach ($db->selectObjects('blog') as $post) {
            if (empty($post->publish)) {
                $post->publish = $post->created_at;
                $db->updateObject($post,'blog');
                $count++;
            }
	    }

        return $count.' '.gt('old blog posts had their publish date set to their created date.');
	}
}

?>
