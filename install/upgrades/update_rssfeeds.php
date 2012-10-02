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
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
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
	function description() { return "In v2.0.9, the RSS Feed feature was revised which requires additional entries in the database.  This Script updates those entries"; }

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

        $titlefixed = 0;
		// update each rss feed
	    $rssfeeds = $db->selectObjects('expRss',1);
	    foreach ($rssfeeds as $rssfeed) {
            if (empty($rssfeed->title) || empty($rssfeed->sef_url)) {
                if (empty($rssfeed->title)) $rssfeed->title = !empty($rssfeed->feed_title) ? $rssfeed->feed_title : '';
                if (empty($rssfeed->sef_url)) $rssfeed->sef_url = expCore::makeSefUrl($rssfeed->title,'expRss');
   		        $db->updateObject($rssfeed,'expRss');
                $titlefixed++;
            }
	    }
        // search for and create expRss items based on module configurations
        $feedconfigs = $db->selectObjects('expConfigs',"config LIKE '%s:10:\"enable_rss\";s:1:\"1\";%'");
        $fixed = 0;
        foreach ($feedconfigs as $feedconfig) {
            // create a new RSS object if enable is checked.
            $loc = expUnserialize($feedconfig->location_data);
            $config = expUnserialize($feedconfig->config);
        	$params['module'] = $loc->mod;
        	$params['src'] = $loc->src;
            $params['title'] = !empty($config['feed_title']) ? $config['feed_title'] : '';
            $params['sef_url'] = !empty($config['feed_sef_url']) ? $config['feed_sef_url'] : expCore::makeSefUrl($params['title'],'expRss');
            $params['feed_desc'] = $config['feed_desc'];
        	$params['enable_rss'] = $config['enable_rss'];
            $params['advertise'] = !empty($config['advertise']) ? $config['advertise'] : false;
        	$params['rss_limit'] = !empty($config['rss_limit']) ? $config['rss_limit'] : 24;
        	$params['rss_cachetime'] = !empty($config['rss_cachetime']) ? $config['rss_cachetime'] : 1440;
            if (!empty($config['itunes_cats'])) $params['itunes_cats'] = $config['itunes_cats'];
            $rssfeed = new expRss($params);
            $rssfeed->update($params);
            // backfill the rss sef_url into the module config
            if (empty($config['feed_sef_url'])) {
                $newconfig = new expConfig($loc);
                $newconfig->config['feed_sef_url'] = !empty($rssfeed->sef_url) ? $rssfeed->sef_url : $params['sef_url'];
                $newconfig->save();
            }
            $fixed++;
        }

        return ($titlefixed?$titlefixed:gt('No')).' '.gt('RSS Feed Titles were corrected and').' '.($fixed?$fixed:gt('No')).' '.gt('RSS Feeds were updated');
	}

}

?>
