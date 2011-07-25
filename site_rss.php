<?php
##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

if (!defined("EXPONENT")) require_once('exponent.php');
//if (!defined('SYS_PODCASTING')) include_once('core_podcast.php');
//if (!defined('SYS_RSS')) define('SYS_RSS', 1); // This is an ugly way of getting around accidentally loading core_rss.php
											   // What can we do to minimize code duplication and make this a lot better?
											   // How about this?
//if (!defined('SYS_RSS')) include_once('core_rss.php');
if (!defined('SYS_RSS')) require_once(BASE.'external/feedcreator.class.php');

$site_rss = new expRss($_REQUEST);
$site_rss->feed_title = empty($site_rss->feed_title) ? 'RSS for '.URL_FULL : $site_rss->feed_title;
$site_rss->feed_desc = empty($site_rss->feed_desc) ? 'This is the site wide RSS syndication for '.HOSTNAME : $site_rss->feed_desc;
if (isset($site_rss->rss_cachetime)) { $ttl = $site_rss->rss_cachetime; }
if ($ttl == 0) { $ttl = 1440; }

// $ic = explode(";", $config->config['itunes_cats']);
// $x = 0;
// foreach($ic as $cat){
	// $cat_sub = explode(":", $cat);
	// $itunes_cats[$x]->category = $cat_sub[0];
	// if(isset($cat_sub[1])) {
		// $itunes_cats[$x]->subcategory = $cat_sub[1];	    
	// }
	// $x++;
// }

if ($site_rss->enable_rss == true) {
	$rss = new UniversalFeedCreator();
	$rss->cssStyleSheet = "";
//	$rss->useCached("PODCAST");
	$rss->useCached();
	$rss->title = $site_rss->feed_title;
	$rss->description = $site_rss->feed_desc;
	$rss->ttl = $ttl;
	$rss->link = "http://".HOSTNAME.PATH_RELATIVE;
	$rss->syndicationURL = "http://".HOSTNAME.PATH_RELATIVE.$_SERVER['PHP_SELF'];
	if ($_REQUEST['module'] == "filedownload") {
		$rss->itunes->summary = $site_rss->feed_desc;
		$rss->itunes->author = ORGANIZATION_NAME;
//		$rss->itunes->category = @$itunes_cats[0]->category;
//		$rss->itunes->subcategory = @$itunes_cats[0]->subcategory;
		$rss->itunes->category = '';
		$rss->itunes->subcategory = '';
		$rss->itunes->image = URL_FULL."framework/modules/filedownloads/assets/images/logo.png";
		$rss->itunes->explicit = 0;
		$rss->itunes->subtitle = 0;
		$rss->itunes->keywords = 0;
		$rss->itunes->owner_email = 0;
	}

	$pubDate = '';
	foreach ($site_rss->getFeedItems() as $item) {
		if ($item->date > $pubDate) { $pubDate = $item->date; }
		$rss->addItem($item);
	}
	if (isset($site_rss->rss_limit) && $site_rss->rss_limit > 0) {
		$rss->items = array_slice($rss->items, 0, $site_rss->rss_limit);
	}
	$rss->pubDate = $pubDate;

	header("Content-type: text/xml");
	if ($_REQUEST['module'] == "filedownload") {
		echo $rss->createFeed("PODCAST");
	} else {
		echo $rss->createFeed("RSS2.0");
	}
} else {
	echo "This RSS feed has been disabled.";
}

?>
