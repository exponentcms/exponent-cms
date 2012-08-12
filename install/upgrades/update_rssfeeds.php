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
 * This is the class update_rssfeeds
 */
class update_rssfeeds extends upgradescript {
	protected $from_version = '1.99.0';  // version number lower than first released version, 2.0.0
	protected $to_version = '2.0.9';  // code was changed in 2.0.9

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Updates RSS Feeds in expRss to new format"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In v2.0.9, the RSS Feed feature was revised and required additional entries in the database.  This Script updates those entries"; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
		return true;  // we'll just do it ine very instance instead of testing if user profile extensions are active
	}

	/**
	 * updates new title and sef_url properties/fields
	 * @return bool
	 */
	function upgrade() {
	    global $db;

        $fixed = 0;
		// update each rss feed
	    $rssfeeds = $db->selectObjects('expRss',1);
	    foreach ($rssfeeds as $rssfeed) {
            if (empty($rssfeed->title) || empty($rssfeed->sef_url)) {
                if (empty($rssfeed->title)) $rssfeed->title = !empty($rssfeed->feed_title) ? $rssfeed->feed_title : '';
                if (empty($rssfeed->sef_url)) $rssfeed->sef_url = self::makeSefUrl($rssfeed->title);
   		        $db->updateObject($rssfeed,'expRss');
                $fixed =+1 ;
            }
	    }
        return ($fixed?$fixed:gt('No')).' '.gt('RSS Feeds were updated');
	}

    /**
   	 * make an sef_url for expRss
   	 */
    function makeSefUrl($title) {
        global $db, $router;

        if (!empty($title)) {
            $sef_url = $router->encode($title);
        } else {
            $sef_url = $router->encode('Untitled');
        }
        $dupe = $db->selectValue('expRss', 'sef_url', 'sef_url="'.$sef_url.'"');
        if (!empty($dupe)) {
            list($u, $s) = explode(' ',microtime());
            $sef_url .= '-'.$s.'-'.$u;
        }
        return $sef_url;
    }

}

?>
