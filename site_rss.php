<?php
##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
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

if (!defined("EXPONENT")) include_once('exponent.php');
if (!defined('SYS_RSS')) include_once('core_rss.php');

$site_rss = new expRss($_REQUEST);

//FIXME: 
// We need to add a site wide config to turn RSS on/off
// and a site wide title & description as well..i'm hardcoding 
// these for now.
$config->enable_rss = true;
$config->feed_title = empty($site_rss->feed_title) ? 'RSS for '.URL_FULL : $site_rss->feed_title;
$config->feed_desc = empty($site_rss->feed_desc) ? 'This is the site wide RSS syndication for '.HOSTNAME : $site_rss->feed_desc;
$ttl = $config->rss_cachetime;
if ($ttl == 0) { $ttl = 24; }

if ($config->enable_rss == true) {
	$rss = new UniversalFeedCreator();
	$rss->cssStyleSheet = "";
	$rss->useCached();
	$rss->title = $config->feed_title;
	$rss->description = $config->feed_desc;
	$rss->ttl = $ttl;
	$rss->link = "http://".HOSTNAME.PATH_RELATIVE;
	$rss->syndicationURL = "http://".HOSTNAME.PATH_RELATIVE.$_SERVER['PHP_SELF'];	

	foreach ($site_rss->getFeedItems() as $item) {
		$rss->addItem($item);
	}

	header("Content-type: application/xml;");
	echo $rss->createFeed("RSS2.0");
} else {
	echo "This RSS feed has been disabled.";
}

?>
