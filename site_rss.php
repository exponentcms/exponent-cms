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
/** @define "BASE" "." */

require_once('exponent.php');

// for backwards compatibility, use the new method
redirect_to(array('controller'=>'rss','action'=>'feed','module'=>$_REQUEST['module'],'src'=>$_REQUEST['src']));

//FIXME this is the old, deprecated method
require_once(BASE.'external/feedcreator.class.php');

$site_rss = new expRss($_REQUEST);
//$site_rss->feed_title = empty($site_rss->feed_title) ? gt('RSS for').' '.URL_FULL : $site_rss->feed_title;
$site_rss->title = empty($site_rss->title) ? gt('RSS for').' '.URL_FULL : $site_rss->title;
$site_rss->feed_desc = empty($site_rss->feed_desc) ? gt('This is the site wide RSS syndication for').' '.HOSTNAME : $site_rss->feed_desc;
if (isset($site_rss->rss_cachetime)) { $ttl = $site_rss->rss_cachetime; }
if ($site_rss->rss_cachetime == 0) { $site_rss->rss_cachetime = 1440; }

if (!empty($site_rss->itunes_cats)) {
    $ic = explode(";", $site_rss->itunes_cats);
    $x = 0;
    $itunes_cats = array();
    foreach($ic as $cat){
        $cat_sub = explode(":", $cat);
        $itunes_cats[$x]->category = $cat_sub[0];
        if(isset($cat_sub[1])) {
            $itunes_cats[$x]->subcategory = $cat_sub[1];
        }
        $x++;
    }
}

if ($site_rss->enable_rss == true) {
	$rss = new UniversalFeedCreator();
	$rss->cssStyleSheet = "";
//	$rss->useCached("PODCAST");
	$rss->useCached();
//	$rss->title = $site_rss->feed_title;
    $rss->title = $site_rss->title;
	$rss->description = $site_rss->feed_desc;
    $rss->image->url = URL_FULL.'themes/'.DISPLAY_THEME.'/images/logo.png';
//    $rss->image->title = $site_rss->feed_title;
    $rss->image->title = $site_rss->title;
    $rss->image->link = URL_FULL;
//    $rss->image->width = 64;
//    $rss->image->height = 64;
	$rss->ttl = $site_rss->rss_cachetime;
	$rss->link = "http://".HOSTNAME.PATH_RELATIVE;
	$rss->syndicationURL = "http://".HOSTNAME.$_SERVER['PHP_SELF'].'?module='.$site_rss->module.'&src='.$site_rss->src;
	if ($_REQUEST['module'] == "filedownload") {
//		$rss->itunes->summary = $site_rss->feed_desc;
		$rss->itunes->author = ORGANIZATION_NAME;
        if (!empty($itunes_cats)) {
            $rss->itunes->category = $itunes_cats[0]->category;
            $rss->itunes->subcategory = $itunes_cats[0]->subcategory;
        }
		$rss->itunes->image = URL_FULL.'themes/'.DISPLAY_THEME.'/images/logo.png';
//		$rss->itunes->explicit = 0;
//		$rss->itunes->subtitle = $site_rss->feed_title;
        $rss->itunes->subtitle = $site_rss->title;
//		$rss->itunes->keywords = 0;
		$rss->itunes->owner_email = SMTP_FROMADDRESS;
        $rss->itunes->owner_name = ORGANIZATION_NAME;
	}

	$pubDate = '';
	foreach ($site_rss->getFeedItems() as $item) {
		if ($item->date > $pubDate) { $pubDate = $item->date; }
		$rss->addItem($item);
	}
	if (!empty($site_rss->rss_limit)) {
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
	echo gt("This RSS feed is not available.");
}

?>